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

    public function delete(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès refusé");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id_creneau'] ?? 0);
            if ($id > 0) {
                ch_deleteCreneau($this->pdo, $id);
            }
        }

        // Retour au planning
        header('Location: ' . BASE_URL . '/public/index.php?page=chef/planning');
        exit;
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
                // Créneaux déjà planifiés pour ce salarié ce jour-là
                $existing = ch_getCreneauxJour($this->pdo, $id_salarie, $date_jour);

                // 1) Limite de 2 créneaux (matin + après-midi max)
                if (count($existing) >= 2) {
                    $errors[] = "Ce salarié est déjà planifié sur la journée complète (2 demi-journées).";
                } else {
                    // 2) Empêcher de recréer la même demi-journée
                    if ($periode === 'am') {
                        $hDeb = '08:00:00';
                        $hFin = '12:00:00';
                    } else { // pm
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
        }

        // Variables pour la vue
        $pageTitle = "Nouveau chantier – Team Jardin";
        $nav = ["Tableau de bord", "Facturation", "Planning"];
        $bouton = "Déconnexion";
        $redirection = BASE_URL . "/public/index.php?page=logout";


        require __DIR__ . '/../Views/chef/planning/create.php';
    }

    
}
