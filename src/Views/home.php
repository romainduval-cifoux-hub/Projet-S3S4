<?php require_once __DIR__ . '/../../src/config.php'; 
require_once __DIR__ . '/../../src/Database/db.php';
$pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Team Jardin') ?></title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet"> 

    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">

<link href="<?= BASE_URL ?>/public/assets/vitrine/css/accueil/style.php" rel="stylesheet">    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/accueil/position.css" rel="stylesheet">
    
    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/avis/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/avis/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/page-realisation/style.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/realisation/style.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/vitrine/css/contact/style.css" rel="stylesheet">

</head>

<body>

    <?php
    require_once __DIR__ . '/shared/header.php';
    require_once __DIR__ . '/vitrine/accueil.php';

$sql = "
    SELECT 
        c.nom_client AS nom,
        c.prenom_client AS prenom,
        a.date_commentaire AS date,
        a.commentaire,
        a.note
    FROM avis a
    INNER JOIN users u ON a.user_id = u.id
    INNER JOIN clients c ON c.id_client = u.id
    ORDER BY a.date_commentaire DESC
";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $avis = $stmt->fetchAll();

    foreach ($avis as &$item) {
        $item['photo'] = BASE_URL . '/public/assets/vitrine/img/pp1.jpg';
    }
    unset($item); 

    $moyenne = 0;
    if (count($avis) > 0) {
        $totalNotes = 0;
        foreach ($avis as $a) {
            $totalNotes += (int)$a['note'];
        }
        $moyenne = round($totalNotes / count($avis)); // entier entre 1 et 5
    }


    require_once __DIR__ . '/vitrine/avis.php';
    require_once __DIR__ . '/vitrine/realisation.php';
    require_once __DIR__ . '/vitrine/contact.php';
    require_once __DIR__ . '/shared/footer.php';
    ?>
</body>
</html>
