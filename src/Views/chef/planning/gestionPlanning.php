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
<script>
    document.addEventListener('click', function (e) {
        // Si on clique sur le bouton "trois points"
        const toggle = e.target.closest('.slot-menu-toggle');
        const menus  = document.querySelectorAll('.slot-menu');

        if (toggle) {
            const menu = toggle.closest('.slot-menu');

            // Fermer tous les autres
            menus.forEach(m => {
                if (m !== menu) m.classList.remove('open');
            });

            // Toggle celui-ci
            menu.classList.toggle('open');
            return;
        }

        // Si on clique en dehors des menus → tout fermer
        menus.forEach(m => m.classList.remove('open'));
    });
</script>
<body>
<div class="page">
    <?php
        require_once(__DIR__ . '/../shared/header_chef.php');
    ?>

    <div class="app">
        <?php
            
            $menuTitle1 = $menuTitle1 ?? 'Gestion des chantiers';
            $menuTitle2 = $menuTitle2 ?? 'Gestion Employé'; 

            $menu1 = [
              ['label'=>'Nouveau chantier', 'href'=> BASE_URL.'/public/index.php?page=chantier/create'],
            ];
            $menu2 = [
              ['label'=>'Ajouter employé', 'href'=> BASE_URL.'/public/index.php?page=employe/create'],
            ];
            require_once(__DIR__ . '/../../shared/aside.php');
        ?>

        <main class="main-content">

            <section class="board">

                
                <div class="board__toolbar">
                    <div class="chip">
                        <a class="navbtn"
                        href="<?= BASE_URL ?>/public/index.php?page=chef/planning&date=<?= htmlspecialchars($prevMonday) ?>">
                            ‹
                        </a>

                        <strong><?= htmlspecialchars($monthLabel ?? '') ?></strong>

                        <a class="navbtn"
                        href="<?= BASE_URL ?>/public/index.php?page=chef/planning&date=<?= htmlspecialchars($nextMonday) ?>">
                            ›
                        </a>

                    </div>

                    <div class="views">
                        <button type="button">Vue Jour</button>
                        <button type="button" class="active">Vue Hebdo</button>
                        <button type="button">Vue Mensuelle</button>
                    </div>

                    <!-- Formulaire de recherche d’employé -->
                    <form method="get" class="emp-search">
                        <!-- on force la page pour rester sur le planning -->
                        <input type="hidden" name="page" value="chef/planning">
                        <!-- on garde la semaine courante -->
                        <input type="hidden" name="date" value="<?= htmlspecialchars($refDate ?? $joursAffiches[0]) ?>">

                        <input type="text"
                            name="emp"
                            placeholder="Rechercher un employé..."
                            value="<?= htmlspecialchars($searchEmp ?? '') ?>">
                    </form>
                </div>
                <div class="board-body">
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
                                    <!-- avatar simple avec initiales -> On modifiera ensuite avec les photos de profil quand on les aura-->
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

                                            // Construire le label du créneau
                                            if (!empty($slot['nom_poste'])) {
                                                $label = $slot['nom_poste'];
                                            } elseif (!empty($slot['commentaire'])) {
                                                $label = $slot['commentaire'];
                                            } else {
                                                $label = 'Intervention';
                                            }
                                        ?>
                                            <div class="slot">
                                                <div class="slot-main">
                                                    <strong><?= htmlspecialchars($hDeb . '–' . $hFin) ?></strong>
                                                    <small><?= htmlspecialchars($label) ?></small>
                                                </div>
                                                <div class="slot-menu js-slot-menu">
                                                    <div class="slot-menu-panel">
                                                        <!-- Modifier -->
                                                        <a class="slot-menu-item"
                                                            href="<?= BASE_URL ?>/public/index.php?page=chantier/edit&id=<?= (int)($slot['id_creneau'] ?? 0) ?>">
                                                            Modifier
                                                        </a>

                                                        <!-- Supprimer -->
                                                        <form method="post"
                                                            action="<?= BASE_URL ?>/public/index.php?page=chantier/delete">
                                                            <input type="hidden" name="id_creneau" value="<?= (int)($slot['id_creneau'] ?? 0) ?>">
                                                            <button type="submit" class="slot-menu-item slot-menu-danger">
                                                                Supprimer
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <?php require_once(__DIR__ . '/../../shared/footer.php'); ?>
</div>
</body>
</html>
