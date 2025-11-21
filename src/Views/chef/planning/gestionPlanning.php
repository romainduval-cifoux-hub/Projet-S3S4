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
</head>
<body>
<div class="page">
    <?php
        $nav = ['Tableau de bord','Facturation','Planning'];
        $bouton = 'Déconnexion';
        $redirection = BASE_URL . '/public/index.php?page=logout';
        require_once(__DIR__ . '/../../shared/header.php');
    ?>

    <div class="app">
        <?php
            
            $menu1 = [
              ['label'=>'Nouveau chantier', 'href'=> BASE_URL.'/public/index.php?page=chantier/create'],
              ['label'=>'Éditer chantier',  'href'=> BASE_URL.'/public/index.php?page=chantier/list'],
            ];
            $menu2 = [
              ['label'=>'Ajouter employé', 'href'=> BASE_URL.'/public/index.php?page=employe/create'],
              ['label'=>'Éditer employé',  'href'=> BASE_URL.'/public/index.php?page=employe/list'],
            ];
            require_once(__DIR__ . '/../../shared/aside.php');
        ?>

        <main class="main-content">

            <section class="board">

                
                <div class="board__toolbar">
                    <div class="chip">
                        <a class="navbtn"
                        href="<?= BASE_URL ?>/public/index.php?page=planning&date=<?= htmlspecialchars($prevMonday) ?>">
                            ‹
                        </a>

                        <a class="navbtn"
                        href="<?= BASE_URL ?>/public/index.php?page=planning&date=<?= htmlspecialchars($nextMonday) ?>">
                            ›
                        </a>

                    </div>

                    <div class="views">
                        <button type="button">Vue Jour</button>
                        <button type="button" class="active">Vue Hebdo</button>
                        <button type="button">Vue Mensuelle</button>
                    </div>
                </div>

                <!-- Header colonnes (jours) -->
                <?php
                    // noms des jours
                    $joursNoms = ['Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'];
                ?>
                <div class="col-header">
                    <div class="hcell"></div>
                    <?php foreach ($joursAffiches as $day): 
                        $ts = strtotime($day);
                        $num = date('d', $ts);
                        $eng = date('l', $ts);
                        $nomFr = $joursNoms[$eng] ?? $eng;
                    ?>
                        <div class="hcell">
                            <?= htmlspecialchars($num) ?>
                            <small><?= htmlspecialchars($nomFr) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Grille -->
                <div class="grid">
                    <?php foreach ($employes as $emp): 
                        $idS = (int)$emp['id_salarie'];
                    ?>
                        <div class="row">
                            <!-- Colonne employé -->
                            <div class="emp">
                                <!-- avatar simple avec initiales -->
                                <div class="avatar">
                                    <?= htmlspecialchars(mb_substr($emp['prenom_salarie'], 0, 1) . mb_substr($emp['nom_salarie'], 0, 1)) ?>
                                </div>
                                <div class="emp-name">
                                    <?= htmlspecialchars($emp['prenom_salarie'] . ' ' . $emp['nom_salarie']) ?>
                                </div>
                                <!-- bouton options -->
                                <button class="dots" title="Options">⋮</button>
                            </div>

                            <!-- Colonnes jours -->
                            <?php foreach ($joursAffiches as $day): 
                                $daySlots = $slotsMatrix[$idS][$day] ?? [];
                            ?>
                                <div class="cell">
                                    <?php foreach ($daySlots as $slot): 
                                        $hDeb = substr($slot['heure_debut'], 0, 5);
                                        $hFin = substr($slot['heure_fin'], 0, 5);
                                        $label = $slot['nom_poste'] ?? $slot['commentaire'] ?? 'Intervention';
                                    ?>
                                        <div class="slot">
                                            <strong><?= htmlspecialchars($hDeb . '–' . $hFin) ?></strong>
                                            <small><?= htmlspecialchars($label) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>

    <?php require_once(__DIR__ . '/../../shared/footer.php'); ?>
</div>
</body>
</html>
