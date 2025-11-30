<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/planningRepository.php';

class PlanningController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    }

    public function handleRequest() {

        // Sécurité (seulement chef/admin)
        if (!isset($_SESSION)) {
            session_start();
        }

        if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès réservé au chef d'entreprise.");
        }

        
        //Déterminer la semaine
        
        $refDate = $_GET['date'] ?? date('Y-m-d');
        $ts      = strtotime($refDate);
        $lundi   = date('Y-m-d', strtotime('monday this week', $ts));

        $joursAffiches = [];
        for ($i = 0; $i < 5; $i++) {
            $joursAffiches[] = date('Y-m-d', strtotime("$lundi +$i day"));
        }

        //Labels semaine
        $debutLabel = date('d/m/Y', strtotime($joursAffiches[0]));
        $finLabel   = date('d/m/Y', strtotime(end($joursAffiches)));
        $weekLabel  = "Semaine du $debutLabel au $finLabel";

        //Semaine précédente / suivante
        $prevMonday = date('Y-m-d', strtotime($lundi . ' -7 day'));
        $nextMonday = date('Y-m-d', strtotime($lundi . ' +7 day'));

        // Label mois (en français)
        $moisFr = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        $monthNum   = (int)date('n', strtotime($lundi)); // mois du lundi de la semaine
        $year       = date('Y', strtotime($lundi));
        $monthLabel = ($moisFr[$monthNum] ?? date('F', strtotime($lundi))) . ' ' . $year;

        //Charger données BDD
        $employes    = getSalaries($this->pdo);
        $slotsMatrix = getWeekMatrix($this->pdo, $lundi);

        
        //Variables envoyées à la vue
        $pageTitle = "Planning – Team Jardin";

        // header
        $nav = ["Tableau de bord", "Facturation", "Planning"];
        $bouton = "Déconnexion";
        $redirection = BASE_URL . "/public/index.php?page=logout";

        // aside
        $menu1 = [
            ['label'=>'Nouveau chantier', 'href'=> BASE_URL.'/public/index.php?page=chantier/create'],
            ['label'=>'Éditer chantier',  'href'=> BASE_URL.'/public/index.php?page=chantier/list'],
        ];
        $menu2 = [
            ['label'=>'Ajouter employé', 'href'=> BASE_URL.'/public/index.php?page=employe/create'],
            ['label'=>'Éditer employé',  'href'=> BASE_URL.'/public/index.php?page=employe/list'],
        ];
    

        
        require __DIR__ . '/../../../Views/chef/planning/gestionPlanning.php';
        
    }
}
