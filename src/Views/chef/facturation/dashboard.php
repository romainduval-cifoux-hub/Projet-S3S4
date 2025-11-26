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
  <link href="<?= BASE_URL ?>/public/assets/chef/dashboard/style.css" rel="stylesheet">

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
    <h1>Tableau de bord de l'année <?php echo($annee)?></h1>
    <div class="container">
        <div class="en-attente">
            <?php

                echo "Nombre de facture en attente : $nbFactureEnAttente <br>";
                echo "Montant en attente : $montantFactureEnAttente € <br>";
            ?>

            <!-- Chart Montant en attente -->
            <div style="width: 600px; height: 400px; margin-bottom: 30px;">
                <canvas id="facturesChart"></canvas>
            </div>

            <script>
                const labelsMois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
                const montantsParMois = <?php echo json_encode(array_values($montantsParMois)); ?>;

                new Chart(document.getElementById('facturesChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labelsMois,
                        datasets: [{
                            label: 'Montant en attente (€)',
                            data: montantsParMois,
                            backgroundColor: 'rgba(66, 66, 66, 0.6)',
                            borderColor: 'rgba(43, 43, 43, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            </script>
        </div>

        <div class="paye">
            <?php 
            
                echo "Nombre de facture payées : $nbFacturePayee <br>";
                echo "Chiffre d'affaires : $montantFacturePayee € <br>";    
            ?>

            <!-- Chart Montant payé -->
            <div style="width: 600px; height: 400px;">
                <canvas id="facturesPayeesChart"></canvas>
            </div>

            <script>
                const montantsPayesParMois = <?php echo json_encode(array_values($montantPayeparMois)); ?>;

                new Chart(document.getElementById('facturesPayeesChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labelsMois,
                        datasets: [{
                            label: 'Montant payé (€)',
                            data: montantsPayesParMois,
                            backgroundColor: 'rgba(4, 134, 0, 0.6)',
                            borderColor: 'rgba(0, 90, 12, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            </script>
        </div>
    </div> 
</main>
    </div>

    <?php require_once(__DIR__ . '/../../shared/footer.php'); ?>

</div>
