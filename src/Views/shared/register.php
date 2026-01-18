<?php
require_once __DIR__ . '/../../../src/config.php';
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Login - Team Jardin</title>
    <link href="<?= BASE_URL ?>/public/assets/shared/login/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
</head>

<body class="page-login">
    <a href="<?= BASE_URL ?>/public/index.php?page=login" class="back-arrow">
    <span class="arrow">←</span>
    <span class="text">Retour</span>
</a>
    <div class="form-container">
        



        <img id="logo_login" src="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardin.png" alt="Logo Team Jardin">
        <?php if (!empty($erreur)) : ?>
            <div class="alert-error">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input class="input"
                type="text"
                name="username"
                placeholder="email@univ-lyon1.fr"
                value="<?= htmlspecialchars($username ?? '') ?>"
                required> <input class="input" type="password" name="password" placeholder="Mot de passe" required>
            <input class="input" type="password" name="password2" placeholder="Confirmer le mot de passe" required>
            <button class="btn_connexion" type="submit">Créer le compte</button>

        </form>
    </div>

</body>

</html>