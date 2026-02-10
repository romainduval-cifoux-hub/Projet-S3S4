<?php
require_once __DIR__ . '/../src/config.php';
// on tente de régler un soucis

//Shared
require_once __DIR__ . '/Controllers/Shared/HomeController.php';
require_once __DIR__ . '/Controllers/Shared/LoginController.php';
require_once __DIR__ . '/Controllers/Shared/RegisterController.php';
require_once __DIR__ . '/Controllers/Shared/LogoutController.php';
require_once __DIR__ . '/Controllers/Shared/ForgotPasswordController.php';
require_once __DIR__ . '/Controllers/Shared/ResetPasswordController.php';
    //Avatar
require_once __DIR__ . '/Controllers/Shared/AvatarController.php';

require_once __DIR__ . '/Controllers/auth/ActivateController.php';

//Vitrine
require_once __DIR__ . '/Controllers/Vitrine/AvisController.php';
require_once __DIR__ . '/Controllers/Vitrine/RealisationController.php';
require_once __DIR__ . '/Controllers/Vitrine/ContactSubmitController.php';


//Chef
//Planning
require_once __DIR__ . '/Controllers/Chef/Planning/PlanningController.php';
require_once __DIR__ . '/Controllers/Chef/Planning/ChantierController.php';
require_once __DIR__ . '/Controllers/Chef/Planning/EmployeController.php';
//Facturation
require_once __DIR__ . '/Controllers/Chef/Facturation/FacturationController.php';
require_once __DIR__ . '/Controllers/Chef/Facturation/FormEditBusinessInfoController.php';
require_once __DIR__ . '/Controllers/Chef/Facturation/GestionnaireFacturationController.php';
require_once __DIR__ . '/Controllers/Chef/Facturation/CreationDocumentController.php';
//Notifications
require_once __DIR__ . '/Controllers/Chef/Notifications/NotificationsChefController.php';


//Realisation
require_once __DIR__ . '/Controllers/Chef/Realisation/AdminRealisationController.php';
//Categories
require_once __DIR__ . '/Controllers/Chef/Realisation/AdminCategoryController.php';
//Conges
require_once __DIR__ . '/Controllers/Chef/Planning/ChefCongeController.php';

//Client
require_once __DIR__ . '/Controllers/Client/ClientController.php';
require_once __DIR__ . '/Controllers/Client/ClientDocumentController.php';


//Employe
require_once __DIR__ . '/Controllers/Employe/PlanningEmployeController.php';
require_once __DIR__ . '/Controllers/Employe/NotificationsEmployeController.php';


//Conges
require_once __DIR__ . '/Controllers/Employe/EmployeCongeController.php';

//Profil
require_once __DIR__ . '/Controllers/Employe/EmployeProfilController.php';


//Client
require_once __DIR__ . '/Controllers/Client/ClientController.php';





$page = $_GET['page'] ?? 'home';

$pagesPubliques = ['login', 'register', 'activate', 'logout', 'home', 'realisation', 'forgot_password', 'reset_password', 'contact_submit'];

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
        $bouton = isset($_SESSION['user']) ? 'Déconnexion' : 'Se connecter';
        $lienBouton = isset($_SESSION['user'])
            ? BASE_URL . '/public/index.php?page=logout'
            : BASE_URL . '/public/index.php?page=login';

        require_once __DIR__ . '/Views/shared/header.php';

        $controller = new RealisationController();
        $controller->affichage_realisations();

        require_once __DIR__ . '/Views/shared/footer.php';
        break;

    case 'avatar/upload':
        
        $controller = new AvatarController();
        $controller->upload();
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

    case 'chef/realisations/toggleFavoris':
        $controller = new AdminRealisationController();
        $controller->toggleFavoris();
        break;

    case 'chef/realisations/toggleMasque':
        $controller = new AdminRealisationController();
        $controller->toggleMasque();
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
        $controller = new CreationDocumentController();
        $controller->handleRequest();
        break;

    //Notifications Chef

    case 'chef/notifications':
        $controller = new NotificationsChefController();
        $controller->index();
        break;

    case 'chef/notifications/read':
        $controller = new NotificationsChefController();
        $controller->read();
        break;

    case 'chef/notifications/read-all':
        $controller = new NotificationsChefController();
        $controller->readAll();
        break;


    //Client
    case 'client/document/pdf':
        $controller = new ClientDocumentController();
        $controller->pdf();
        break;

    case 'client/profil':
        $controller = new ClientController();
        $controller->profil();
        break;

    case 'client/save':
        $controller = new ClientController();
        $controller->save();
        break;

    case 'client/documents':
        $controller = new ClientController();
        $controller->documents();
        break;

    case 'client/commentaires':
        $controller = new ClientController();
        $controller->commentaires();
        break;

    case 'client/supprimer_commentaire':
        $controller = new ClientController();
        $controller->supprimer_commentaire();
        break;

    case 'activate':
        $controller = new ActivateController();
        $controller->handleRequest();
        break;

    //Employe
    //Notifications Employee

    case 'employe/notifications':
        $controller = new NotificationsEmployeController();
        $controller->index();
        break;

    case 'employe/notifications/read':
        $controller = new NotificationsEmployeController();
        $controller->read();
        break;

    case 'employe/notifications/read-all':
        $controller = new NotificationsEmployeController();
        $controller->readAll();
        break;


    //Conges
    case 'employe/conge':
        $controller = new EmployeCongeController();
        $controller->demande();
        break;

    case 'forgot_password':
        $controller = new ForgotPasswordController();
        $controller->handleRequest();
        break;

    case 'reset_password':
        $controller = new ResetPasswordController();
        $controller->handleRequest();
        break;

    //profil


    case 'employe/profil':

        $controller = new EmployeProfilController();
        $controller->handleRequest('profil');
        break;

    case 'employe/profil/save':

        $controller = new EmployeProfilController();
        $controller->handleRequest('save');
        break;
    case 'contact_submit':
        $controller = new ContactSubmitController();
        $controller->handleRequest();
        break;


    default:
        http_response_code(404);
        echo "404 - Page non trouvée";
        break;
}
