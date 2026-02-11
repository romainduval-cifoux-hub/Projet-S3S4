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
    <link href="<?= BASE_URL ?>/public/assets/clients/avatar/style.css" rel="stylesheet">


    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/burger-accueil/css/style.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

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

        <?php
        $defaultPhoto = '/public/assets/clients/img/default.png';

        $photo = $client['photo'] ?? '';

        $avatarUrl = (
            $photo !== '' &&
            $photo !== $defaultPhoto
        )
            ? rtrim(BASE_URL, '/') . '/' . ltrim($photo, '/')
            : rtrim(BASE_URL, '/') . $defaultPhoto;
        ?>



        <form method="post"
            action="<?= BASE_URL ?>/public/index.php?page=avatar/upload"
            class="avatar-form">

            <input type="hidden" name="redirect"
                value="<?= BASE_URL ?>/public/index.php?page=client/profil">

            <label class="avatar-upload">
                <input type="file" id="avatarInput" accept="image/*">
                Changer la photo
            </label>

            <div class="crop-container">
                <img id="avatarPreview" alt="">
            </div>

            <div>
                
                <img
                    class="avatar-current"
                    src="<?= htmlspecialchars($avatarUrl) ?>"
                    alt=""
                    onerror="this.onerror=null; this.src='<?= rtrim(BASE_URL,'/') ?>/public/assets/clients/img/default.png';"
                />
            
            </div>


            <input type="hidden" name="croppedImage" id="croppedImage">

            <button type="submit" class="btn_login">Enregistrer</button>
        </form>



    </main>

    <?php require __DIR__ . '/../shared/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/shared/avatar/avatar-cropper.js"></script>

</body>
</html>
