<section class="realisations">    
    <h2>Nos<span class="highlight">Réalisations</span></h2>
    <div class="container-realisation">
        <?php foreach ($realisation as $r): ?>
            <div class="realisation-item">
                <div class="realisation-image">
                    <img src="<?= htmlspecialchars($r['photo']) ?>" alt="Image de la réalisation">
                    <p class="realisation-commentaire">
                        <?= htmlspecialchars($r['commentaire']) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>