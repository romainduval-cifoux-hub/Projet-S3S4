<?php
// Board-only (pour iframe)
// attend: $date_jour, $prevDay, $nextDay, $employes, $dayMatrix, $searchEmp
?>

<link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/public/assets/chef/css/style.css" rel="stylesheet">

<section class="board" style="margin:0;">
  <div class="board__toolbar">
    <div class="chip">
      <a class="navbtn"
         href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=day&date=<?= htmlspecialchars($prevDay) ?>&embed=1">‹</a>

      <strong><?= htmlspecialchars(date('d/m/Y', strtotime($date_jour))) ?></strong>

      <a class="navbtn"
         href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=day&date=<?= htmlspecialchars($nextDay) ?>&embed=1">›</a>
    </div>

    <!-- Recherche employé -->
    <form method="get" class="emp-search" style="margin-left:auto;">
      <input type="hidden" name="page" value="chef/planning">
      <input type="hidden" name="view" value="day">
      <input type="hidden" name="embed" value="1">
      <input type="hidden" name="date" value="<?= htmlspecialchars($date_jour) ?>">
      <input type="text"
             name="emp"
             placeholder="Rechercher un employé..."
             value="<?= htmlspecialchars($searchEmp ?? '') ?>">
    </form>
  </div>

  <div class="board-body">
    <?php
      $joursNoms = [
        'Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi',
        'Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'
      ];
      $ts = strtotime($date_jour);
      $num = date('d', $ts);
      $nomFr = $joursNoms[date('l',$ts)] ?? date('l',$ts);
    ?>

    <div class="col-header" style="grid-template-columns:260px 1fr;">
      <div class="hcell"></div>
      <div class="hcell">
        <?= htmlspecialchars($num) ?>
        <small><?= htmlspecialchars($nomFr) ?></small>
      </div>
    </div>

    <div class="grid">
      <?php foreach ($employes as $emp):
        $idS = (int)$emp['id_salarie'];
        $slots = $dayMatrix[$idS] ?? [];
      ?>
        <div class="row" style="grid-template-columns:260px 1fr;">
          <div class="emp">
            <div class="emp-name">
              <?= htmlspecialchars($emp['prenom_salarie'] . ' ' . $emp['nom_salarie']) ?>
            </div>
          </div>

          <div class="cell">
            <?php foreach ($slots as $slot):
              $hDeb = substr($slot['heure_debut'], 0, 5);
              $hFin = substr($slot['heure_fin'], 0, 5);

              if (!empty($slot['label'])) $label = $slot['label'];
              elseif (!empty($slot['commentaire'])) $label = $slot['commentaire'];
              else $label = 'Intervention';
            ?>
              <div class="slot">
                <strong><?= htmlspecialchars($hDeb . '–' . $hFin) ?></strong>
                <small><?= htmlspecialchars($label) ?></small>
              </div>
            <?php endforeach; ?>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
