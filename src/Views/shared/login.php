<?php require_once __DIR__ . '/../../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$successMessage = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
    <meta charset="utf-8">
    <title>Login - Team Jardin</title>
    <link href="<?= BASE_URL ?>/public/assets/shared/login/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">

</head>

<body class="page-login">
    <a href="<?= BASE_URL ?>/public/index.php" class="back-arrow">
        <span class="arrow">←</span>
        <span class="text">Retour</span>
    </a>
    <div class="form-container">
        <img id="logo_login" src="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardin.png" alt="Logo Team Jardin">

        <?php if (!empty($successMessage)) : ?>
            <div class="alert-success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($erreur)) : ?>
            <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input class="input" type="text" name="username" placeholder="email@univ-lyon1.fr" required>
            <input class="input" type="password" name="password" placeholder="Mot de passe" required>
            <button class="btn_connexion" type="submit">Connexion</button>

            <a href="<?= BASE_URL ?>/public/index.php?page=forgot_password">Mot de passe oublié ?</a>


            <a href="<?= BASE_URL ?>/public/index.php?page=register">Nouveau ? Créer un compte !</a>
        </form>
    </div>
</body>

</html>