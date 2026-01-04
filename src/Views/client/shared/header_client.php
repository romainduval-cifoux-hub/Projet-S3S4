<header>
    <div class='container'>
        <img id="logo_header" 
            src="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinTexte.png" 
            alt="logo_Team_Jardin">

        <nav>
            <ul>
                <li><a href="<?= BASE_URL ?>/public/index.php?page=home">Accueil</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php?page=client/profil">Mon profil</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php?page=client/documents">Mes documents</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php?page=client/commentaires">Mes commentaires</a></li>
            </ul>
        </nav>

        <a href="<?= BASE_URL ?>/public/index.php?page=logout" class="btn_login">
            Déconnexion
        </a>
    </div>
</header>