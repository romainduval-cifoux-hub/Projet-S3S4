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
            <h1><?= htmlspecialchars($formTitle ?? 'Nouveau chantier (créneau demi-journée)') ?></h1>
            <section class="board">
                

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

                
                <div class="chantier-form">
                    <!-- <form method="get" class="form-filter-emp">
                        <input type="hidden" name="page" value="chantier/create">

                        <div class="form-row">
                            <label for="filter_emp">Rechercher un salarié</label>
                            <input type="text"
                                id="filter_emp"
                                name="emp"
                                placeholder="Ex : Marie Dupont"
                                value=" htmlspecialchars($searchEmp ?? '') ?>">
                        </div>

                        <div class="form-row">
                            <label for="filter_client">Rechercher un client</label>
                            <input type="text"
                                id="filter_client"
                                name="cli"
                                placeholder="Ex : Alice Durand"
                                value="htmlspecialchars($searchClient ?? '') ?>">
                        </div>

                        <button type="submit" class="btn_filtrer">Filtrer</button>
                    </form> -->

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
                            <label for="salarie_search">Salarié affecté</label>

                            <input type="hidden" name="id_salarie" id="id_salarie" value="<?= (int)($id_salarie ?? 0) ?>">

                            <div class="combo" id="comboSalarie">
                                <input type="text" id="salarie_search" placeholder="Rechercher un salarié..."
                                    autocomplete="off">

                                <div class="combo-panel" hidden>
                                <?php foreach ($salaries as $s): 
                                    $sid = (int)$s['id_salarie'];
                                    $nomComplet = $s['prenom_salarie'].' '.$s['nom_salarie'];

                                    // dispoMap optionnel (si calculé côté controller)
                                    $isOk = isset($dispoMap[$sid]) ? (bool)$dispoMap[$sid] : null;
                                ?>
                                    <a href="#"
                                        class="combo-item <?= $isOk === true ? 'ok' : ($isOk === false ? 'ko' : '') ?>"
                                        data-id="<?= $sid ?>"
                                        data-label="<?= htmlspecialchars($nomComplet) ?>">

                                            <span class="combo-name"><?= htmlspecialchars($nomComplet) ?></span>

                                            <?php if ($isOk === true): ?>
                                                <span class="badge ok">Dispo</span>
                                            <?php elseif ($isOk === false): ?>
                                                <span class="badge ko">Occupé</span>
                                            <?php endif; ?>

                                        </a>

                                    
                                <?php endforeach; ?>
                                </div>
                            </div>

                            <small>Cliquez sur “Vérifier disponibilités” après avoir choisi les dates et la période.</small>
                            </div>


                            <div class="form-row">
                                <label for="periode">Période</label>
                                <select id="periode" name="periode" required>
                                    <option value="am" <?= $periode === 'am' ? 'selected' : '' ?>>Matin (8h–12h)</option>
                                    <option value="pm" <?= $periode === 'pm' ? 'selected' : '' ?>>Après-midi (13h–17h)</option>
                                    <option value="full" <?= $periode === 'full' ? 'selected' : '' ?>>Journée entière (8h–12h + 13h–17h)</option>
                                </select>
                            </div>
                        
                            
                            <button type="submit" class="btn_filtrer" name="check_dispo" value="1">
                                Vérifier disponibilités
                            </button>            


                            <div class="form-row">
                                <label for="client_search">Client (optionnel)</label>

                                <!-- valeur envoyée au POST -->
                                <input type="hidden" name="id_client" id="id_client" value="<?= (int)($id_client ?? 0) ?>">

                                <div class="combo" id="comboClient">
                                    <input
                                    type="text"
                                    id="client_search"
                                    placeholder="Rechercher un client..."
                                    autocomplete="off"
                                    value="<?= htmlspecialchars($client_label ?? '') ?>"
                                    >

                                    <div class="combo-panel" hidden>
                                    <?php foreach ($clients as $c):
                                        $cid = (int)$c['id_client'];
                                        $nomComplet = trim(($c['prenom_client'] ?? '') . ' ' . ($c['nom_client'] ?? ''));
                                    ?>
                                        <a href="#"
                                            class="combo-item"
                                            data-id="<?= $cid ?>"
                                            data-label="<?= htmlspecialchars($nomComplet) ?>">
                                            <span class="combo-name"><?= htmlspecialchars($nomComplet) ?></span>
                                        </a>
                                        
                                        </button>
                                    <?php endforeach; ?>
                                    </div>
                                </div>

                                <small>Laisse vide si ce chantier n’est pas lié à un client.</small>
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
<script src="<?= BASE_URL ?>/public/assets/chef/shared/js/salariesdispo.js"></script>
<script src="<?= BASE_URL ?>/public/assets/chef/shared/js/listeclients.js"></script>
</body>
</html>
