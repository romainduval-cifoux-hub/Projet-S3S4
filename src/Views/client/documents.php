<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars($pageTitle ?? 'Mes documents') ?></title>

        <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">

        <link href="<?= BASE_URL ?>/public/assets/clients/documents/style.css" rel="stylesheet">

        <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

        <link href="<?= BASE_URL ?>/public/assets/shared/burger-accueil/css/style.css" rel="stylesheet">
    </head>
    <body>
        <?php 

        require __DIR__ . '/../shared/burger-accueil.php';
        ?>

        <?php
            require_once(__DIR__ . '/shared/header_client.php');
        ?>

        <main>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>

            <?php if (!empty($documents)): ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Règlement</th>
                            <th>Téléchargement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['num']) ?></td>
                                <td><?= htmlspecialchars($doc['typeDoc']) ?></td>
                                <td><?= htmlspecialchars($doc['statusDoc']) ?></td>
                                <td><?= htmlspecialchars($doc['dateDoc']) ?></td>
                                <td><?= htmlspecialchars($doc['reglementDoc']) ?></td>
                                <td>
                                    <!-- Bouton pour télécharger -->
                                    <form method="post" action="<?= BASE_URL ?>/public/download.php" style="display:inline">
                                        <input type="hidden" name="document_id" value="<?= $doc['idDoc'] ?>">
                                        <button type="submit">PDF</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun document disponible pour le moment.</p>
            <?php endif; ?>

        </main>

        <?php
            require_once(__DIR__ . '/../shared/footer.php');
        ?>
    </body>
</html>