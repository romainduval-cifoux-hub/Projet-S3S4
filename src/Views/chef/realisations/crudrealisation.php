<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Gestion des réalisations') ?></title>
    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/realisations/liste/style.css" rel="stylesheet">
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
                <h1>Liste des réalisations</h1>

                <form method="get" class="filter-commentaire">
                    <input type="hidden" name="page" value="chef/realisations">

                    <input
                        type="text"
                        name="q"
                        placeholder="Rechercher un commentaire"
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                    >

                    <select name="categorie_id">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option
                                value="<?= $cat['id'] ?>"
                                <?= (($_GET['categorie_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="favoris">
                        <option value="">Tous</option>
                        <option value="1" <?= ($_GET['favoris'] ?? '') === '1' ? 'selected' : '' ?>>Favoris</option>
                        <option value="0" <?= ($_GET['favoris'] ?? '') === '0' ? 'selected' : '' ?>>Non favoris</option>
                    </select>

                    <select name="masque">
                        <option value="">Tous</option>
                        <option value="1" <?= ($_GET['masque'] ?? '') === '1' ? 'selected' : '' ?>>Masqué</option>
                        <option value="0" <?= ($_GET['masque'] ?? '') === '0' ? 'selected' : '' ?>>Visible</option>
                    </select>

                    <button type="submit">Filtrer</button>

                    <?php if (!empty($_GET['q']) || !empty($_GET['categorie_id']) || isset($_GET['favoris'])): ?>
                        <a href="<?= BASE_URL ?>/public/index.php?page=chef/realisations" class="reset-link">
                            Réinitialiser
                        </a>
                    <?php endif; ?>
                </form>
                
                <?php if (!empty($realisations)): ?>
                    <table class="table-realisations">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Commentaire</th>
                                <th>Catégorie</th>
                                <th>Favoris</th>
                                <th>Masque</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($realisations as $r): ?>
                                <tr>
                                    <td>
                                        <img src="<?= BASE_URL.$r['photo'] ?>" alt="Photo" width="80">
                                    </td>
                                    <td><?= htmlspecialchars($r['commentaire']) ?></td>
                                    <td><?= htmlspecialchars($r['categorie_nom']) ?></td>
                                    <td>
                                        <form method="post" action="<?= BASE_URL ?>/public/index.php?page=chef/realisations/toggleFavoris&id=<?= $r['id'] ?>">
                                            <button id="btn_rea" type="submit">
                                                <?= $r['favoris'] ? 'Retirer des favoris' : 'Mettre en favoris' ?>
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        <form method="post" action="<?= BASE_URL ?>/public/index.php?page=chef/realisations/toggleMasque&id=<?= $r['id'] ?>">
                                            <button id="btn_rea" type="submit">
                                                <?= $r['masque'] ? 'Rendre visible' : 'Masquer' ?>
                                            </button>
                                        </form>
                                    </td>

                                    <td>
                                        <a href="<?= BASE_URL ?>/public/index.php?page=chef/realisations/edit&id=<?= $r['id'] ?>">Modifier</a> |
                                        <a href="<?= BASE_URL ?>/public/index.php?page=chef/realisations/delete&id=<?= $r['id'] ?>"
                                           onclick="return confirm('Supprimer cette réalisation ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucune réalisation pour le moment.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <?php require __DIR__ . '/../../shared/footer.php'; ?>
</div>
</body>
</html>
