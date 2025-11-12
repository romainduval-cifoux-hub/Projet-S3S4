<?php 
    function ShowRealisationController() {
        $pageTitle = "Nos Réalisations - Team Jardin";
        $nav = ['Accueil', 'Avis', 'Nos réalisations', 'Contact'];
        $bouton = "Se connecter";
        $redirection = BASE_URL . "/public/index.php?page=login";
        require __DIR__ . '/../Views/Vitrine/page-realisation.php';
}