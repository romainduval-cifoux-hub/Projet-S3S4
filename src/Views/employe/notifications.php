<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
  <meta charset="utf-8">
  <title><?= htmlspecialchars($pageTitle ?? 'Mes notifications') ?></title>

  <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/employe/shared/header_employe/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/employe/notifications/style.css" rel="stylesheet">

  
</head>
<body>

<section class="page">
  <?php
    $nav = ['Espace Personnel','Messagerie','Mon planning'];
    $bouton = 'Déconnexion';
    $redirection = BASE_URL . '/public/index.php?page=logout';
    require __DIR__ . '/shared/header_employe.php';
  ?>

  <div class="app">
    <?php
      $menuTitle1 = 'Notifications';
      $menu1 = [
        ['label' => 'Mes notifications', 'href' => BASE_URL . '/public/index.php?page=employe/notifications'],
        ['label' => 'Mon planning', 'href' => BASE_URL . '/public/index.php?page=employe/planning'],
      ];
      $menuTitle2 = 'Mon compte';
      $menu2 = [
        ['label' => 'Mes informations', 'href' => BASE_URL . '/public/index.php?page=employe/profil'],
      ];
      require __DIR__ . '/../shared/aside.php';
    ?>

    <main class="main-content">
      <main>
        <div class="notif-top">
          <h1>Mes notifications</h1>

          <form method="post" action="<?= BASE_URL ?>/public/index.php?page=employe/notifications/read-all">
            <button type="submit" class="btn secondary">Tout marquer comme lu</button>
          </form>
        </div>

        <?php if (empty($notifications)): ?>
          <p>Aucune notification pour le moment.</p>
        <?php else: ?>
          <div class="notif-list">
            <?php foreach ($notifications as $n): ?>
              <div class="notif <?= ((int)$n['is_read'] === 0) ? 'unread' : '' ?>">
                <!-- <div class="notif-type"> htmlspecialchars($n['type']) ?></div> -->

                <div class="notif-body">
                  <p class="notif-title"><?= htmlspecialchars($n['titre']) ?></p>
                  <p class="notif-msg"><?= nl2br(htmlspecialchars($n['message'])) ?></p>

                  <div class="notif-meta">
                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($n['date_creation']))) ?>
                  </div>
                </div>

                <div class="notif-actions">
                  <?php if (!empty($n['lien'])): ?>
                    <a class="btn" href="<?= htmlspecialchars($n['lien']) ?>">Voir</a>
                  <?php endif; ?>

                  <?php if ((int)$n['is_read'] === 0): ?>
                    <form method="post" action="<?= BASE_URL ?>/public/index.php?page=employe/notifications/read">
                      <input type="hidden" name="id_notification" value="<?= (int)$n['id_notification'] ?>">
                      <button type="submit" class="btn secondary">Marquer lu</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </main>
    </main>
  </div>

  <?php require __DIR__ . '/../shared/footer.php'; ?>
</section>

</body>
</html>
