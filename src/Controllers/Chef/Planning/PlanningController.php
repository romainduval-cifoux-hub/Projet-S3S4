<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/planningRepository.php';



class PlanningController
{
    private PDO $pdo;

    

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
    }

    public function handleRequest(): void
    {
        



        // Session + sécurité
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès réservé au chef d'entreprise.");
        }

        $view = $_GET['view'] ?? 'week'; // week par défaut
        $refDate = $_GET['date'] ?? date('Y-m-d');
        $searchEmp = trim($_GET['emp'] ?? '');

        
        if ($view === 'day') {
            $this->showDay($refDate, $searchEmp); 

            return;
        }

        if ($view === 'month') {
            $this->showMonth($refDate, $searchEmp);
            return;
        }

        // Par défaut: week
        $this->showWeek($refDate, $searchEmp);
    }

    private function commonLayoutVars(): array
    {
        // header
        $nav = ["Tableau de bord", "Facturation", "Planning"];
        $bouton = "Déconnexion";
        $redirection = BASE_URL . "/public/index.php?page=logout";

        // aside
        $menu1 = [
            ['label' => 'Nouveau chantier', 'href' => BASE_URL . '/public/index.php?page=chantier/create'],
            ['label' => 'Éditer chantier',  'href' => BASE_URL . '/public/index.php?page=chantier/list'],
        ];
        $menu2 = [
            ['label' => 'Ajouter employé', 'href' => BASE_URL . '/public/index.php?page=employe/create'],
            ['label' => 'Éditer employé',  'href' => BASE_URL . '/public/index.php?page=employe/list'],
        ];

        return compact('nav', 'bouton', 'redirection', 'menu1', 'menu2');
    }

    private function monthLabelFromDate(string $date): string
    {
        $moisFr = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        $monthNum = (int)date('n', strtotime($date));
        $year = date('Y', strtotime($date));
        return ($moisFr[$monthNum] ?? date('F', strtotime($date))) . ' ' . $year;
    }

    private function showWeek(string $refDate, string $searchEmp): void
    {
        $embed = (int)($_GET['embed'] ?? 0) === 1;

        $ts = strtotime($refDate);
        $lundi = date('Y-m-d', strtotime('monday this week', $ts));

        $joursAffiches = [];
        for ($i = 0; $i < 5; $i++) {
            $joursAffiches[] = date('Y-m-d', strtotime("$lundi +$i day"));
        }

        $prevMonday = date('Y-m-d', strtotime($lundi . ' -7 day'));
        $nextMonday = date('Y-m-d', strtotime($lundi . ' +7 day'));

        $monthLabel = $this->monthLabelFromDate($lundi);

        $employes = getSalaries($this->pdo, $searchEmp);
        $slotsMatrix = getWeekMatrix($this->pdo, $lundi);

        $pageTitle = "Planning – Team Jardin";

        extract($this->commonLayoutVars());

        if ($embed) {
            require __DIR__ . '/../../../Views/chef/planning/board_week.php';
            return;
        }



        require __DIR__ . '/../../../Views/chef/planning/gestionPlanning.php';
    }

    private function showDay(string $refDate, string $searchEmp): void
    {
        $embed = (int)($_GET['embed'] ?? 0) === 1;

        $date_jour = date('Y-m-d', strtotime($refDate));

        $prevDay = date('Y-m-d', strtotime($date_jour . ' -1 day'));
        $nextDay = date('Y-m-d', strtotime($date_jour . ' +1 day'));

        $monthLabel = $this->monthLabelFromDate($date_jour);

        $employes = getSalaries($this->pdo, $searchEmp);

        
        $dayMatrix = getDayMatrix($this->pdo, $date_jour);

        $pageTitle = "Planning (jour) – Team Jardin";

        extract($this->commonLayoutVars());

        if ($embed) {
            require __DIR__ . '/../../../Views/chef/planning/board_day.php';
            return;
        }



        require __DIR__ . '/../../../Views/chef/planning/gestionPlanningDay.php';
    }

    private function showMonth(string $refDate, string $searchEmp = ''): void
{
    $ts = strtotime($refDate);
    if ($ts === false) $ts = time();

    $year  = (int)date('Y', $ts);
    $month = (int)date('n', $ts);

    $monthStart = date('Y-m-01', $ts);
    $monthEnd   = date('Y-m-t', $ts); // dernier jour du mois

    // navigation mois précédent/suivant
    $prevMonth = date('Y-m-01', strtotime($monthStart . ' -1 month'));
    $nextMonth = date('Y-m-01', strtotime($monthStart . ' +1 month'));

    // Label mois FR
    $moisFr = [
        1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',
        7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'
    ];
    $monthLabel = ($moisFr[$month] ?? date('F', $ts)) . ' ' . $year;

    // Données: nb créneaux par jour
    $counts = getMonthCounts($this->pdo, $monthStart, $monthEnd);

    // Variables header/aside si tu les utilises
    $pageTitle = "Planning – Team Jardin";

    require __DIR__ . '/../../../Views/chef/planning/gestionPlanningMonth.php';
}

}
