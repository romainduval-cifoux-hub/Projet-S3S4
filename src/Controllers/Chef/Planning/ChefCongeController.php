<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/congeRepository.php';
require_once __DIR__ . '/../../../Database/notificationsRepository.php';

class ChefCongeController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkAdmin(): void {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès réservé au chef d'entreprise.");
        }
    }

    public function index(): void
    {
        $this->checkAdmin();

        $demandes = conge_getEnAttente($this->pdo);

        $pageTitle = "Demandes de congés – Team Jardin";

        require __DIR__ . '/../../../Views/chef/planning/listedemandesconge.php';
    }

    public function traiter(): void
    {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/public/index.php?page=chef/conges');
            exit;
        }

        $id_conge = (int)($_POST['id_conge'] ?? 0);
        $action   = $_POST['action'] ?? '';

        $conge = conge_getById($this->pdo, $id_conge);
        if (!$conge) {
            header('Location: ' . BASE_URL . '/public/index.php?page=chef/conges&err=notfound');
            exit;
        }

        $managerId = (int)($_SESSION['user_id'] ?? 0);

        if ($action === 'accepter') {

            $okPlanning = conge_appliquerAuPlanning(
                $this->pdo,
                (int)$conge['id_salarie'],
                $managerId,
                $conge['date_debut'],
                $conge['date_fin']
            );


            if ($okPlanning) {
                conge_updateStatut($this->pdo, $id_conge, 'accepte', $managerId);

                notif_create(
                    $this->pdo,
                    (int)$conge['id_salarie'],      // destinataire = salarié
                    $managerId,                     // expéditeur = chef
                    "Congé accepté",
                    "Votre demande de congé du {$conge['date_debut']} au {$conge['date_fin']} a été acceptée.",
                    "success",
                    BASE_URL . "/public/index.php?page=employe/planning&date=" . $conge['date_debut']
                );


                header('Location: ' . BASE_URL . '/public/index.php?page=chef/conges&ok=1');
            } else {
                // conflit planning
                header('Location: ' . BASE_URL . '/public/index.php?page=chef/conges&err=planning');
            }

            


        } elseif ($action === 'refuser') {

            conge_updateStatut($this->pdo, $id_conge, 'refuse', $managerId);

            notif_create(
                $this->pdo,
                (int)$conge['id_salarie'],
                $managerId,
                "Congé refusé",
                "Votre demande de congé du {$conge['date_debut']} au {$conge['date_fin']} a été refusée.",
                "error",
                BASE_URL . "/public/index.php?page=employe/conges"
            );


            header('Location: ' . BASE_URL . '/public/index.php?page=chef/conges&ok=0');



        } else {
            header('Location: ' . BASE_URL . '/public/index.php?page=chef/conges&err=action');
        }

        exit;
    }
}
