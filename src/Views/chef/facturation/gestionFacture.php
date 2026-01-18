<?php

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../../Database/db.php');

$types = $_POST['types'] ?? ['Facture', 'Devis'];
if (!is_array($types)) $types = ['Facture', 'Devis'];

$filtreEnAttente = $_POST['en_attente'] ?? '1'; // coché par défaut
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Team jardin (Chef d'entreprise)</title>

    <!-- CSS -->
    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/gestionFacture/style.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<div class="page">
    <?php require_once(__DIR__ . '/../shared/header_chef.php'); ?>

    <div class="app">
        <?php require_once(__DIR__ . '/asidefacturation.php'); ?>

        <main>
            


            <!-- FILTRES -->
            <form method="POST">
                <select name="idCli" onchange="this.form.submit()">
                    <option value="">-- Tous les clients --</option>
                    <?php foreach ($clients as $cli): ?>
                        <option value="<?= $cli['id_client'] ?>"
                            <?= ((int)($idCli ?? 0) === (int)$cli['id_client']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cli['nom_client']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>
                    <input type="checkbox" name="types[]" value="Facture"
                        <?= in_array('Facture', $types, true) ? 'checked' : '' ?>
                        onchange="this.form.submit()">
                    Factures
                </label>

                <label>
                    <input type="checkbox" name="types[]" value="Devis"
                        <?= in_array('Devis', $types, true) ? 'checked' : '' ?>
                        onchange="this.form.submit()">
                    Devis
                </label>

                <input type="hidden" name="en_attente" value="0">

                <label>
                    <input type="checkbox" name="en_attente" value="1"
                        <?= $filtreEnAttente === '1' ? 'checked' : '' ?>
                        onchange="this.form.submit()">
                    Documents en attente uniquement
                </label>

            </form>

            <h1>Liste des documents</h1>
            <?php if (!empty($_SESSION['factu_success'])): ?>
                <div class="alert-factu_success">
                    <?= htmlspecialchars($_SESSION['factu_success']) ?>
                </div>
                <?php unset($_SESSION['factu_success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['factu_error'])): ?>
                <div class="alert-factu_error">
                    <?= htmlspecialchars($_SESSION['factu_error']) ?>
                </div>
                <?php unset($_SESSION['factu_error']); ?>
            <?php endif; ?>

            <?php foreach ($factures as $facture): ?>

                <?php
                $typeDoc = $facture['typeDoc'] ?? '';
                $estDevis = ($typeDoc === 'Devis');

                // Date affichée (évite 01/01/1970)
                $dateRaw = $facture['datePaiement'] ?? null;
                if (empty($dateRaw)) $dateRaw = $facture['dateDoc'] ?? null;
                $dateAffichee = !empty($dateRaw) ? date('d/m/Y', strtotime($dateRaw)) : '-';
                $dateRelanceRaw = $facture['dateDerniereRelance'] ?? null;
                $dateRelanceAffichee = !empty($dateRelanceRaw) ? date('d/m/Y H:i', strtotime($dateRelanceRaw)) : '-';

                ?>

                <div class="facture">
                    <h2><?= htmlspecialchars($typeDoc) ?> n°<?= htmlspecialchars((string)$facture['num']) ?></h2>
                    <hr>

                    <p><strong>Client :</strong> <?= htmlspecialchars((string)($facture['nomClient'] ?? '')) ?></p>
                    <p><strong>Date :</strong> <?= $dateAffichee ?></p>
                    <p><strong>Status :</strong> <?= htmlspecialchars((string)($facture['statusDoc'] ?? '')) ?></p>
                    <p><strong>Dernière relance :</strong> <?= $dateRelanceAffichee ?></p>
                    <p><strong>Nombre de relances :</strong> <?= (int)($facture['nbRelance'] ?? 0) ?></p>


                    <!-- ACTIONS -->
                    <?php if (($facture['statusDoc'] ?? '') === 'En attente'): ?>
                        <form method="POST">
                            <input type="hidden" name="idDoc" value="<?= (int)$facture['idDoc'] ?>">
                            <input type="hidden" name="idCli" value="<?= htmlspecialchars($idCli ?? '') ?>">

                            <?php foreach (($types ?? ['Facture', 'Devis']) as $t): ?>
                                <input type="hidden" name="types[]" value="<?= htmlspecialchars($t) ?>">
                            <?php endforeach; ?>

                            <input type="hidden" name="en_attente" value="<?= htmlspecialchars($filtreEnAttente) ?>">

                            <?php if ($estDevis): ?>
                                <button type="submit" name="action" value="accepter">Marquer comme accepté</button>
                                <button type="submit" name="action" value="refuser">Marquer comme refusé</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="payer">Document payé</button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- PDF -->
                    <form method="POST" target="_blank">
                        <input type="hidden" name="idDoc" value="<?= (int)$facture['idDoc'] ?>">
                        <input type="hidden" name="idCli" value="<?= htmlspecialchars($idCli ?? '') ?>">

                        <?php foreach (($types ?? ['Facture', 'Devis']) as $t): ?>
                            <input type="hidden" name="types[]" value="<?= htmlspecialchars($t) ?>">
                        <?php endforeach; ?>

                        <input type="hidden" name="en_attente" value="<?= htmlspecialchars($filtreEnAttente) ?>">

                        <button name="action" value="pdf">Générer le PDF</button>
                    </form>

                    <!-- Envoi -->
                    <form method="POST">
                        <input type="hidden" name="idDoc" value="<?= (int)$facture['idDoc'] ?>">
                        <input type="hidden" name="idCli" value="<?= htmlspecialchars($idCli ?? '') ?>">

                        <?php foreach (($types ?? ['Facture', 'Devis']) as $t): ?>
                            <input type="hidden" name="types[]" value="<?= htmlspecialchars($t) ?>">
                        <?php endforeach; ?>

                        <input type="hidden" name="en_attente" value="<?= htmlspecialchars($filtreEnAttente) ?>">

                        <button type="submit" name="action" value="send">Envoyer le document par mail</button>
                    </form>


                    <h3>Détails :</h3>

                    <?php $total = 0; ?>

                    <ul>
                        <?php foreach (($facture['lignes'] ?? []) as $ligne): ?>
                            <?php
                            $qte = (float)($ligne['quantite'] ?? 0);
                            $pu = (float)($ligne['prixUnitaire'] ?? 0);
                            $ligneTotal = $qte * $pu;
                            $total += $ligneTotal;
                            ?>
                            <li>
                                <?= htmlspecialchars((string)($ligne['designation'] ?? '')) ?> : <?= $ligneTotal ?> €
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <hr>
                    <p><strong>Total :</strong> <?= $total ?> €</p>
                </div>

            <?php endforeach; ?>

        </main>

    </div>
<?php require_once(__DIR__ . '/../../shared/footer.php'); ?>

</div>

</html>