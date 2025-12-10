<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? (isset($realisation['id']) ? 'Modifier une réalisation' : 'Nouvelle réalisation')) ?></title>
    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/realisations/formRealisation/style.css" rel="stylesheet">
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

            // Déterminer l'action du formulaire dynamiquement (création ou édition)
            $formAction = isset($realisation['id']) 
                ? BASE_URL.'/public/index.php?page=chef/realisations/edit&id='.$realisation['id'] 
                : BASE_URL.'/public/index.php?page=chef/realisations/create';
        ?>

        <main class="main-content">
            <section class="board">
                <h1><?= isset($realisation['id']) ? 'Modifier une réalisation' : 'Nouvelle réalisation' ?></h1>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="form-realisation" action="<?= $formAction ?>">
                    <div class="form-row">
                        <label for="photo">Photo</label>
                        <input type="file" id="photo" name="photo" <?= empty($realisation['photo']) ? 'required' : '' ?>>
                        <?php if (!empty($realisation['photo'])): ?>
                            <img src="<?= BASE_URL.'/'.$realisation['photo'] ?>" alt="Photo actuelle" width="100">
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <label for="commentaire">Commentaire</label>
                        <textarea id="commentaire" name="commentaire" rows="3" required><?= htmlspecialchars($realisation['commentaire'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <label for="categorie_id">Catégorie</label>
                        <select id="categorie_id" name="categorie_id" required>
                            <option value="">-- Sélectionner une catégorie --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int)$c['id'] ?>" <?= isset($realisation['categorie_id']) && $realisation['categorie_id']==$c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="favoris">
                            <input type="checkbox" id="favoris" name="favoris" <?= !empty($realisation['favoris']) ? 'checked' : '' ?>>
                            Favoris
                        </label>
                    </div>

                    <button type="submit" class="btn_creer_realisation">
                        <?= isset($realisation['id']) ? 'Mettre à jour' : 'Créer' ?>
                    </button>
                </form>
            </section>
        </main>
    </div>

    <?php require __DIR__ . '/../../shared/footer.php'; ?>
</div>
</body>
</html>
