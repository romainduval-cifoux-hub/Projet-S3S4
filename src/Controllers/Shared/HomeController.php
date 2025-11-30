<?php

function ShowHomeController() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $pageTitle = "Team Jardin";

    if (!empty($_SESSION['role']) && $_SESSION['role'] === 'client') {
        // Client connecté
        $nav         = ['Accueil', 'Avis', 'Nos réalisations', 'Contact', 'Profil'];
        $bouton      = 'Déconnexion';
        $redirection = BASE_URL . '/public/index.php?page=logout';
    } else {
        // Visiteur ou autre rôle
        $nav         = ['Accueil', 'Avis', 'Nos réalisations', 'Contact'];
        $bouton      = 'Se connecter';
        $redirection = BASE_URL . '/public/index.php?page=login';
    }

    require_once __DIR__ . '/../Vitrine/RealisationController.php';
    $realisationController = new RealisationController();
    $realisationFavorite = $realisationController->getFavorites();
    require __DIR__ . '/../../Views/home.php';
}
