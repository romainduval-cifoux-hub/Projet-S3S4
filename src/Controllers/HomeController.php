<?php

function ShowHomeController() {
    $pageTitle = "Team Jardin";

    require_once __DIR__ . '/../Controllers/RealisationController.php';
    $realisationController = new RealisationController();
    $realisationController = new RealisationController();
    $realisationFavorite = $realisationController->getFavorites();
    require __DIR__ . '/../Views/home.php';
}
