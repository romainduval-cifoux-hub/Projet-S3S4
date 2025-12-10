<?php
require_once __DIR__ . '/../src/config.php';

//Shared
require_once __DIR__ . '/Controllers/Shared/HomeController.php';
require_once __DIR__ . '/Controllers/Shared/LoginController.php';
require_once __DIR__ . '/Controllers/Shared/RegisterController.php';
require_once __DIR__ . '/Controllers/Shared/LogoutController.php';

//Vitrine
require_once __DIR__ . '/Controllers/Vitrine/AvisController.php';
require_once __DIR__ . '/Controllers/Vitrine/RealisationController.php';

//Chef
    //Planning
require_once __DIR__ . '/Controllers/Chef/Planning/PlanningController.php';
require_once __DIR__ . '/Controllers/Chef/Planning/ChantierController.php';
require_once __DIR__ . '/Controllers/Chef/Planning/EmployeController.php';
    //Facturation
require_once __DIR__ . '/Controllers/Chef/Facturation/FacturationController.php';
require_once __DIR__ . '/Controllers/Chef/Facturation/FormEditBusinessInfoController.php';
require_once __DIR__ . '/Controllers/Chef/Facturation/GestionnaireFacturationController.php';
    //Realisation
require_once __DIR__ . '/Controllers/Chef/Realisation/AdminRealisationController.php';
    //Categories
require_once __DIR__ . '/Controllers/Chef/Realisation/AdminCategoryController.php';
    //Conges
require_once __DIR__ . '/Controllers/Chef/Planning/ChefCongeController.php';

//Employe
require_once __DIR__ . '/Controllers/Employe/PlanningEmployeController.php';
    //Conges
require_once __DIR__ . '/Controllers/Employe/EmployeCongeController.php';



//Client
require_once __DIR__ . '/Controllers/Client/ClientController.php';



$page = $_GET['page'] ?? 'home';

$pagesPubliques = ['login', 'register', 'logout', 'home', 'realisation'];

if (!in_array($page, $pagesPubliques) && empty($_SESSION['user'])) {

    header('Location: ' . BASE_URL . '/public/index.php');
    exit;
}


switch ($page) {
    //commun
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

    case 'logout':
        $controller = new LogoutController();
        $controller->handleRequest();
        break;

    case 'avis_add':
    
        $controller = new AvisController();
        $controller->add();
        break;

    case 'realisation':
        require_once __DIR__ . '/Views/shared/header.php';  
        $controller = new RealisationController();
        $controller->affichage_realisations();
        require_once __DIR__ . '/Views/shared/footer.php' ;
        break;

    //Chef
    
        // Réalisations admin
    case 'chef/realisations':
        $controller = new AdminRealisationController();
        $controller->index();
        break;

    case 'chef/realisations/create':
        $controller = new AdminRealisationController();
        $controller->create();
        break;

    case 'chef/realisations/edit':
        $controller = new AdminRealisationController();
        $controller->edit();
        break;

    case 'chef/realisations/delete':
        $controller = new AdminRealisationController();
        $controller->delete();
        break;

        // Catégories admin
    case 'chef/categories':
        $controller = new AdminCategoryController();
        $controller->index();
        break;

    case 'chef/categories/create':
        $controller = new AdminCategoryController();
        $controller->create();
        break;

    case 'chef/categories/edit':
        $controller = new AdminCategoryController();
        $controller->edit();
        break;

    case 'chef/categories/delete':
        $controller = new AdminCategoryController();
        $controller->delete();
        break;
        //conges
    case 'chef/conges':
        $controller = new ChefCongeController();
        $controller->index();
        break;

    case 'chef/conges/traiter':
        $controller = new ChefCongeController();
        $controller->traiter();
        break;


        //Planning
    case 'chef/planning':
        
        $controller = new PlanningController();
        $controller->handleRequest();
        break;

    case 'chantier/create':
        $controller = new ChantierController();
        $controller->create();
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

        //Facturation
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

    case 'chef/facturation/createFacture':
        $controller = new GestionnaireFacturationController();
        $controller->handleRequest();
        break;

    //Client
    case 'client/profil':
        $controller = new ClientController();
        $controller->profil();
        break;

    case 'client/save':
        $controller = new ClientController();
        $controller->save();
        break;
    //Employe
        //Conges
    case 'employe/conge':
        $controller = new EmployeCongeController();
        $controller->demande();
        break;
        
    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
    
    }
