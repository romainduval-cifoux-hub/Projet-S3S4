<link href="<?= BASE_URL ?>/public/assets/vitrine/css/page-realisation/style.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">

<?php foreach ($categoriesWithRealisations as $category): ?>
    <section class="realisations">
        <h2><?php echo htmlspecialchars($category['name']); ?></h2>
        <div class="container-realisation">
            <?php if (!empty($category['realisations'])): ?>
                <?php foreach ($category['realisations'] as $r): ?>
                    <div class="realisation-item">
                        <div class="realisation-image">
                            <img src="/Projet-S3S4/<?= htmlspecialchars($r['photo']) ?>" alt="Image de la réalisation">
                            <p class="realisation-commentaire">
                                <?= htmlspecialchars($r['commentaire']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune réalisation pour cette catégorie.</p>
            <?php endif; ?>
        </div>
    </section>
<?php endforeach; ?>