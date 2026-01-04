<?php require_once __DIR__ . '/../../../config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Demandes de congés') ?></title>

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
    <?php require __DIR__ . '/../shared/header_chef.php'; ?>

    <div class="app">
        <?php
            $menuTitle1 = 'Gestion des chantiers';
            $menu1 = [
                ['label' => 'Planning', 'href' => BASE_URL . '/public/index.php?page=chef/planning'],
                ['label' => 'Demandes de congés', 'href' => BASE_URL . '/public/index.php?page=chef/conges'],
            ];
            $menuTitle2 = 'Gestion Employé';
            $menu2 = [
                ['label'=>'Ajouter employé', 'href'=> BASE_URL.'/public/index.php?page=employe/create'],
            ];
            require __DIR__ . '/../../shared/aside.php';
        ?>

        <main class="main-content">
            <section class="board">
                <h1>Demandes de congés en attente</h1>

                <?php if (isset($_GET['err']) && $_GET['err'] === 'planning'): ?>
                    <div class="alert alert-error">
                        Impossible d'appliquer le congé : conflit avec des créneaux de travail existants.
                    </div>
                <?php endif; ?>

                <table class="table-conge">
                    <thead>
                        <tr>
                            <th>Salarié</th>
                            <th>Période</th>
                            <th>Motif</th>
                            <th>Demandé le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandes as $d): ?>
                            <tr>
                                <td>
                                    <?php
                                        if (!empty($d['prenom_salarie']) || !empty($d['nom_salarie'])) {
                                            echo htmlspecialchars($d['prenom_salarie'] . ' ' . $d['nom_salarie']);
                                        } else {
                                            echo htmlspecialchars($d['username']); // fallback
                                        }
                                    ?>
                                </td>

                                <td><?= htmlspecialchars($d['date_debut']) ?> – <?= htmlspecialchars($d['date_fin']) ?></td>
                                <td><?= nl2br(htmlspecialchars($d['motif'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($d['date_demande']) ?></td>
                                <td>
                                    <form method="post" action="<?= BASE_URL ?>/public/index.php?page=chef/conges/traiter">
                                        <input type="hidden" name="id_conge" value="<?= (int)$d['id_conge'] ?>">
                                        <button type="submit" name="action" value="accepter" class="btn_login">Accepter</button>
                                        <button type="submit" name="action" value="refuser" class="btn_login">Refuser</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </section>
        </main>
    </div>

    <?php require __DIR__ . '/../../shared/footer.php'; ?>
</div>
</body>
</html>
