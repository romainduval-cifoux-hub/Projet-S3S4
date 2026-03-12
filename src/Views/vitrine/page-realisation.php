<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
    <meta charset="UTF-8">
        <link href="<?= BASE_URL ?>/public/assets/vitrine/css/page-realisation/style.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet"> 

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Jardin</title>
</head>
<body>
    
</body>
</html>

<?php if (empty($categoriesWithRealisations) || !is_array($categoriesWithRealisations)): ?>
    <p>Aucune réalisation disponible.</p>
<?php else: ?>

    <?php foreach ($categoriesWithRealisations as $category): ?>
        <section class="realisations">
            <h2><?= htmlspecialchars($category['name'] ?? '') ?></h2>

            <div class="container-realisation">
                <?php if (!empty($category['realisations']) && is_array($category['realisations'])): ?>
                    <?php foreach ($category['realisations'] as $r): ?>
                        <div class="realisation-item">
                            <div class="realisation-image">
                                <img
                                    src="<?= BASE_URL . '/' . ltrim(htmlspecialchars($r['photo'] ?? ''), '/') ?>"
                                    alt="Image de la réalisation"
                                    loading="lazy"
                                >
                                <?php if (!empty($r['commentaire'])): ?>
                                    <p class="realisation-commentaire">
                                        <?= htmlspecialchars($r['commentaire']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune réalisation pour cette catégorie.</p>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>

<?php endif; ?>
