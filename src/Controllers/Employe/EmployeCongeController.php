<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/congeRepository.php';

class EmployeCongeController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function demande(): void
    {
        if (($_SESSION['role'] ?? '') !== 'salarie') {
            http_response_code(403);
            exit("Accès réservé aux salariés.");
        }

        $id_salarie = (int)($_SESSION['user_id'] ?? 0); // id_salarie == users.id

        $errors = [];
        $success = false;

        $date_debut = date('Y-m-d');
        $date_fin   = date('Y-m-d');
        $motif      = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date_debut = $_POST['date_debut'] ?? '';
            $date_fin   = $_POST['date_fin'] ?? '';
            $motif      = trim($_POST['motif'] ?? '');

            if (!$date_debut || !$date_fin) {
                $errors[] = "Les dates de début et de fin sont obligatoires.";
            } elseif (strtotime($date_fin) < strtotime($date_debut)) {
                $errors[] = "La date de fin doit être supérieure ou égale à la date de début.";
            }

            if (empty($errors)) {
                $ok = conge_createDemande($this->pdo, $id_salarie, $date_debut, $date_fin, $motif);
                if ($ok) {
                    $success = true;
                    // On réinitialise le formulaire
                    $motif = '';
                } else {
                    $errors[] = "Erreur lors de l'enregistrement de la demande.";
                }
            }
        }

        // Historique du salarié
        $demandes = conge_getBySalarie($this->pdo, $id_salarie);

        $notifsUnread = notif_getUnread($this->pdo, (int)$_SESSION['user_id']);
        $nbNotifs     = count($notifsUnread);

        $pageTitle = "Demande de congés – Team Jardin";

        require __DIR__ . '/../../Views/employe/demandeconge.php';
    }
}
