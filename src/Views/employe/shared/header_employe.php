<header>
    <div class='container'>
<a href="<?= BASE_URL ?>/public/index.php#main-accueil"><img id='logo_header' src="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinTexte.png" alt="logo_Team_Jardin"></a>

        <nav>
            <ul>
                <li><a href="<?= BASE_URL ?>/public/index.php?page=employe/profil">Profil</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php?page=employe/planning">Planning</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php?page=employe/conge">Congé</a></li>
            </ul>
        </nav>

        <a class="notif-link" href="<?= BASE_URL ?>/public/index.php?page=employe/notifications">
        
        
            <span class="notif-badge">Notifications : <?= (int)$nbNotifs ?></span>
        
        </a>

        <a href="<?= BASE_URL ?>/public/index.php?page=logout" class="btn_login">
            Déconnexion
        </a>
    </div>
</header>
