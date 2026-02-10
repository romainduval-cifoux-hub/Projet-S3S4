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
              ['label'=>'Demandes de congé', 'href'=> BASE_URL.'/public/index.php?page=chef/conges'],
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
                        <a class="btn-view"
                            href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=day&date=<?= htmlspecialchars($refDate ?? $joursAffiches[0]) ?>">
                            Vue Jour
                        </a>

                        <a class="btn-view active"
                            href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=week&date=<?= htmlspecialchars($refDate ?? $joursAffiches[0]) ?>">
                            Vue Hebdo
                        </a>

                        <a class="btn-view"
                            href="<?= BASE_URL ?>/public/index.php?page=chef/planning&view=month&date=<?= htmlspecialchars($refDate ?? $joursAffiches[0]) ?>">
                            Vue Mensuelle
                        </a>
                    </div>


                    <!-- Formulaire de recherche d’employé -->
                    <form method="get" class="emp-search">
                        <!-- forcage de la page pour rester sur le planning -->
                        <input type="hidden" name="page" value="chef/planning">
                        <!-- semaine courante -->
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
                                    
                                    
                                    <?php
                                        
                                        $photo = $emp['photo'] ?? '';

                                        
                                        $src = $photo
                                            ? BASE_URL . $photo
                                            : BASE_URL . '/public/assets/shared/img/default.png';
                                    ?>
                                    <img
                                        class="avatar"
                                        src="<?= htmlspecialchars($src) ?>"
                                        alt="Photo de profil"
                                        onerror="this.src='<?= BASE_URL ?>/public/assets/shared/img/default.png';"
                                    />

                                    <div class="emp-name">
                                        <?= htmlspecialchars($emp['prenom_salarie'] . ' ' . $emp['nom_salarie']) ?>
                                    </div>
                                    
                                </div>

                                <!-- Colonnes jours -->
                                <?php foreach ($joursAffiches as $day): 
                                    $daySlots = $slotsMatrix[$idS][$day] ?? [];
                                ?>
                                    <div class="cell">
                                        <?php foreach ($daySlots as $slot): 
                                            $hDeb = substr($slot['heure_debut'], 0, 5);
                                            $hFin = substr($slot['heure_fin'], 0, 5);

                                            if (!empty($slot['label'])) {
                                                $label = $slot['label'];
                                            } elseif (!empty($slot['commentaire'])) {
                                                $label = $slot['commentaire'];
                                            } else {
                                                $label = 'Intervention';
                                            }
                                        ?>


                                            <?php
                                                $slotClass = 'slot';

                                                if (($slot['heure_debut'] ?? '') === '08:00:00') $slotClass .= ' slot--am';
                                                if (($slot['heure_debut'] ?? '') === '13:00:00') $slotClass .= ' slot--pm';

                                                
                                                if (($slot['type_travail'] ?? '') === 'conge') $slotClass .= ' slot--conge';

                                                
                                                if (stripos($label, 'congé') !== false) $slotClass .= ' slot--conge';
                                            ?>
                                                <div class="<?= htmlspecialchars($slotClass) ?> js-slot-open"
                                                    data-heures="<?= htmlspecialchars($hDeb . '–' . $hFin) ?>"
                                                    data-label="<?= htmlspecialchars($label) ?>"
                                                    data-info="<?= htmlspecialchars($slot['commentaire'] ?? '') ?>"
                                                >
                                                <div class="slot-main">
                                                    <strong><?= htmlspecialchars($hDeb . '–' . $hFin) ?></strong>
                                                    <small><?= htmlspecialchars($label) ?></small>
                                                </div>
                                                <div class="slot-menu js-slot-menu">
                                                    <div class="slot-menu-panel">
                                                        <!-- Bouton DÉTAILS -->
                                                        <button
                                                            type="button"
                                                            class="slot-details-btn slot-menu-item js-open-creneau-modal"
                                                            data-heures="<?= htmlspecialchars($hDeb . '–' . $hFin) ?>"
                                                            data-label="<?= htmlspecialchars($label) ?>"
                                                            data-info="<?= htmlspecialchars($slot['commentaire'] ?? '') ?>"
                                                        >
                                                            Détails
                                                        </button>

                                                        <!-- Modifier -->
                                                        <a class="slot-menu-item js-no-modal"
                                                            href="<?= BASE_URL ?>/public/index.php?page=chantier/edit&id=<?= (int)($slot['id_creneau'] ?? 0) ?>">
                                                            Modifier
                                                        </a>

                                                        <!-- Supprimer -->
                                                        <form method="post"
                                                            action="<?= BASE_URL ?>/public/index.php?page=chantier/delete"
                                                            class="js-double-confirm js-no-modal"
                                                        >
                                                        <input type="hidden" name="id_creneau" value="<?= (int)($slot['id_creneau'] ?? 0) ?>">

                                                        <!-- 1er clic -->
                                                        <button type="button" class="slot-menu-item slot-menu-danger js-ask-delete">
                                                            🗑
                                                        </button>

                                                        <!-- 2e validation -->
                                                        <div class="slot-confirm" hidden>
                                                            <button type="submit" class="slot-menu-item slot-menu-danger js-no-modal">
                                                            Confirmer
                                                            </button>
                                                            <button type="button" class="slot-menu-item js-cancel-delete js-no-modal">
                                                            Annuler
                                                            </button>
                                                        </div>
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
    <script src="<?= BASE_URL ?>/public/assets/chef/shared/js/creneau-modal.js"></script>     
    <script src="<?= BASE_URL ?>/public/assets/chef/shared/js/confirmation-suppression.js"></script>                                       
    <?php 
    require_once __DIR__ . '/../shared/popup_creneau.php'; 
    require_once __DIR__ . '/../../shared/footer.php'; 
    ?>
</div>
</body>
</html>
