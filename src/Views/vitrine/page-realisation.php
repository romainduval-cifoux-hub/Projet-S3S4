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
