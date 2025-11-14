<?php require_once __DIR__ . '/../../src/config.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
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

</head>

<body>
    



    <?php
    
    $nav = $nav ?? ['Accueil', 'Avis', 'Nos réalisations', 'Contact']; 
    $bouton = $bouton ?? "Se connecter";
    $redirection = $redirection ?? BASE_URL . "/public/index.php?page=login";
    require_once __DIR__ . '/shared/header.php';

    require_once __DIR__ . '/vitrine/accueil.php';

    $avis = [
        [
            'nom' => 'Luis Vasconcelos',
            'date' => '2025-06-02',
            'commentaire' => 'Très satisfait avec les travaux, la manutention et l’attention envers nous.',
            'photo' => BASE_URL . '/public/assets/vitrine/img/pp1.jpg'
        ],
        [
            'nom' => 'Sophie Martin',
            'date' => '2025-07-15',
            'commentaire' => 'Service impeccable, très professionnel et ponctuel.',
            'photo' => BASE_URL . '/public/assets/vitrine/img/pp1.jpg'
        ],
        [
            'nom' => 'Karim Benali',
            'date' => '2025-09-03',
            'commentaire' => 'Travail soigné et équipe sympathique, je recommande fortement !',
            'photo' => BASE_URL . '/public/assets/vitrine/img/pp1.jpg'
        ]
    ];


    require_once __DIR__ . '/vitrine/avis.php';
    require_once __DIR__ . '/vitrine/realisation.php';
    require_once __DIR__ . '/vitrine/contact.php';
    require_once __DIR__ . '/shared/footer.php';
    ?>
</body>
</html>
