<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($pageTitle ?? 'Planning – Team Jardin') ?></title>

  <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/chef/css/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

  <!-- CSS mensuel -->
  <link href="<?= BASE_URL ?>/public/assets/chef/planning/vueMois/style.css" rel="stylesheet">
</head>

<body>
<div class="page">
  <?php require_once(__DIR__ . '/../shared/header_chef.php'); ?>

  <div class="app">
    <?php
      $menuTitle1 = $menuTitle1 ?? 'Gestion des chantiers';
      $menuTitle2 = $menuTitle2 ?? 'Gestion Employé';

      $menu1 = [
        ['label'=>'Nouveau chantier', 'href'=> BASE_URL.'/public/index.php?page=chantier/create'],
      ];
      $menu2 = [
        ['label'=>'Ajouter employé', 'href'=> BASE_URL.'/public/index.php?page=employe/create'],
        ['label'=>'Demandes de congé', 'href'=> BASE_URL.'/public/index.php?page=chef/conges'],
      ];
      require_once(__DIR__ . '/../../shared/aside.php');
    ?>

    <main class="main-content">
      <section class="board month-board">

        <div class="month-toolbar">
        <div class = "chip">
          <a class="navbtn"
             href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=month&date=<?= htmlspecialchars($prevMonth) ?>">
            ‹
          </a>

          <strong class="month-title"><?= htmlspecialchars($monthLabel) ?></strong>

          <a class="navbtn"
             href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=month&date=<?= htmlspecialchars($nextMonth) ?>">
            ›
          </a>
        </div>
          <div class="views month-views">
            <a class="btn-view"
                href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=day&date=<?= htmlspecialchars($refDate) ?>">
                Vue Jour
            </a>

            <a class="btn-view"
                href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=week&date=<?= htmlspecialchars($refDate) ?>">
                Vue Hebdo
            </a>

            <a class="btn-view active"
                href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=month&date=<?= htmlspecialchars($refDate) ?>">
                Vue Mensuelle
            </a>
            </div>

        </div>

        <?php
            $weekdays = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];

            $startTs = strtotime($monthStart);
            $endTs   = strtotime($monthEnd);

            // Décalage: lundi = 1, dimanche = 7
            $dow = (int)date('N', $startTs); // 1..7
            $padBefore = $dow - 1; // nb de cases vides avant le 1er
            $totalDays = (int)date('t', $startTs);

            // Total cases = on arrondit à la semaine
            $totalCells = $padBefore + $totalDays;
            $padAfter = (7 - ($totalCells % 7)) % 7;
            $totalCells += $padAfter;

            $today = date('Y-m-d');
            
            $refDate = $_GET['date'] ?? $today;
            $refDate = date('Y-m-d', strtotime($refDate)); // sécurité format

        ?>

        <div class="month-grid">
          <!-- header jours -->
          <?php foreach ($weekdays as $wd): ?>
            <div class="month-head"><?= htmlspecialchars($wd) ?></div>
          <?php endforeach; ?>

          <!-- cases vides avant -->
          <?php for ($i=0; $i<$padBefore; $i++): ?>
            <div class="month-cell month-cell--empty"></div>
          <?php endfor; ?>

          

          <!-- jours du mois -->
          <?php 
            $year  = (int)date('Y', strtotime($monthStart));
            $month = (int)date('n', strtotime($monthStart));

          for ($day=1; $day<=$totalDays; $day++):
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $nb = $counts[$date] ?? 0;

            $isToday = ($date === $today);
            $occupied = ($nb > 0);
          ?>
            <a class="month-cell <?= $occupied ? 'month-cell--busy' : '' ?> <?= $isToday ? 'month-cell--today' : '' ?>"
               href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=day&date=<?= htmlspecialchars($date) ?>">
              <div class="month-day"><?= (int)$day ?></div>

              <?php if ($nb > 0): ?>
                <div class="month-badge"><?= (int)$nb ?> créneau<?= $nb>1?'x':'' ?></div>
              <?php endif; ?>
            </a>
          <?php endfor; ?>

          <!-- cases vides après -->
          <?php for ($i=0; $i<$padAfter; $i++): ?>
            <div class="month-cell month-cell--empty"></div>
          <?php endfor; ?>
        </div>

      </section>
    </main>
  </div>

  <?php require_once __DIR__ . '/../../shared/footer.php'; ?>
</div>
</body>
</html>
