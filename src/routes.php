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




$page = $_GET['page'] ?? 'home';

$pagesPubliques = ['login', 'register', 'logout', 'home'];

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

    case 'chantier/delete':
        
        $controller = new ChantierController();
        $controller->delete();
    
    case 'chef/facturation/dashboard':
        
        $controller = new FacturationController();
        $controller->handleRequest();
        break;

        
    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
    
    }
