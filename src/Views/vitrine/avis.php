<?php
require_once __DIR__ . '/../../Database/avis.php';

[$avis, $moyenne] = getAvisEtMoyenne($pdo);
?>
<div id="main-avis" class="avis_container">
    <h2>Vous pouvez nous faire <span class="highlight">CONFIANCE</span> </h2>
    <img id="logo-google" src="<?= BASE_URL ?>/public/assets/vitrine/img/Google-logo.png" alt="logo-google">

    <img id="note"
        src="<?= BASE_URL ?>/public/assets/vitrine/img/<?= $moyenne ?>stars-google.png"
        alt="note moyenne">
    <p>Basé sur <strong><?= count($avis) ?> avis</strong></p>
    <?php if (!empty($_SESSION['avis_success'])): ?>
        <div class="alert-avis_success">
            <?= htmlspecialchars($_SESSION['avis_success']) ?>
        </div>
        <?php unset($_SESSION['avis_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['avis_error'])): ?>
        <div class="alert-avis_error">
            <?= htmlspecialchars($_SESSION['avis_error']) ?>
        </div>
        <?php unset($_SESSION['avis_error']); ?>
    <?php endif; ?>



    <div class="avis-slider-wrapper">
        <button class="avis-arrow avis-arrow-left" type="button">‹</button>

        <div class="avis-slider-window">
            <div class="avis-slider">
                <?php foreach ($avis as $a): ?>
                    <div class="avis-slide">
                        <div class="avis-google">
                            <div class="avis-header">
                                <img
                                    class="pp"
                                    src="..<?= htmlspecialchars($a['photo']) ?>"
                                    onerror="this.src='<?= BASE_URL ?>/public/assets/clients/img/default.png';"
                                    alt="photo de profil">

                                <div class="avis-info">
                                    <p class="nom-client">
                                        <?= htmlspecialchars($a['nom'] . ' ' . $a['prenom']) ?>
                                    </p>
                                    <p class="date-avis">
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($a['date']))) ?>
                                    </p>
                                </div>

                                <img class="logo-google"
                                    src="<?= BASE_URL ?>/public/assets/vitrine/img/google-logo-carre.png"
                                    alt="logo Google">
                            </div>

                            <div class="avis-note">
                                <img
                                    src="<?= BASE_URL ?>/public/assets/vitrine/img/<?= (int)$a['note'] ?>stars-google.png"
                                    alt="note <?= (int)$a['note'] ?> étoiles"
                                    class="etoiles">
                                <img src="<?= BASE_URL ?>/public/assets/vitrine/img/certif.png" class="verifie">
                            </div>


                            <p class="commentaire-client">
                                <?= htmlspecialchars($a['commentaire']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="avis-arrow avis-arrow-right" type="button">›</button>
    </div>
    <div class="avis-leave-review">
        <button type="button" id="btn-laisser-avis">Laisser un avis</button>
    </div>

    <!-- Popup (modal) -->
    <div id="avis-modal" class="avis-modal-overlay">
        <div class="avis-modal">
            <button type="button" class="avis-modal-close">&times;</button>
            <h3>Laisser un avis</h3>
            <form action="index.php?page=avis_add" method="post">
                <div class="form-group">
                    <label for="note-avis">Note :</label>
                    <select name="note" id="note-avis" required>
                        <option value="">Choisir…</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Très bien</option>
                        <option value="3">3 - Moyen</option>
                        <option value="2">2 - Passable</option>
                        <option value="1">1 - Mauvais</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="commentaire">Commentaire :</label>
                    <textarea name="commentaire" id="commentaire" rows="4" required></textarea>
                </div>

                <button type="button" id="submit-avis-btn">Envoyer mon avis</button>
            </form>
        </div>
    </div>
    <!-- Popup (modal) de connexion requise -->
    <div id="login-modal" class="avis-modal-overlay">
        <div class="avis-modal">
            <button type="button" class="avis-modal-close">&times;</button>
            <h3>Connexion requise</h3>
            <p>Vous devez être connecté pour laisser un avis.</p>
            <button type="button" id="btn-go-login">Se connecter</button>
        </div>
    </div>


</div>
<script>
    // ---- Infos de session passées depuis PHP ----
    window.IS_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    window.IS_CLIENT = <?= (isset($_SESSION['user_id']) && (isset($_SESSION['role']) && $_SESSION['role'] === 'client')) ? 'true' : 'false' ?>;
    window.USER_ROLE = '<?= $_SESSION['role'] ?? '' ?>';
    window.LOGIN_URL = "<?= BASE_URL ?>/public/index.php?page=login&redirect=home";
</script>

<script src="<?= BASE_URL ?>/public/assets/vitrine/js/avis-slider.js"></script>
<script src="<?= BASE_URL ?>/public/assets/vitrine/js/avis-modal.js"></script>