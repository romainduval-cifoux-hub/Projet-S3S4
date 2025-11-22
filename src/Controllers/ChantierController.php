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

    public function create(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès réservé au chef d'entreprise.");
        }

        $managerId = $_SESSION['user_id'] ?? null;

        if (!$managerId) {
            http_response_code(500);
            exit("Manager non identifié dans la session.");
        }

        // Liste des salariés et clients via le repository
        $salaries = ch_getSalaries($this->pdo);
        $clients  = ch_getClients($this->pdo);

        $errors   = [];
        $success  = false;

        // Valeurs par défaut pour le formulaire
        $date_jour = date('Y-m-d');
        $periode   = 'am';
        $commentaire = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $date_jour  = $_POST['date_jour'] ?? '';
            $periode    = $_POST['periode'] ?? 'am';
            $id_salarie = (int)($_POST['id_salarie'] ?? 0);
            $id_client  = !empty($_POST['id_client']) ? (int)$_POST['id_client'] : null;
            $commentaire = trim($_POST['commentaire'] ?? '');

            // Validation 
            if (!$date_jour) {
                $errors[] = "La date est obligatoire.";
            }
            if ($id_salarie <= 0) {
                $errors[] = "Veuillez sélectionner un salarié.";
            }
            if (!in_array($periode, ['am', 'pm'], true)) {
                $errors[] = "Période invalide.";
            }

            if (empty($errors)) {
                $ok = ch_createCreneauAvecPlanning(
                    $this->pdo,
                    $managerId,
                    $id_salarie,
                    $date_jour,
                    $periode,
                    $id_client,
                    $commentaire
                );

                if ($ok) {
                    // Redirection vers la vue planning sur la bonne semaine
                    header('Location: ' . BASE_URL . '/public/index.php?page=chef/planning&date=' . $date_jour);
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création du créneau.";
                }
            }
            if (empty($errors)) {

                // Tous les créneaux du salarié pour ce jour
                $existing = ch_getCreneauxJour($this->pdo, $id_salarie, $date_jour);

                // Limite de 2 créneaux / jour
                if (count($existing) >= 2) {
                    $errors[] = "Ce salarié est déjà planifié sur la journée complète (2 demi-journées).";
                } else {
                    // Empêcher la même demi-journée en double
                    if ($periode === 'am') {
                        $hDeb = '08:00:00';
                        $hFin = '12:00:00';
                    } else {
                        $hDeb = '13:00:00';
                        $hFin = '17:00:00';
                    }

                    foreach ($existing as $slot) {
                        if ($slot['heure_debut'] === $hDeb && $slot['heure_fin'] === $hFin) {
                            $errors[] = "Ce salarié a déjà un créneau sur cette demi-journée.";
                            break;
                        }
                    }
                }
            }
        }

        // Variables pour la vue
        $pageTitle = "Nouveau chantier – Team Jardin";
        $nav = ["Tableau de bord", "Facturation", "Planning"];
        $bouton = "Déconnexion";
        $redirection = BASE_URL . "/public/index.php?page=logout";

        $menu1 = [
            ['label'=>'Nouveau chantier', 'href'=> BASE_URL.'/public/index.php?page=chantier/create'],
            ['label'=>'Éditer chantier',  'href'=> BASE_URL.'/public/index.php?page=chantier/list'],
        ];
        $menu2 = [
            ['label'=>'Ajouter employé', 'href'=> BASE_URL.'/public/index.php?page=employe/create'],
            ['label'=>'Éditer employé',  'href'=> BASE_URL.'/public/index.php?page=employe/list'],
        ];

        require __DIR__ . '/../Views/chef/planning/create.php';
    }
}
