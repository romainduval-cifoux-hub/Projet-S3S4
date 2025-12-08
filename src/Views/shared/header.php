<?php 
    $nav = $nav ?? ['Accueil', 'Avis', 'Nos réalisations', 'Contact']; 
    $bouton = $bouton ?? "Se connecter";

    $redirection = $redirection ?? ($_SERVER['REQUEST_URI'] ?? BASE_URL . "/public/index.php");
?>
<header>
    <div class='container'>
        <img id='logo_header' src="<?=  BASE_URL ?>/public/assets/shared/img/logoTeamJardinTexte.png" alt="logo_Team_Jardin">
        <nav>
            <ul>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-accueil">Accueil</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-avis">Avis</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-realisation">Réalisations</a></li>
                <li><a href="<?= BASE_URL ?>/public/index.php#main-contact">Contact</a></li>
            </ul>
        </nav>

        <a href="<?= BASE_URL ?>/public/index.php?page=login&redirect=<?= urlencode($redirection) ?>" 
           class="btn_login">
            <?= htmlspecialchars($bouton) ?>
        </a>
    </div>
</header>
