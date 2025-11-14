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
</section>

<div style="text-align: center; margin: 20px 0;">
    <a href="index.php?page=realisation">
        <button style="background-color: #367048; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            Voir toutes les réalisations
        </button>
    </a>
</div>
