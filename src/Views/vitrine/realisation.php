<section class="realisations">    
    <h2>Nos <span class="highlight">Réalisations</span></h2>
    <div class="container-realisation">
        <?php if (!empty($realisationFavorite) && is_array($realisationFavorite)): ?>
            <?php foreach ($realisationFavorite as $r): ?>
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
            <p>Aucune réalisation favorite pour le moment.</p>
        <?php endif; ?>
    </div>
    <div>
        <a href="index.php?page=realisation">
            <button>
                Voir toutes les réalisations
            </button>
        </a>
    </div>
</section>
