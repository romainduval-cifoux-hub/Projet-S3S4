<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database/db.php';
require_once __DIR__ . '/../Database/planningRepository.php';

class PlanningEmployeController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function handleRequest(): void
    {
        // Sécurité : seulement salariés
        if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'salarie') {
            http_response_code(403);
            exit("Accès réservé aux employés.");
        }

        $idUser = $_SESSION['user_id'] ?? null;
        if (!$idUser) {
            http_response_code(500);
            exit("Utilisateur non identifié.");
        }

        // Un salarié a id_salarie = id dans users
        $idSalarie = $idUser;

        // 1) Semaine
        $refDate = $_GET['date'] ?? date('Y-m-d');
        $ts      = strtotime($refDate);
        $lundi   = date('Y-m-d', strtotime('monday this week', $ts));

        $joursAffiches = [];
        for ($i = 0; $i < 5; $i++) {
            $joursAffiches[] = date('Y-m-d', strtotime("$lundi +$i day"));
        }

        $prevMonday = date('Y-m-d', strtotime($lundi . ' -7 day'));
        $nextMonday = date('Y-m-d', strtotime($lundi . ' +7 day'));

        // 2) Charger les slots de CET employé pour la semaine
        $slotsMatrix = getWeekMatrix($this->pdo, $lundi);

        $slotsPerso = $slotsMatrix[$idSalarie] ?? [];

        $pageTitle = "Mon planning – Team Jardin";

        require __DIR__ . '/../Views/employe/planning.php';
    }
}
