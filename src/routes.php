<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/LoginController.php';
require_once __DIR__ . '/Controllers/RegisterController.php';
require_once __DIR__ . '/Controllers/DashboardController.php';
require_once __DIR__ . '/Controllers/LogoutController.php';

$page = $_GET['page'] ?? 'home';

$pagesPubliques = ['login', 'register', 'logout', 'home', 'contact', 'realisation']; /* Ajout de 'realisation' */

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

    case 'contact':
        require_once __DIR__ . '/../src/Views/shared/contact.php';
    break;

    case 'realisation':  /* Ajout de ce cas */
        require_once __DIR__ . '/../src/Views/vitrine/page-realisation.php';
        break;
        
    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
    
    }
