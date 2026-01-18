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

<body>
    
<div class="page">

<?php require_once(__DIR__ . '/../shared/header_chef.php'); ?>

<div class="app">
<?php require_once(__DIR__ . '/asidefacturation.php'); ?>

<main>

    <form action="" method="post" class="business-form">

        <div class="business-form__container">

            <div class="business-form__section">
                <label>Raison sociale</label>
                <input type="text" name="nom"
                       value="<?= htmlspecialchars($dataBusiness['nom']); ?>" required>

                <label>Description</label>
                <input type="text" name="description"
                    value="<?= htmlspecialchars($dataBusiness['description']); ?>" required>

                <label>Téléphone</label>
                <input type="text" name="telephone"
                       value="<?= htmlspecialchars($dataBusiness['telephone']); ?>">

                <label>Adresse</label>
                <input type="text" name="adresse"
                       value="<?= htmlspecialchars($dataBusiness['adresse']); ?>">

                <label>SIRET</label>
                <input type="text" name="siret"
                       value="<?= htmlspecialchars($dataBusiness['siret']); ?>">

                <label>IBAN</label>
                <input type="text" name="iban"
                       value="<?= htmlspecialchars($dataBusiness['iban']); ?>">

                <label>BIC</label>
                <input type="text" name="bic"
                       value="<?= htmlspecialchars($dataBusiness['bic']); ?>">
            </div>

        </div>

        <button type="submit" class="business-form__submit">
            Enregistrer
        </button>

        <?php if (!empty($successMessage)): ?>
            <div class="business-form__success">
                <?= htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        </form>

    </main>
        </div>
            <?php require_once(__DIR__ . '/../../shared/footer.php'); ?>
            </div> 
</body>
</html>