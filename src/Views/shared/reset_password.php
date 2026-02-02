<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
  <meta charset="utf-8">
  <title>Réinitialiser le mot de passe - Team Jardin</title>
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
    <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

    <input class="input" type="password" name="password" placeholder="Nouveau mot de passe" required>
    <input class="input" type="password" name="password2" placeholder="Confirmer le mot de passe" required>

    <button class="btn_connexion" type="submit">Changer le mot de passe</button>
  </form>
</div>

</body>
</html>
