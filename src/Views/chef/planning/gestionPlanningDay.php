<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
  <meta charset="utf-8">
  <title><?= htmlspecialchars($pageTitle ?? 'Planning – Team Jardin') ?></title>

  <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/chef/css/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
</head>

<body>
<div class="page">
  <?php require_once(__DIR__ . '/../shared/header_chef.php'); ?>
  <?php require_once __DIR__ . '/../shared/popup_creneau.php'; ?>

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
      <section class="board">

        <div class="board__toolbar">
          <div class="chip">
            <a class="navbtn"
              href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=day&date=<?= htmlspecialchars($prevDay) ?>">
              ‹
            </a>

            <strong><?= htmlspecialchars(date('d/m/Y', strtotime($date_jour))) ?></strong>

            <a class="navbtn"
              href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=day&date=<?= htmlspecialchars($nextDay) ?>">
              ›
            </a>
          </div>

          <div class="views">
            <a class="btn-view active"
              href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=day&date=<?= htmlspecialchars($date_jour) ?>">
              Vue Jour
            </a>
            <a class="btn-view"
              href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=week&date=<?= htmlspecialchars($date_jour) ?>">
              Vue Hebdo
            </a>
            <a class="btn-view"
              href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=month&date=<?= htmlspecialchars($date_jour) ?>">
              Vue Mensuelle
            </a>
          </div>

          <!-- Recherche employé -->
          <form method="get" class="emp-search">
            <input type="hidden" name="page" value="chef/planning">
            <input type="hidden" name="view" value="day">
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

          <!-- Header colonnes : 1 seule colonne jour -->
          <div class="col-header" style="grid-template-columns:260px 1fr;">
            <div class="hcell"></div>
            <div class="hcell">
              <?= htmlspecialchars($num) ?>
              <small><?= htmlspecialchars($nomFr) ?></small>
            </div>
          </div>

          <!-- Grille : 1 seule colonne jour -->
          <div class="grid">
            <?php foreach ($employes as $emp):
              $idS = (int)$emp['id_salarie'];
              $slots = $dayMatrix[$idS] ?? []; // tableau de créneaux du jour (tous)
            ?>
              <div class="row" style="grid-template-columns:260px 1fr;">
                <div class="emp">
                  <?php
                    $photo = $emp['photo'] ?? '';
                    $src = $photo ? (BASE_URL . $photo) : (BASE_URL . '/public/assets/shared/img/default.png');
                  ?>
                  <img
                    class="avatar"
                    src="<?= htmlspecialchars($src) ?>"
                    onerror="this.src='<?= BASE_URL ?>/public/assets/shared/img/default-pp.png';"
                    alt="photo de profil">

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
                      <div class="slot-main">
                        <strong><?= htmlspecialchars($hDeb . '–' . $hFin) ?></strong>
                        <small><?= htmlspecialchars($label) ?></small>
                      </div>

                      <div class="slot-menu js-slot-menu">
                        <!-- Bouton DÉTAILS -->
                        

                        <div class="slot-menu-panel">
                            <button
                                type="button"
                                class="slot-details-btn slot-menu-item js-open-creneau-modal"
                                data-heures="<?= htmlspecialchars($hDeb . '–' . $hFin) ?>"
                                data-label="<?= htmlspecialchars($label) ?>"
                                data-info="<?= htmlspecialchars($slot['commentaire'] ?? '') ?>"
                                >
                                Détails
                            </button>
                            <a class="slot-menu-item"
                                href="<?= BASE_URL ?>/public/index.php?page=chantier/edit&id=<?= (int)($slot['id_creneau'] ?? 0) ?>">
                                Modifier
                            </a>

                            <form method="post"
                                    action="<?= BASE_URL ?>/public/index.php?page=chantier/delete&date=<?= htmlspecialchars($date_jour) ?>"
                                    class="js-double-confirm">
                                <input type="hidden" name="id_creneau" value="<?= (int)($slot['id_creneau'] ?? 0) ?>">
                                <button type="submit" class="slot-menu-item slot-menu-danger">Supprimer</button>
                            </form>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

              </div>
            <?php endforeach; ?>
          </div>

        </div>
      </section>
    </main>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/chef/shared/js/confirmation-suppression.js"></script>
  <script src="<?= BASE_URL ?>/public/assets/chef/shared/js/creneau-modal.js"></script>

                          
  <?php require_once __DIR__ . '/../../shared/footer.php'; ?>
</div>
</body>
</html>
