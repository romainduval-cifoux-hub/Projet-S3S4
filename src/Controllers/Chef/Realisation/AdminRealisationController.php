<?php
require_once __DIR__ . '/../../../Database/adminRealisationRepository.php';
require_once __DIR__ . '/../../../Database/db.php';

class AdminRealisationController {

    private AdminRealisationRepository $repo;

    public function __construct() {
        $pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        $this->repo = new AdminRealisationRepository($pdo);
    }

    public function index() {
        $filters = [
            'q' => $_GET['q'] ?? null,
            'categorie_id' => $_GET['categorie_id'] ?? null,
            'favoris' => $_GET['favoris'] ?? null,
        ];

        $realisations = $this->repo->search($filters);
        $categories = $this->repo->getAllCategories();

        require __DIR__ . '/../../../Views/chef/realisations/crudrealisation.php';
    }

    public function create() {
        $errors = [];
        $categories = $this->repo->getAllCategories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentaire = $_POST['commentaire'] ?? '';
            $categorie_id = (int)($_POST['categorie_id'] ?? 0);
            $favoris = isset($_POST['favoris']) ? 1 : 0;

            $photoPath = null;

            if (!empty($_POST['croppedImage'])) {
                $data = $_POST['croppedImage'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                $targetDir = __DIR__ . '/../../../../public/assets/realisation/img/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

                $fileName = uniqid() . '.png';
                $targetFile = $targetDir . $fileName;

                if (file_put_contents($targetFile, $data)) {
                    $photoPath = '/public/assets/realisation/img/' . $fileName;
                } else {
                    $errors[] = "Erreur lors de l'enregistrement de l'image.";
                }
            } else {
                $errors[] = "La photo est obligatoire";
            }

            if (empty($errors)) {
                $this->repo->create([
                    'photo' => $photoPath,
                    'commentaire' => $commentaire,
                    'categorie_id' => $categorie_id,
                    'favoris' => $favoris
                ]);
                header('Location: ' . BASE_URL . '/public/index.php?page=chef/realisations');
                exit;
            }
        }

        require __DIR__ . '/../../../Views/chef/realisations/form_realisation.php';
    }

    public function edit() {
        $errors = [];
        $categories = $this->repo->getAllCategories();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID manquant";
            exit;
        }

        $realisation = $this->repo->getById((int)$id);
        if (!$realisation) {
            echo "Réalisation non trouvée";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentaire = $_POST['commentaire'] ?? '';
            $categorie_id = (int)($_POST['categorie_id'] ?? 0);
            $favoris = isset($_POST['favoris']) ? 1 : 0;

            $photoPath = $realisation['photo'];

            if (!empty($_POST['croppedImage'])) {
                $data = $_POST['croppedImage'];
                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                $targetDir = __DIR__ . '/../../../../public/assets/realisation/img/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

                $fileName = uniqid() . '.png';
                $targetFile = $targetDir . $fileName;

                if (file_put_contents($targetFile, $data)) {
                    if (!empty($realisation['photo'])) {
                        $oldPhotoPath = __DIR__ . '/../../../../' . ltrim($realisation['photo'], '/');
                        if (file_exists($oldPhotoPath)) unlink($oldPhotoPath);
                    }
                    $photoPath = '/public/assets/realisation/img/' . $fileName;
                } else {
                    $errors[] = "Erreur lors de l'enregistrement de l'image.";
                }
            }

            if (empty($errors)) {
                $this->repo->update((int)$id, [
                    'photo' => $photoPath,
                    'commentaire' => $commentaire,
                    'categorie_id' => $categorie_id,
                    'favoris' => $favoris
                ]);
                header('Location: ' . BASE_URL . '/public/index.php?page=chef/realisations');
                exit;
            }
        }

        require __DIR__ . '/../../../Views/chef/realisations/form_realisation.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID manquant";
            exit;
        }

        $realisation = $this->repo->getById((int)$id);

        if ($realisation) {
            if (!empty($realisation['photo'])) {
                $photoPath = __DIR__ . '/../../../../' . ltrim($realisation['photo'], '/');
                if (file_exists($photoPath)) unlink($photoPath);
            }

            $this->repo->delete((int)$id);
        }

        header('Location: ' . BASE_URL . '/public/index.php?page=chef/realisations');
        exit;
    }
}
