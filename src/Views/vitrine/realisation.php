<section id="main-realisation" class="realisations">
    <h2>Découvrez nos <span class="highlight">Réalisations</span></h2>

    <div class="container-realisation">
        <?php if (!empty($realisationFavorite) && is_array($realisationFavorite)): ?>
            <?php foreach ($realisationFavorite as $r): ?>
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
            <p>Aucune réalisation favorite pour le moment.</p>
        <?php endif; ?>
    </div>

    <div class="realisations-actions">
        <a class="btn" href="<?= BASE_URL ?>/public/index.php?page=realisation">
            Voir toutes les réalisations
        </a>
    </div>
</section>
