<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
    <html lang="fr">
    <head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
        <meta charset="utf-8">
        <title><?= htmlspecialchars($pageTitle ?? 'Mes commentaires') ?></title>

        <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">

        <link href="<?= BASE_URL ?>/public/assets/clients/commentaires/style.css" rel="stylesheet">

        <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

        <link href="<?= BASE_URL ?>/public/assets/shared/burger-accueil/css/style.css" rel="stylesheet">
    </head>
    <body>
        <?php 

        require __DIR__ . '/../shared/burger-accueil.php';
        ?>

        <?php

        $bouton = "Déconnexion";
        $redirection = BASE_URL . "/public/index.php?page=logout";
            require_once(__DIR__ . '/header_client.php');
        ?>

        <main>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>

            <?php if (!empty($commentaires)): ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th>Supprimer votre commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commentaires as $commentaire): ?>
                            <tr>
                                <td><?= htmlspecialchars($commentaire['date_commentaire']) ?></td>
                                <td><?= htmlspecialchars($commentaire['note']) ?></td>
                                <td><?= htmlspecialchars($commentaire['commentaire']) ?></td>
                                <td>
                                    <!-- Bouton pour supprimer -->
                                    <form method="post" action="<?= BASE_URL ?>/public/index.php?page=client/supprimer_commentaire" >
                                        <input type="hidden" name="commentaire_id" value="<?= htmlspecialchars($commentaire['id']) ?>">
                                        <button type="submit">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun commentaire disponible pour le moment.</p>
            <?php endif; ?>
        </main>

        <?php
            require_once(__DIR__ . '/../shared/footer.php');
        ?>
    </body>
</html>