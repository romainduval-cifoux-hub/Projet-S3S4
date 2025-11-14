<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/LoginController.php';
require_once __DIR__ . '/Controllers/RegisterController.php';
require_once __DIR__ . '/Controllers/DashboardController.php';
require_once __DIR__ . '/Controllers/LogoutController.php';
require_once __DIR__ . '/Controllers/RealisationController.php';

$page = $_GET['page'] ?? 'home';

$pagesPubliques = ['login', 'register', 'logout', 'home', 'contact', 'realisation']; /* Ajout de 'realisation' */

if (!in_array($page, $pagesPubliques) && empty($_SESSION['user'])) {

    header('Location: ' . BASE_URL . '/public/index.php');
    exit;
}
                                        // Crée le PDO à partir des constantes définies dans config.php
                                        try {
                                            $pdo = new PDO(
                                                "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
                                                DB_USER,
                                                DB_PASS,
                                                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                                            );
                                        } catch (PDOException $e) {
                                            die('Erreur de connexion à la BDD : ' . $e->getMessage());
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

    case 'realisation':
        require_once __DIR__ . '/../src/Controllers/RealisationController.php';
        $controller = new RealisationController($pdo); // passer le PDO créé dans config.php
        $controller->affichage_realisations();        // appelle la méthode qui prépare la vue
        break;

        
    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
    
    }
