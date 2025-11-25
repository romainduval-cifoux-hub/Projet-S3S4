<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Mon planning') ?></title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/css/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
</head>
<body>
<div class="page">
    <?php
        // header simplifié “employé”
        $nav = ['Mon planning'];
        $bouton = 'Déconnexion';
        $redirection = BASE_URL . '/public/index.php?page=logout';
        require __DIR__ . '/../shared/header.php';
    ?>

    <div class="app">
        <main class="main-content">
            <section class="board">

                <div class="board__toolbar">
                    <div class="chip">
                        <a class="navbtn"
                           href="<?= BASE_URL ?>/public/index.php?page=employe/planning&date=<?= htmlspecialchars($prevMonday) ?>">
                            ‹
                        </a>

                        <a class="navbtn"
                           href="<?= BASE_URL ?>/public/index.php?page=employe/planning&date=<?= htmlspecialchars($nextMonday) ?>">
                            ›
                        </a>
                    </div>
                    <div class="views">
                        <button type="button" class="active">Vue Hebdo</button>
                    </div>
                </div>

                <?php
                    $joursNoms = ['Monday'=>'Lundi','Tuesday'=>'Mardi','Wednesday'=>'Mercredi','Thursday'=>'Jeudi','Friday'=>'Vendredi','Saturday'=>'Samedi','Sunday'=>'Dimanche'];
                ?>

                <div class="board-body">
                    <div class="col-header">
                        <div class="hcell">Jour</div>
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

                    <div class="grid">
                        <div class="row">
                            <div class="emp">
                                <div class="emp-name">
                                    Mon planning
                                </div>
                            </div>

                            <?php foreach ($joursAffiches as $day): 
                                $daySlots = $slotsPerso[$day] ?? [];
                            ?>
                                <div class="cell">
                                    <?php foreach ($daySlots as $slot): 
                                        $hDeb = substr($slot['heure_debut'], 0, 5);
                                        $hFin = substr($slot['heure_fin'], 0, 5);
                                        $label = htmlspecialchars($slot['label'] ?? $slot['commentaire'] ?? 'Intervention');
                                    ?>
                                        <div class="slot">
                                            <strong><?= htmlspecialchars($hDeb . '–' . $hFin) ?></strong>
                                            <small><?= $label ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </section>
        </main>
    </div>

    <?php require __DIR__ . '/../shared/footer.php'; ?>
</div>
</body>
</html>
