<?php

require_once __DIR__ . '/../Database/RealisationRepository.php';

class RealisationController {
    private RealisationRepository $repository;

    public function __construct(PDO $pdo) {                              // On se connecte à la BDD
        $this->repository = new RealisationRepository($pdo) ; 
    }

    public function affichage_realisations(): void {                     // Méthode pour afficher la page des réalisations*
        $categories = $this->repository->getAllCategories() ;
        $categoriesWithRealisations = [] ;

        foreach ($categories as $category) {
            $categoriesWithRealisations[$category['id']] = [
                'name' => $category['nom'],
                'realisations' => $this->repository->getRealisationsByCategory($category['id'])
            ];
        }
        require_once __DIR__ . '/../Views/vitrine/page-realisation.php' ; // On affiche la page après avoir récupéré les données
    }
}