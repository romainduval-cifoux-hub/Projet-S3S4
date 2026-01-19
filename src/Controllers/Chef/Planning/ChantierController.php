<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/chantierRepository.php';

class ChantierController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkAdmin(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès réservé au chef d'entreprise.");
        }
    }

    public function create(): void
    {
        $this->checkAdmin();
        $this->handleForm('create');
    }

    public function edit(): void
    {
        $this->checkAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit("ID de créneau invalide.");
        }

        $creneau = ch_getCreneauById($this->pdo, $id);
        if (!$creneau) {
            http_response_code(404);
            exit("Créneau introuvable.");
        }

        $this->handleForm('edit', $creneau);
    }

    /**
     * Gère à la fois la création et la modification
     * $mode = 'create' ou 'edit'
     * $creneau = données existantes en mode édition
     */
    private function handleForm(string $mode, ?array $creneau = null): void
    {
        $managerId = $_SESSION['user_id'] ?? null;
        if (!$managerId) {
            http_response_code(500);
            exit("Manager non identifié dans la session.");
        }

        // recherche employé pour limiter la liste
        $searchEmp = trim($_GET['emp'] ?? '');
        $salaries  = ch_getSalaries($this->pdo, $searchEmp);

        $searchClient = trim($_GET['cli'] ?? '');
        $clients  = ch_getClients($this->pdo, $searchClient);

        $errors = [];

        // Valeurs par défaut du formulaire
        if ($mode === 'edit' && $creneau) {
            $date_jour   = $creneau['date_jour'];
            $date_debut  = $date_jour;
            $date_fin    = $date_jour;
            $id_salarie  = (int)$creneau['id_salarie'];
            $commentaire = $creneau['commentaire'] ?? '';

            if ($creneau['heure_debut'] === '08:00:00' && $creneau['heure_fin'] === '12:00:00') {
                $periode = 'am';
            } elseif ($creneau['heure_debut'] === '13:00:00' && $creneau['heure_fin'] === '17:00:00') {
                $periode = 'pm';
            } else {
                $periode = 'am';
            }
        } else {
            // MODE CRÉATION
            $date_debut  = date('Y-m-d');
            $date_fin    = $date_debut;
            $date_jour   = $date_debut;
            $id_salarie  = 0;
            $commentaire = '';
            $periode     = 'am';
        }

        $id_client = null; 

        $dispoMap = null;

        // Disponibilités
        if ($mode === 'create' && isset($_GET['dispo']) && $_GET['dispo'] === '1') {

            $date_debut = $_GET['date_debut'] ?? $date_debut;
            $date_fin   = $_GET['date_fin'] ?? $date_debut;
            if ($date_fin === '') $date_fin = $date_debut;

            $periode = $_GET['periode'] ?? $periode;
            if (!in_array($periode, ['am','pm','full'], true)) $periode = 'am';

            // IMPORTANT : on ne calcule que si la date_debut est valide
            if ($date_debut !== '') {
                $dispoMap = ch_buildDispoMap($this->pdo, $salaries, $date_debut, $date_fin, $periode);
            }
        }



    
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            if ($mode === 'edit') {
                // édition : un seul jour
                $date_jour   = $_POST['date_jour'] ?? '';
                $date_debut  = $date_jour;
                $date_fin    = $date_jour;
            } else {
                // création : période
                $date_debut  = $_POST['date_debut'] ?? '';
                $date_fin    = $_POST['date_fin'] ?? '';
                if (!$date_fin) {
                    $date_fin = $date_debut; // si vide un seul jour
                }
                // pour la redirection après succès
                $date_jour = $date_debut;
            }

            



            $periode     = $_POST['periode'] ?? 'am';
            $id_salarie  = (int)($_POST['id_salarie'] ?? 0);
            $id_client   = !empty($_POST['id_client']) ? (int)$_POST['id_client'] : null;
            $commentaire = trim($_POST['commentaire'] ?? '');

            $justCheck = isset($_POST['check_dispo']) && $_POST['check_dispo'] == '1';

            if ($justCheck) {
                // on calcule la dispo et on affiche la page, SANS créer
                $dispoMap = ch_buildDispoMap($this->pdo, $salaries, $date_debut, $date_fin, $periode);
                require __DIR__ . '/../../../Views/chef/planning/crudcreneau.php';
                return;
            }

            if ($mode === 'create' && $dispoMap === null) {
                $dispoMap = ch_buildDispoMap($this->pdo, $salaries, $date_debut, $date_fin, $periode);
            }

            // Validations communes
            if (!$date_debut) {
                $errors[] = "La date de début est obligatoire.";
            }
            if ($date_fin && $date_fin < $date_debut) {
                $errors[] = "La date de fin doit être postérieure ou égale à la date de début.";
            }
            if ($id_salarie <= 0) {
                $errors[] = "Veuillez sélectionner un salarié.";
            }
            if (!in_array($periode, ['am','pm','full'], true)) {
                $errors[] = "Période invalide.";
            }
            if ($mode === 'edit' && $periode === 'full') {
                $errors[] = "En modification, choisissez Matin ou Après-midi (la journée entière n'est disponible qu'à la création).";
            }

            $joursSelectionnes = [];
            if (empty($errors)) {
                $tsDebut = strtotime($date_debut);
                $tsFin   = strtotime($date_fin);

                if ($tsDebut === false || $tsFin === false) {
                    $errors[] = "Dates invalides.";
                } elseif ($tsFin < $tsDebut) {
                    $errors[] = "La date de fin doit être postérieure ou égale à la date de début.";
                } else {
                    for ($ts = $tsDebut; $ts <= $tsFin; $ts += 86400) {
                        // N = 1 (lundi) à 7 (dimanche)
                        $dayOfWeek = (int)date('N', $ts);

                        // On garde seulement lundi (1) à vendredi (5)
                        if ($dayOfWeek <= 5) {
                            $joursSelectionnes[] = date('Y-m-d', $ts);
                        }
                    }
                }
            }

            if (empty($joursSelectionnes)) {
                $errors[] = "La période sélectionnée ne contient aucun jour ouvré (lundi à vendredi).";
            }

            // Vérifier limites pour chaque jour
            if (empty($errors)) {
                foreach ($joursSelectionnes as $jour) {
                    $existing = ch_getCreneauxJour($this->pdo, $id_salarie, $jour);

                    // En mode édition, on exclut le créneau lui-même de la liste
                    if ($mode === 'edit' && $creneau && $jour === $creneau['date_jour']) {
                        $existing = array_filter($existing, function($slot) use ($creneau) {
                            return (int)$slot['id_creneau'] !== (int)$creneau['id_creneau'];
                        });
                    }

                    // Si on demande une journée entière
                    if ($periode === 'full') {
                        // 1) déjà 2 demi-journées ce jour-là : impossible
                        if (count($existing) >= 2) {
                            $errors[] = "Le $jour : ce salarié est déjà planifié sur la journée complète (2 demi-journées).";
                            break;
                        }

                        // 2) une des deux demi-journées déjà prise : impossible
                        $matinPris = false;
                        $apremPris = false;

                        foreach ($existing as $slot) {
                            if ($slot['heure_debut'] === '08:00:00' && $slot['heure_fin'] === '12:00:00') {
                                $matinPris = true;
                            }
                            if ($slot['heure_debut'] === '13:00:00' && $slot['heure_fin'] === '17:00:00') {
                                $apremPris = true;
                            }
                        }

                        if ($matinPris || $apremPris) {
                            $errors[] = "Le $jour : ce salarié a déjà une demi-journée planifiée, impossible de réserver la journée entière.";
                            break;
                        }
                    }
                    // Sinon : logique normale am/pm
                    else {
                        $hDeb = ($periode === 'am') ? '08:00:00' : '13:00:00';
                        $hFin = ($periode === 'am') ? '12:00:00' : '17:00:00';

                        if (count($existing) >= 2) {
                            $errors[] = "Le $jour : ce salarié est déjà planifié sur la journée complète (2 demi-journées).";
                            break;
                        }

                        foreach ($existing as $slot) {
                            if ($slot['heure_debut'] === $hDeb && $slot['heure_fin'] === $hFin) {
                                $errors[] = "Le $jour : ce salarié a déjà un créneau sur cette demi-journée.";
                                break 2;
                            }
                        }
                    }
                }
            }

            // Si tout est OK, création ou update
            if (empty($errors)) {
                if ($mode === 'create') {
                    $okGlobal = true;

                    foreach ($joursSelectionnes as $jour) {

                        if ($periode === 'full') {
                            // Matin
                            $ok1 = ch_createCreneauAvecPlanning(
                                $this->pdo,
                                $managerId,
                                $id_salarie,
                                $jour,
                                'am',
                                $id_client,
                                $commentaire
                            );
                            // Après-midi
                            $ok2 = ch_createCreneauAvecPlanning(
                                $this->pdo,
                                $managerId,
                                $id_salarie,
                                $jour,
                                'pm',
                                $id_client,
                                $commentaire
                            );

                            if (!$ok1 || !$ok2) {
                                $okGlobal = false;
                                break;
                            }
                        } else {
                            // cas simple : matin OU après-midi
                            $ok = ch_createCreneauAvecPlanning(
                                $this->pdo,
                                $managerId,
                                $id_salarie,
                                $jour,
                                $periode,
                                $id_client,
                                $commentaire
                            );

                            if (!$ok) {
                                $okGlobal = false;
                                break;
                            }
                        }
                    }
                } else {
                    // édition : un seul jour, et on ne gère pas "full" ici
                    $okGlobal = ch_updateCreneau(
                        $this->pdo,
                        (int)$creneau['id_creneau'],
                        $id_salarie,
                        $date_jour,
                        $periode,
                        $id_client,
                        $commentaire
                    );
                }

                if ($okGlobal) {
                    header('Location: ' . BASE_URL . '/public/index.php?page=chef/planning&date=' . $date_debut);
                    exit;
                } else {
                    $errors[] = "Erreur lors de l'enregistrement du créneau.";
                }
            }
        }

        // Variables pour la vue
        $pageTitle = ($mode === 'create')
            ? "Nouveau chantier – Team Jardin"
            : "Modifier le chantier – Team Jardin";

        $formTitle = ($mode === 'create')
            ? "Nouveau chantier (créneau demi-journée)"
            : "Modifier le créneau";

        $actionUrl = ($mode === 'create')
            ? BASE_URL . '/public/index.php?page=chantier/create'
            : BASE_URL . '/public/index.php?page=chantier/edit&id=' . (int)$creneau['id_creneau'];

        
        // on réutilise la même vue pour create + edit
        require __DIR__ . '/../../../Views/chef/planning/crudcreneau.php';
    }

    public function delete(): void
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id_creneau'] ?? 0);
            if ($id > 0) {
                ch_deleteCreneau($this->pdo, $id);
            }
        }

        $date = $_GET['date'] ?? date('Y-m-d');
        header('Location: ' . BASE_URL . '/public/index.php?page=chef/planning&date=' . $date);
        exit;
    }
}

