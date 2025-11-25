<?php

require_once(__DIR__ . '/../../../config.php'); 
require_once(__DIR__ . '/../../../Database/db.php');     

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Team jardin (Chef d'entreprise)</title>

    <!-- CSS -->

  <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">

  <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">

  <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/chef/gestionFacture/style.css" rel="stylesheet">

  <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<div class="page">
    <?php
        require_once(__DIR__ . '/../shared/header_chef.php');
    ?>

    <div class="app">
        <?php
            require_once(__DIR__ . '/asidefacturation.php');
        ?>

<main>


<h1>Liste des factures</h1>

<?php foreach ($factures as $facture): ?>

<div class="facture">
    <h2>Facture n°<?= $facture['num'] ?></h2>
    <hr>
    <p><strong>Client :</strong> <?= $facture['nomClient'] ?></p>
    <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($facture['dateDoc'])) ?></p>
    <p><strong>Status :</strong> <?= $facture['statusDoc'] ?></p>

    <h3>Détails :</h3>

    <?php $total = 0;  ?>

    <ul>
        <?php foreach ($facture['lignes'] as $ligne): ?>
            <?php 
                $ligneTotal = $ligne['quantite'] * $ligne['prixUnitaire'];
                $total += $ligneTotal; 
            ?>
            <li>
                <?= $ligne['designation'] ?> : <?= $ligneTotal ?> €
            </li>
        <?php endforeach; ?>
    </ul>
    <hr>
    <p><strong>Total :</strong> <?= $total ?> €</p>
</div>

<?php endforeach; ?>

</main>
