<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Nouveau chantier') ?></title>
    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/planning/crudcreneau/style.css" rel="stylesheet"> 
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">
</head>
<body>
<div class="page">
    <?php require_once(__DIR__ . '/../shared/header_chef.php'); ?>

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
            
            $isEdit = isset($mode) && $mode === 'edit';

            
            if (!isset($date_debut)) {
                
                $date_debut = $date_jour ?? date('Y-m-d');
            }
            if (!isset($date_fin)) {
                $date_fin = $date_debut;
            }
        ?>

        <main class="main-content">
            <section class="board">
                <h1><?= htmlspecialchars($formTitle ?? 'Nouveau chantier (créneau demi-journée)') ?></h1>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!--Formulaire de filtres-->

                <form method="get" class="form-filter-emp">
                    <input type="hidden" name="page" value="chantier/create">

                    <div class="form-row">
                        <label for="filter_emp">Rechercher un salarié</label>
                        <input type="text"
                            id="filter_emp"
                            name="emp"
                            placeholder="Ex : Marie Dupont"
                            value="<?= htmlspecialchars($searchEmp ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <label for="filter_client">Rechercher un client</label>
                        <input type="text"
                            id="filter_client"
                            name="cli"
                            placeholder="Ex : Alice Durand"
                            value="<?= htmlspecialchars($searchClient ?? '') ?>">
                    </div>

                    <button type="submit" class="btn_filtrer">Filtrer</button>
                </form>

                <form method="post" class="form-chantier" action="<?= htmlspecialchars($actionUrl ?? (BASE_URL . '/public/index.php?page=chantier/create')) ?>">
                    
                    <?php if ($isEdit): ?>
                        <!-- MODE ÉDITION : une seule date -->
                        <div class="form-row">
                            <label for="date_jour">Date du chantier</label>
                            <input type="date"
                                   id="date_jour"
                                   name="date_jour"
                                   value="<?= htmlspecialchars($date_jour ?? $date_debut) ?>"
                                   required>
                        </div>
                    <?php else: ?>
                                
                        <!-- MODE CRÉATION : période -->
                        <div class="form-row">
                            <label for="date_debut">Date de début</label>
                            <input type="date"
                                id="date_debut"
                                name="date_debut"
                                value="<?= htmlspecialchars($date_debut) ?>"
                                required>
                        </div>

                        <div class="form-row">
                            <label for="date_fin">Date de fin</label>
                            <input type="date"
                                id="date_fin"
                                name="date_fin"
                                value="<?= htmlspecialchars($date_fin) ?>">
                            <small>Si vous laissez vide, le créneau sera créé uniquement le <?= htmlspecialchars($date_debut) ?>.</small>
                        </div>

                    <?php endif; ?>
                            
                            
                            
                        <div class="form-row">
                            <label for="id_salarie">Salarié affecté</label>
                            <select id="id_salarie" name="id_salarie" required>
                                <option value="">-- Sélectionner un salarié --</option>
                                <?php foreach ($salaries as $s): ?>
                                    <option value="<?= (int)$s['id_salarie'] ?>">
                                        <?= htmlspecialchars($s['prenom_salarie'] . ' ' . $s['nom_salarie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <label for="periode">Période</label>
                            <select id="periode" name="periode" required>
                                <option value="am" <?= $periode === 'am' ? 'selected' : '' ?>>Matin (8h–12h)</option>
                                <option value="pm" <?= $periode === 'pm' ? 'selected' : '' ?>>Après-midi (13h–17h)</option>
                                <option value="full" <?= $periode === 'full' ? 'selected' : '' ?>>Journée entière (8h–12h + 13h–17h)</option>
                            </select>
                        </div>
                        
                        

                        <div class="form-row">
                            <label for="id_client">Client (optionnel)</label>
                            <select id="id_client" name="id_client">
                                <option value="">-- Aucun / interne --</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= (int)$c['id_client'] ?>">
                                        <?= htmlspecialchars($c['prenom_client'] . ' ' . $c['nom_client']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <label for="commentaire">Commentaire (optionnel)</label>
                            <textarea id="commentaire" name="commentaire" rows="3"
                                    placeholder="Détails du chantier, lieu précis, matériel à prévoir..."><?= htmlspecialchars($commentaire ?? '') ?></textarea>
                        </div>

                    <button type="submit" class="btn_creer_creneau">Créer le créneau</button>

                </form>
            </section>
        </main>
    </div>

    <?php require __DIR__ . '/../../shared/footer.php'; ?>
</div>
</body>
</html>
