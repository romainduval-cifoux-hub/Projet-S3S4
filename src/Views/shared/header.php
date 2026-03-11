<header>
    <div class='container'>
        <a href="<?= BASE_URL ?>/public/index.php#main-accueil">
            <img id='logo_header' src="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinTexte.png" alt="logo_Team_Jardin">
        </a>

        <nav id="nav-menu">
            <ul>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-accueil">Accueil</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-avis">Avis</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-realisation">Réalisations</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-contact">Contact</a></li>
            </ul>
        </nav>

        <a href="<?= BASE_URL ?>/public/index.php?page=login" class="btn_login">
            <?= htmlspecialchars($bouton) ?>
        </a>

        <!-- Burger -->
        <div class="burger" id="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>

    </div>
</header>