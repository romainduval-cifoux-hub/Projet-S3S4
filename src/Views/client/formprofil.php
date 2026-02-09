<?php require_once __DIR__ . '/../../config.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
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
    $bouton = "Déconnexion";
    $redirection = BASE_URL . "/public/index.php?page=logout";
    ?>
    <?php require __DIR__ . '/header_client.php'; ?>
    <?php 

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
                <label>Téléphone</label>
                <input type="text" name="telephone"
                       value="<?= htmlspecialchars($client['telephone_client'] ?? '') ?>">
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

        

        <?php
            $avatarUrl = BASE_URL . ($client['photo'] ?? '/public/assets/clients/img/default.png');
        ?>

        <img class="avatar" src="<?= htmlspecialchars($avatarUrl) ?>" alt="Photo de profil">

        <form method="post"
            action="<?= BASE_URL ?>/public/index.php?page=avatar/upload"
            enctype="multipart/form-data">

            <input type="hidden" name="redirect"
                value="<?= htmlspecialchars(BASE_URL . '/public/index.php?page=client/profil') ?>">

            <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required>
            <button class="btn_login" type="submit">Mettre à jour la photo</button>
        </form>


    </main>

    <?php require __DIR__ . '/../shared/footer.php'; ?>
</body>
</html>
