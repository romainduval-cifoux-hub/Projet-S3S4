<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? (isset($realisation['id']) ? 'Modifier une réalisation' : 'Nouvelle réalisation')) ?></title>
    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/categories/formCategorie/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
</head>
<body>
<div class="page">
    <?php require_once(__DIR__ . '/../shared/header_chef.php'); ?>

    <div class="app">
        <?php
            $menuTitle1 = 'Gestion des réalisations';
            $menu1 = [
                ['label'=>'Liste des réalisations', 'href'=> BASE_URL.'/public/index.php?page=chef/realisations'],
                ['label'=>'Nouvelle réalisation', 'href'=> BASE_URL.'/public/index.php?page=chef/realisations/create']

            ];
            $menuTitle2 = 'Gestion des catégories';
            $menu2 = [
                ['label'=>'Liste des catégories', 'href'=> BASE_URL.'/public/index.php?page=chef/categories'],
                ['label'=>'Nouvelle catégorie', 'href'=> BASE_URL.'/public/index.php?page=chef/categories/create']
            ];
            require_once(__DIR__ . '/../../shared/aside.php');
        ?>

        <main class="main-content">
        <section class="board">
            <h1><?= isset($category) ? 'Modifier une catégorie' : 'Nouvelle catégorie' ?></h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" class="form-category">
                <div class="form-row">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($category['nom'] ?? '') ?>" required>
                </div>
                <button type="submit" class="btn_submit">
                    <?= isset($category) ? 'Mettre à jour' : 'Créer' ?>
                </button>
            </form>
        </section>
    </main>
    </div>

    <?php require __DIR__ . '/../../shared/footer.php'; ?>
</div>
</body>
</html>

