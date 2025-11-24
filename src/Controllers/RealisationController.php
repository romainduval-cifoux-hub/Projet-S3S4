<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database/realisationRepository.php';

class RealisationController {

    private RealisationRepository $repo;

    public function __construct() {
        $pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        $this->repo = new RealisationRepository($pdo);
    }

    public function getFavorites(): array {
        return $this->repo->getRealisationsFavorites();
    }


    public function affichage_realisations_favories():void {
        $realisationFavorite = $this->repo->getRealisationsFavorites();
        require_once __DIR__ . '/../Views/vitrine/realisation.php';

    }


    public function affichage_realisations(): void {
        $categories = $this->repo->getAllCategories();
        $categoriesWithRealisations = [];

        foreach ($categories as $category) {
            $categoriesWithRealisations[$category['id']] = [
                'name' => $category['nom'],
                'realisations' => $this->repo->getRealisationsByCategory($category['id'])
            ];
        }

        require_once __DIR__ . '/../Views/vitrine/page-realisation.php';
    }
}
