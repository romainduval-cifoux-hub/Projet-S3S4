<?php require_once __DIR__ . '/../../config.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Mon profil employé') ?></title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/employe/css/position.css" rel="stylesheet">
    
    <link href="<?= BASE_URL ?>/public/assets/clients/profil/style.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
</head>

<body>
<?php
    $bouton = "Déconnexion";
    $redirection = BASE_URL . "/public/index.php?page=logout";
    require __DIR__ . '/shared/header_employe.php';
?>

<main>
    <h1>Mon profil employé</h1>

    <?php if (!empty($errors ?? [])): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php $isNew = empty($salarie); ?>

    <form method="post" action="<?= BASE_URL ?>/public/index.php?page=employe/profil/save">

        <div class="form-row">
            <label>Nom</label>
            <input type="text" name="nom" required
                   value="<?= htmlspecialchars($salarie['nom_salarie'] ?? '') ?>">
        </div>

        <div class="form-row">
            <label>Prénom</label>
            <input type="text" name="prenom" required
                   value="<?= htmlspecialchars($salarie['prenom_salarie'] ?? '') ?>">
        </div>

        <div class="form-row">
            <label>Email</label>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($salarie['email_salarie'] ?? '') ?>">
        </div>

        <div class="form-row">
            <label>Téléphone</label>
            <input type="text" name="telephone"
                   value="<?= htmlspecialchars($salarie['telephone_salarie'] ?? '') ?>">
        </div>

        <div class="form-row">
            <label>Adresse</label>
            <input type="text" name="adresse"
                   value="<?= htmlspecialchars($salarie['adresse_salarie'] ?? '') ?>">
        </div>

        <button type="submit" class="btn_login">
            <?= $isNew ? "Créer mon profil" : "Mettre à jour mes informations" ?>
        </button>
    </form>
</main>

<?php require __DIR__ . '/../shared/footer.php'; ?>
</body>
</html>
