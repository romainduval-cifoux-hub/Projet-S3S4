<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
  <meta charset="utf-8">
  <title>Mot de passe oublié - Team Jardin</title>
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

  <?php if (!empty($message)) : ?>
    <div class="alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if (!empty($erreur)) : ?>
    <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input class="input" type="text" name="username" placeholder="email@univ-lyon1.fr" required>
    <button class="btn_connexion" type="submit">Envoyer le lien</button>
  </form>
</div>

</body>
</html>
