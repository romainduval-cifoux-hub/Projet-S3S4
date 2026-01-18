<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($pageTitle ?? 'Notifications') ?></title>

  <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/employe/shared/header_employe/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/employe/notifications/style.css" rel="stylesheet">
  
</head>
<body>

<div class="page">
  <?php require __DIR__ . '/shared/header_chef.php'; ?>

  <div class="app">
    <?php
      $menuTitle1 = 'Notifications';
      $menu1 = [
        ['label'=>'Notifications', 'href'=> BASE_URL.'/public/index.php?page=chef/notifications'],
        ['label'=>'Planning', 'href'=> BASE_URL.'/public/index.php?page=chef/planning'],
      ];
      $menuTitle2 = 'Gestion';
      $menu2 = [
        ['label'=>'Demandes de congé', 'href'=> BASE_URL.'/public/index.php?page=chef/conges'],
      ];
      require __DIR__ . '/../shared/aside.php';
    ?>

    <main class="main-content">
      <div class="notif-wrap">
        <div class="notif-top">
          <h1>Notifications</h1>

          <form method="post" action="<?= BASE_URL ?>/public/index.php?page=chef/notifications/read-all">
            <button class="btn secondary" type="submit">Tout marquer comme lu</button>
          </form>
        </div>

        <?php if (empty($notifications)): ?>
          <p>Aucune notification.</p>
        <?php else: ?>
          <div class="notif-list">
            <?php foreach ($notifications as $n): ?>
              <div class="notif <?= ((int)$n['is_read'] === 0) ? 'unread' : '' ?>">
                <div style="flex:1">
                  <h3><?= htmlspecialchars($n['titre']) ?></h3>
                  <p><?= nl2br(htmlspecialchars($n['message'])) ?></p>
                  <small><?= htmlspecialchars(date('d/m/Y H:i', strtotime($n['date_creation']))) ?></small>
                </div>

                <div class="actions">
                  <?php if (!empty($n['lien'])): ?>
                    <a class="btn" href="<?= htmlspecialchars($n['lien']) ?>">Voir</a>
                  <?php endif; ?>

                  <?php if ((int)$n['is_read'] === 0): ?>
                    <form method="post" action="<?= BASE_URL ?>/public/index.php?page=chef/notifications/read">
                      <input type="hidden" name="id_notification" value="<?= (int)$n['id_notification'] ?>">
                      <button class="btn secondary" type="submit">Lu</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    </main>
  </div>

  <?php require __DIR__ . '/../shared/footer.php'; ?>
</div>

</body>
</html>
