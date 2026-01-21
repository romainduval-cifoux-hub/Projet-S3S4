<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des employés</title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/css/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/listemploye/style.css" rel="stylesheet">
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
              ['label' => 'Liste des employés',  'href' => BASE_URL . '/public/index.php?page=employe/list'],
            ];
            require_once(__DIR__ . '/../../shared/aside.php');
        
            
        ?>

        <main class="main-content">
            <section class="board">
                
                <h1>Liste des employés</h1>

                <a href="<?= BASE_URL ?>/public/index.php?page=employe/create" 
                   class="btn_login">
                    Ajouter un employé
                </a>

                <table class="tableau">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Identifiant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salaries as $sal): ?>
                            <tr>
                                <td><?= htmlspecialchars($sal['id_salarie']) ?></td>
                                <td><?= htmlspecialchars($sal['nom_salarie']) ?></td>
                                <td><?= htmlspecialchars($sal['prenom_salarie']) ?></td>
                                <td><?= htmlspecialchars($sal['username']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </section>
        </main>
    </div>

</div>
</body>
</html>
