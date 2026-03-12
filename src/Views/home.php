<?php require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/Database/db.php';
$pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png" alt="Logo Team Jardin">
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Team Jardin') ?></title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/accueil/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/accueil/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/avis/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/avis/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/page-realisation/style.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/realisation/style.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/contact/style.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/burger-accueil/css/style.css" rel="stylesheet">

</head>

<body>

    <?php
    require_once __DIR__ . '/shared/burger-accueil.php';



    if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? null) === 'client') {
        require_once __DIR__ . '/client/header_client.php';
    } else {
        require_once __DIR__ . '/shared/header.php';
    }




    require_once __DIR__ . '/vitrine/accueil.php';
    require_once __DIR__ . '/vitrine/avis.php';
    require_once __DIR__ . '/vitrine/realisation.php';
    require_once __DIR__ . '/vitrine/contact.php';
    require_once __DIR__ . '/shared/footer.php';
    ?>
</body>

</html>