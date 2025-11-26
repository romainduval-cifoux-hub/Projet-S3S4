<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/LoginController.php';
require_once __DIR__ . '/Controllers/RegisterController.php';
require_once __DIR__ . '/Controllers/DashboardController.php';
require_once __DIR__ . '/Controllers/LogoutController.php';
require_once __DIR__ . '/Controllers/PlanningController.php';
require_once __DIR__ . '/Controllers/ChantierController.php';
require_once __DIR__ . '/Controllers/FacturationController.php';
require_once __DIR__ . '/Controllers/FormEditBusinessInfoController.php';
require_once __DIR__ . '/Controllers/GestionnaireFacturationController.php';
require_once __DIR__ . '/Controllers/RealisationController.php';
require_once __DIR__ . '/Controllers/EmployeController.php';
require_once __DIR__ . '/Controllers/PlanningEmployeController.php';

require_once __DIR__ . '/Controllers/ClientController.php';


require_once __DIR__ . '/Controllers/AvisController.php';





$page = $_GET['page'] ?? 'home';

$pagesPubliques = ['login', 'register', 'logout', 'home', 'realisation'];

if (!in_array($page, $pagesPubliques) && empty($_SESSION['user'])) {

    header('Location: ' . BASE_URL . '/public/index.php');
    exit;
}


switch ($page) {
    case 'login':
        $loginController = new LoginController();
        $loginController->handleRequest();
        break;

    case 'register':
        $registerController = new RegisterController();
        $registerController->handleRequest();
        break;

    case 'home':
        ShowHomeController(); 
        break;

    case 'dashboard':
        $controller = new DashboardController();
        $controller->handleRequest();
        break;

    case 'logout':
        $controller = new LogoutController();
        $controller->handleRequest();
        break;

    case 'chef/planning':
        
        $controller = new PlanningController();
        $controller->handleRequest();
        break;

    case 'chantier/create':
        $controller = new ChantierController();
        $controller->create();
        break;
        
    case 'avis_add':
    
        $controller = new AvisController();
        $controller->add();
        break;
    

    case 'chef/facturation':
        
        $controller = new FacturationController();
        $controller->handleRequest();
        break;


    case 'chantier/delete':
        
        $controller = new ChantierController();
        $controller->delete();
        break;

    case 'chantier/edit':
        $controller = new ChantierController();
        $controller->edit();
        break;
    
    case 'employe/create':
        $controller = new EmployeController();
        $controller->create();
        break;

    case 'employe/list':
        $controller = new EmployeController();
        $controller->liste();
        break;

    case 'employe/planning':
        $controller = new PlanningEmployeController();
        $controller->handleRequest();
        break;

    
    case 'chef/facturation/dashboard':
        
        $controller = new FacturationController();
        $controller->handleRequest();
        break;

    case 'chef/facturation/EditBusinessInfo':
        
        $controller = new FormEditBusinessInfoController();
        $controller->handleRequest();
        break;

    case 'chef/facturation/GestionFacturation':
        
        $controller = new GestionnaireFacturationController();
        $controller->handleRequest();
        break;

    case 'client/profil':
        $controller = new ClientController();
        $controller->profil();
        break;

    case 'client/save':
        $controller = new ClientController();
        $controller->save();
        break;

    case 'realisation':
        require_once __DIR__ . '/Views/shared/header.php';  
        $controller = new RealisationController();
        $controller->affichage_realisations();
        require_once __DIR__ . '/Views/shared/footer.php' ;
        break;
        
    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
    
    }
