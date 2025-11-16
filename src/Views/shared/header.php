<?php 
    $nav = $nav ?? ['Accueil', 'Avis', 'Nos réalisations', 'Contact']; 
    $bouton = $bouton ?? "Se connecter";
    $redirection = $redirection ?? BASE_URL . "/public/index.php?page=login";
?>
<header>
    <div class='header-container'>
        <img id="logo_header" src="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinTexte.png" alt="logo_Team_Jardin">
        
        <nav>
            <ul>
                <?php foreach ($nav as $item): ?>
                    <li><a href="#" target="_blank"><?= htmlspecialchars($item) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>    

        <a href="<?= $redirection ?>" class="btn_login"><?php echo($bouton)  ?></a>
    </div>
</header>