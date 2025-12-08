<?php 
    $nav = $nav ?? ['Accueil', 'Avis', 'Nos réalisations', 'Contact']; 
    $bouton = $bouton ?? "Se connecter";
    $redirection = $redirection ?? BASE_URL . "/public/index.php?page=login";
?>
<header>
    <div class='container'>
        <img id='logo_header' src="<?=  BASE_URL ?>/public/assets/shared/img/logoTeamJardinTexte.png" alt="logo_Team_Jardin">
        <nav>
            <ul>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-accueil">Accueil</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-avis">Avis</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-realisation" >Réalisations</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-contact">Contact</a></li>
            </ul>
        </nav>

        <a href="<?= BASE_URL ?>/public/index.php?page=login" class="btn_login"><?php echo ($bouton) ?></a>
    </div>
</header>