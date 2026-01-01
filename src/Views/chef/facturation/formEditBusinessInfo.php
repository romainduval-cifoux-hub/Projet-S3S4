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
  <link href="<?= BASE_URL ?>/public/assets/chef/formEditBusinessInfo/style.css" rel="stylesheet">

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

    <form action="" method="post">

        <h2>Modifier les informations de l'entreprise</h2>
        <div class="container">
            <div class="gauche">
                <label>Raison sociale</label><br>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($dataBusiness['nom']); ?>" required>
                <br><br>

                <label>Description</label><br>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($dataBusiness['description']); ?></textarea>
                <br><br>
            </div>
            <div class="droite">
                <label>Téléphone</label><br>
                <input type="text" name="telephone" value="<?php echo htmlspecialchars($dataBusiness['telephone']); ?>">
                <br><br>

                <label>Adresse</label><br>
                <input type="text" name="adresse" value="<?php echo htmlspecialchars($dataBusiness['adresse']); ?>">
                <br><br>

                <label>SIRET</label><br>
                <input type="text" name="siret" value="<?php echo htmlspecialchars($dataBusiness['siret']); ?>">
                <br><br>

            <label>IBAN</label><br>
            <input type="text" name="iban" value="<?php echo htmlspecialchars($dataBusiness['iban']); ?>">
            <br><br>


            <label>BIC</label><br>
            <input type="text" name="bic" value="<?php echo htmlspecialchars($dataBusiness['bic']); ?>">
            <br><br>
            </div>

            
=
        </div>
        <button type="submit">Enregistrer</button>

        <?php if (!empty($successMessage)): ?>
            <div>
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>


    </form>

</main>