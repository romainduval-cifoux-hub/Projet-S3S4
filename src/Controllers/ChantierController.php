<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database/db.php';
require_once __DIR__ . '/../Database/chantierRepository.php';

class ChantierController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS);
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

        $salaries = ch_getSalaries($this->pdo);
        $clients  = ch_getClients($this->pdo);

        $errors = [];

        // Valeurs par défaut du formulaire
        if ($mode === 'edit' && $creneau) {
            $date_jour  = $creneau['date_jour'];
            $id_salarie = (int)$creneau['id_salarie'];
            $commentaire = $creneau['commentaire'] ?? '';
            // déduire la période à partir des heures
            if ($creneau['heure_debut'] === '08:00:00' && $creneau['heure_fin'] === '12:00:00') {
                $periode = 'am';
            } elseif ($creneau['heure_debut'] === '13:00:00' && $creneau['heure_fin'] === '17:00:00') {
                $periode = 'pm';
            } else {
                $periode = 'am'; // fallback
            }
        } else {
            $date_jour   = date('Y-m-d');
            $id_salarie  = 0;
            $commentaire = '';
            $periode     = 'am';
        }

        $id_client = null; // pour l’instant on ne le stocke pas en BDD, on ne peut pas le pré-remplir

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date_jour   = $_POST['date_jour'] ?? '';
            $periode     = $_POST['periode'] ?? 'am';
            $id_salarie  = (int)($_POST['id_salarie'] ?? 0);
            $id_client   = !empty($_POST['id_client']) ? (int)$_POST['id_client'] : null;
            $commentaire = trim($_POST['commentaire'] ?? '');

            // Validations communes
            if (!$date_jour) {
                $errors[] = "La date est obligatoire.";
            }
            if ($id_salarie <= 0) {
                $errors[] = "Veuillez sélectionner un salarié.";
            }
            if (!in_array($periode, ['am','pm'], true)) {
                $errors[] = "Période invalide.";
            }

            // Vérifier la limite de 2 demi-journées + doublon sur la même période
            if (empty($errors)) {
                $existing = ch_getCreneauxJour($this->pdo, $id_salarie, $date_jour);

                // En mode édition, on exclut le créneau lui-même de la liste pour le contrôle
                if ($mode === 'edit' && $creneau) {
                    $existing = array_filter($existing, function($slot) use ($creneau) {
                        return $slot['id_creneau'] != $creneau['id_creneau'];
                    });
                }

                if (count($existing) >= 2) {
                    $errors[] = "Ce salarié est déjà planifié sur la journée complète (2 demi-journées).";
                } else {
                    $hDeb = ($periode === 'am') ? '08:00:00' : '13:00:00';
                    $hFin = ($periode === 'am') ? '12:00:00' : '17:00:00';

                    foreach ($existing as $slot) {
                        if ($slot['heure_debut'] === $hDeb && $slot['heure_fin'] === $hFin) {
                            $errors[] = "Ce salarié a déjà un créneau sur cette demi-journée.";
                            break;
                        }
                    }
                }
            }

            // Si tout est OK → création ou update
            if (empty($errors)) {
                if ($mode === 'create') {
                    $ok = ch_createCreneauAvecPlanning(
                        $this->pdo,
                        $managerId,
                        $id_salarie,
                        $date_jour,
                        $periode,
                        $id_client,
                        $commentaire
                    );
                } else {
                    $ok = ch_updateCreneau(
                        $this->pdo,
                        (int)$creneau['id_creneau'],
                        $id_salarie,
                        $date_jour,
                        $periode,
                        $id_client,
                        $commentaire
                    );
                }

                if ($ok) {
                    header('Location: ' . BASE_URL . '/public/index.php?page=chef/planning&date=' . $date_jour);
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
        require __DIR__ . '/../Views/chef/planning/crudcreneau.php';
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

