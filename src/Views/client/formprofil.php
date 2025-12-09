<?php require_once __DIR__ . '/../../config.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Mon profil client') ?></title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/clients/profil/style.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/burger-accueil/css/style.css" rel="stylesheet">
</head>
<body>
    <?php 

    $nav         = ['Accueil', 'Avis', 'Nos réalisations', 'Contact', 'Profil'];
    $bouton      = 'Déconnexion';
    $redirection = BASE_URL . '/public/index.php?page=logout';

    
    
    require __DIR__ . '/../shared/header.php'; 
    require __DIR__ . '/../shared/burger-accueil.php';
    ?>
    
    <main>
        <h1>Mon profil client</h1>

        <?php if (!empty($errors ?? [])): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php
            $isNew = empty($client);
        ?>

        <form method="post" action="<?= BASE_URL ?>/public/index.php?page=client/save">
            <div class="form-row">
                <label>Nom</label>
                <input type="text" name="nom" required
                       value="<?= htmlspecialchars($client['nom_client'] ?? '') ?>">
            </div>

            <div class="form-row">
                <label>Prénom</label>
                <input type="text" name="prenom" required
                       value="<?= htmlspecialchars($client['prenom_client'] ?? '') ?>">
            </div>

            <div class="form-row">
                <label>Adresse</label>
                <input type="text" name="adresse"
                       value="<?= htmlspecialchars($client['adresse_client'] ?? '') ?>">
            </div>

            <div class="form-row">
                <label>Ville</label>
                <input type="text" name="ville"
                       value="<?= htmlspecialchars($client['ville_client'] ?? '') ?>">
            </div>

            <div class="form-row">
                <label>Code postal</label>
                <input type="text" name="code_postal"
                       value="<?= htmlspecialchars($client['code_postal_client'] ?? '') ?>">
            </div>

            <div class="form-row">
                <label>SIRET (optionnel)</label>
                <input type="text" name="siret"
                       value="<?= htmlspecialchars($client['siret_client'] ?? '') ?>">
            </div>

            <button type="submit" class="btn_login">
                <?= $isNew ? "Créer mon profil" : "Mettre à jour mes informations" ?>
            </button>
        </form>
    </main>

    <?php require __DIR__ . '/../shared/footer.php'; ?>
</body>
</html>
