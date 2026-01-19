<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Demande de congés') ?></title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/employe/css/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/employe/css/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/demandesConge/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
</head>
<body>
<section class="page">
    <?php
        $nav = ['Espace Personnel', 'Messagerie','Mon planning'];
        $bouton = 'Déconnexion';
        $redirection = BASE_URL . '/public/index.php?page=logout';
        require __DIR__ . '/shared/header_employe.php';
    ?>

    <div class="app">
        <?php
            $menuTitle1 = 'Mon planning';
            $menu1 = [
                ['label' => 'Semaine en cours', 'href' => BASE_URL . '/public/index.php?page=employe/planning'],
                ['label' => 'Demande de congé', 'href' => BASE_URL . '/public/index.php?page=employe/conge']
            ];

            $menuTitle2 = 'Mon compte';
            $menu2 = [
                ['label' => 'Mes informations', 'href' => BASE_URL . '/public/index.php?page=employe/profil'],
            ];
            require __DIR__ . '/../shared/aside.php';
        ?>

        <main class="main-content">
            <section class="board" style="padding:20px;">
                <h1>Demande de congés</h1>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        Votre demande a été enregistrée et est en attente de validation.
                    </div>
                <?php endif; ?>

                <form method="post" class="form-conge">
                    <div class="form-row">
                        <label for="date_debut">Date de début</label>
                        <input type="date" id="date_debut" name="date_debut"
                               value="<?= htmlspecialchars($date_debut) ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="date_fin">Date de fin</label>
                        <input type="date" id="date_fin" name="date_fin"
                               value="<?= htmlspecialchars($date_fin) ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="motif">Motif (optionnel)</label>
                        <textarea id="motif" name="motif" rows="3"><?= htmlspecialchars($motif) ?></textarea>
                    </div>

                    <button type="submit" class="btn_login">Envoyer la demande</button>
                </form>

                <hr>

                <h2>Mes demandes</h2>
                <table class="table-conge">
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Motif</th>
                            <th>Demandé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandes as $d): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($d['date_debut']) ?>
                                    –
                                    <?= htmlspecialchars($d['date_fin']) ?>
                                </td>
                                <td><?= htmlspecialchars($d['statut']) ?></td>
                                <td><?= nl2br(htmlspecialchars($d['motif'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($d['date_demande']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </section>
        </main>
    </div>

    <?php require __DIR__ . '/../shared/footer.php'; ?>
</section>
</body>
</html>
