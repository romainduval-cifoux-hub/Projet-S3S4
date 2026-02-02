<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Ajouter un employé') ?></title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/planning/crudemploye/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
</head>
<body>
<div class="page">
    <?php require __DIR__ . '/../shared/header_chef.php'; ?>

    <div class="app">
        
        <?php

            $menuTitle1 = $menuTitle1 ?? 'Gestion des chantiers';
            $menuTitle2 = $menuTitle2 ?? 'Gestion Employé'; 

            $menu1 = [
              ['label'=>'Nouveau chantier', 'href'=> BASE_URL.'/public/index.php?page=chantier/create'],
            ];
            $menu2 = [
              ['label'=>'Ajouter employé', 'href'=> BASE_URL.'/public/index.php?page=employe/create'],
              ['label' => 'Liste des employés',  'href' => BASE_URL . '/public/index.php?page=employe/list'],
            ];
            require_once(__DIR__ . '/../../shared/aside.php');
            

        ?>

        <main class="main-content">
            <section class="board">
                <h1>Ajouter un employé</h1>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" class="form-chantier">
                    <div class="form-row">
                        <label for="username">Identifiant (login)</label>
                        <input type="text" name="username" id="username" required>
                    </div>

                    <div class="form-row">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" required>
                    </div>

                    <div class="form-row">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" required>
                    </div>

                    <div class="form-row">
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom" id="prenom" required>
                    </div>

                    <div class="form-row">
                        <label for="adresse">Adresse</label>
                        <input type="text" name="adresse" id="adresse">
                    </div>

                    <div class="form-row">
                        <label for="ville">Ville</label>
                        <input type="text" name="ville" id="ville">
                    </div>

                    <div class="form-row">
                        <label for="cp">Code postal</label>
                        <input type="text" name="cp" id="cp">
                    </div>

                    <div class="form-row">
                        <label for="salaire">Salaire</label>
                        <input type="number" step="0.01" name="salaire" id="salaire">
                    </div>

                    <div class="form-row">
                        <label for="date_embauche">Date d'embauche</label>
                        <input type="date" name="date_embauche" id="date_embauche">
                    </div>

                    <button type="submit" class="btn_creer_emp">Créer l'employé</button>
                </form>
            </section>
        </main>
    </div>

    <?php require __DIR__ . '/../../shared/footer.php'; ?>
</div>
</body>
</html>
