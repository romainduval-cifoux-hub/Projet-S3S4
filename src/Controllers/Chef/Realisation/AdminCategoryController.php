<?php
require_once __DIR__ . '/../../../../Database/CategoryRepository.php';
require_once __DIR__ . '/../../../../Database/db.php';

class AdminCategoryController {
    public PDO $pdo;
    private CategoryRepository $repo;

    public function __construct() {
        $pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        $this->repo = new CategoryRepository($pdo);
    }

    public function index() {
        $categories = $this->repo->getAll();
        $menuTitle1 = 'Gestion des catégories';
        $menu1 = [
            ['label'=>'Nouvelle catégorie', 'href'=> BASE_URL.'/public/index.php?page=chef/categories/create']
        ];
        require __DIR__ . '/../../../Views/chef/realisations/crudcategories.php';
    }

    public function create() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            if ($nom === '') $errors[] = "Le nom est obligatoire";

            if (empty($errors)) {
                $this->repo->create($nom);
                header('Location: ' . BASE_URL . '/public/index.php?page=chef/categories');
                exit;
            }
        }

        require __DIR__ . '/../../../Views/chef/realisations/form_categories.php';
    }

    public function edit() {
        $errors = [];
        $id = $_GET['id'] ?? null;
        if (!$id) exit("ID manquant");

        $category = $this->repo->getById((int)$id);
        if (!$category) exit("Catégorie non trouvée");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            if ($nom === '') $errors[] = "Le nom est obligatoire";

            if (empty($errors)) {
                $this->repo->update((int)$id, $nom);
                header('Location: ' . BASE_URL . '/public/index.php?page=chef/categories');
                exit;
            }
        }

        require __DIR__ . '/../../../Views/chef/realisations/form_categories.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) $this->repo->delete((int)$id);
        header('Location: ' . BASE_URL . '/public/index.php?page=chef/categories');
        exit;
    }
}
