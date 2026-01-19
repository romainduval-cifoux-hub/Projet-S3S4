<?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'client'): ?>

<!-- Bouton flottant burger -->
<button class="quick-menu-btn" id="quickMenuToggle" aria-label="Menu rapide">
    ☰
</button>

<!-- Fond sombre -->
<div class="quick-menu-backdrop" id="quickMenuBackdrop"></div>

<!-- Panneau coulissant -->
<aside class="quick-menu" id="quickMenu">
    <h3>Mon espace</h3>

    <nav>
        <a href="<?= BASE_URL ?>/public/index.php?page=client/profil">Mon profil</a>
        <a href="<?= BASE_URL ?>/public/index.php?page=client/documents">Mes documents</a>
        <a href="<?= BASE_URL ?>/public/index.php?page=client/commentaires">Mes commentaires</a>
    </nav>
</aside>


<script src="<?= BASE_URL ?>/public/assets/shared/burger-accueil/js/burger-accueil.js"></script>

<?php endif; ?>
