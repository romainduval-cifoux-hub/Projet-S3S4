<?php
require_once(__DIR__ . '/../../../config.php'); 
require_once(__DIR__ . '/../../../Database/db.php');     
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
    <meta charset="utf-8">
    <title>Tableau de bord – Chef d'entreprise</title>

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

<body>
<div class="page">

<?php require_once(__DIR__ . '/../shared/header_chef.php'); ?>

<div class="app">
<?php require_once(__DIR__ . '/asidefacturation.php'); ?>

<main>

<!-- HEADER -->
<div class="header-filter">
    <h1>Tableau de bord de l'année <?= $annee ?></h1>

    <form method="POST">
        <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '' ) ?>">
        <label for="annee">Année :</label>
        <select name="annee" id="annee" onchange="this.form.submit()">
            <?php foreach ($annees as $a): ?>
                <option value="<?= $a ?>" <?= ((int)$a === (int)$annee) ? 'selected' : '' ?>>
                    <?= $a ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- KPI -->
<div class="kpi-container">

    <div class="kpi-card kpi-grey">
        <span class="kpi-title">Facture(s)</span>
        <span class="kpi-value"><?= $nbFactureEnAttente ?></span>
    </div>

    <div class="kpi-card kpi-grey">
        <span class="kpi-title">Total en attente</span>
        <span class="kpi-value"><?= number_format($montantFactureEnAttente, 2, ',', ' ') ?> €</span>
    </div>

    <div class="kpi-card kpi-green">
        <span class="kpi-title">Facture(s)</span>
        <span class="kpi-value"><?= $nbFacturePayee ?></span>
    </div>

    <div class="kpi-card kpi-green">
        <span class="kpi-title">Total reçu</span>
        <span class="kpi-value"><?= number_format($montantFacturePayee, 2, ',', ' ') ?> €</span>
    </div>

</div>

<!-- CHARTS -->
<div class="charts">

    <div class="chart-card">
        <h2>EN ATTENTE</h2>
        <canvas id="facturesChart"></canvas>
    </div>

    <div class="chart-card">
        <h2>CHIFFRE D'AFFAIRES</h2>
        <canvas id="facturesPayeesChart"></canvas>
    </div>

</div>

</main>
</div>

<?php require_once(__DIR__ . '/../../shared/footer.php'); ?>

</div>

<script>
const labelsMois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

new Chart(document.getElementById('facturesChart'), {
    type: 'bar',
    data: {
        labels: labelsMois,
        datasets: [{
            data: <?= json_encode(array_values($montantsParMois)); ?>,
            backgroundColor: 'rgba(90,90,90,0.7)'
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

new Chart(document.getElementById('facturesPayeesChart'), {
    type: 'bar',
    data: {
        labels: labelsMois,
        datasets: [{
            data: <?= json_encode(array_values($montantPayeparMois)); ?>,
            backgroundColor: 'rgba(40,160,60,0.7)'
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>
