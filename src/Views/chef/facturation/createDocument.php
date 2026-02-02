<?php
require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../../../Database/db.php');

$typeDoc = $_POST['typeDoc'] ?? 'Facture';
$idClientChoisi = $_POST['client'] ?? ($clientData['id_client'] ?? '');

$lignesPost = $_POST['lignes'] ?? [];
if (!is_array($lignesPost) || count($lignesPost) === 0) {
    $lignesPost = [
        ['designation' => '', 'description' => '', 'unite' => '', 'quantite' => 1, 'prixUnitaire' => 0]
    ];
}

$datePaiement = $_POST['datePaiement'] ?? '';
$reglementDoc = $_POST['reglementDoc'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/public/assets/shared/img/logoTeamJardinFavicon.png">
    <meta charset="utf-8">
    <title>Team jardin (Chef d'entreprise)</title>

    <link href="<?= BASE_URL ?>/public/assets/shared/charte-graphique.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/header/position.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/aside/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/chef/dashboard/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/style.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/shared/footer/position.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="page">
    <?php require_once(__DIR__ . '/../shared/header_chef.php'); ?>
    <div class="app">
        <?php require_once(__DIR__ . '/asidefacturation.php'); ?>

<main>

    <h1> Numéro de <?= htmlspecialchars($typeDoc) ?> : <?= htmlspecialchars($numFacture) ?></h1>
    <h2>Créer un nouveau document</h2>

    <form method="POST" action="">
        <fieldset>
            <legend>Sélection</legend>

            <label>Type de document :</label>
            <select name="typeDoc">
                <option value="Facture" <?= $typeDoc === 'Facture' ? 'selected' : '' ?>>Facture</option>
                <option value="Devis"   <?= $typeDoc === 'Devis'   ? 'selected' : '' ?>>Devis</option>
            </select>

            <label>Client :</label>
            <select name="client" required>
                <option value="">-- choisir un client --</option>
                <?php foreach ($clients as $cli): ?>
                    <option value="<?= (int)$cli['id_client'] ?>"
                        <?= ((string)$idClientChoisi === (string)$cli['id_client']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cli['nom_client']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Bouton qui recharge les infos client SANS perdre le reste -->
            <button type="submit" name="action" value="selectClient" formnovalidate>
                Charger le client
            </button>
        </fieldset>

        <hr>

        <fieldset>
            <legend>Informations client</legend>

            <!-- idCli utilisé par ton controller -->
            <input type="hidden" name="idCli" value="<?= htmlspecialchars((string)($clientData['id_client'] ?? $idClientChoisi)) ?>">

            <p><strong>Nom client :</strong> <?= htmlspecialchars($clientData['nom_client'] ?? '') ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($clientData['telephone_client'] ?? '') ?></p>
            <p><strong>Adresse :</strong> <?= htmlspecialchars($clientData['adresse_client'] ?? '') ?></p>
            <p><strong>Ville :</strong> <?= htmlspecialchars($clientData['ville_client'] ?? '') ?></p>
            <p><strong>Code postal :</strong> <?= htmlspecialchars($clientData['code_postal_client'] ?? '') ?></p>
            <p><strong>SIRET :</strong> <?= htmlspecialchars($clientData['siret_client'] ?? '') ?></p>
        </fieldset>

        <hr>

        <fieldset>
            <legend>Lignes du document</legend>

            <table id="tableLignes" border="1" cellpadding="5" style="width:100%; margin-bottom:15px;">
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Description</th>
                        <th>Unité</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($lignesPost as $i => $ligne): ?>
                    <tr>
                        <td><input type="text" name="lignes[<?= (int)$i ?>][designation]"
                                   value="<?= htmlspecialchars($ligne['designation'] ?? '') ?>" required></td>

                        <td><input type="text" name="lignes[<?= (int)$i ?>][description]"
                                   value="<?= htmlspecialchars($ligne['description'] ?? '') ?>"></td>

                        <td><input type="text" name="lignes[<?= (int)$i ?>][unite]"
                                   value="<?= htmlspecialchars($ligne['unite'] ?? '') ?>"></td>

                        <td><input type="number" name="lignes[<?= (int)$i ?>][quantite]" min="1"
                                   value="<?= htmlspecialchars((string)($ligne['quantite'] ?? 1)) ?>"></td>

                        <td><input type="number" name="lignes[<?= (int)$i ?>][prixUnitaire]" step="0.01"
                                   value="<?= htmlspecialchars((string)($ligne['prixUnitaire'] ?? 0)) ?>"></td>

                        <td><button type="button" onclick="removeLine(this)">X</button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <button type="button" onclick="addLine()">+ Ajouter une ligne</button>
        </fieldset>

        <hr>

        <fieldset>
            <legend>Informations</legend>

            <label>Date d'échéance :</label>
            <input type="date" name="datePaiement" value="<?= htmlspecialchars($datePaiement) ?>">

            <br><br>

            <button type="submit" name="action" value="createFacture">
                <?= $typeDoc === 'Devis' ? 'Créer le devis' : 'Créer la facture' ?>
            </button>
        </fieldset>
    </form>

</main>

<script>
let indexLigne = <?= count($lignesPost) ?>;

function addLine() {
    const tbody = document.querySelector('#tableLignes tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="lignes[${indexLigne}][designation]" required></td>
        <td><input type="text" name="lignes[${indexLigne}][description]"></td>
        <td><input type="text" name="lignes[${indexLigne}][unite]"></td>
        <td><input type="number" name="lignes[${indexLigne}][quantite]" min="1" value="1"></td>
        <td><input type="number" name="lignes[${indexLigne}][prixUnitaire]" step="0.01" value="0"></td>
        <td><button type="button" onclick="removeLine(this)">X</button></td>
    `;
    tbody.appendChild(tr);
    indexLigne++;
}

function removeLine(button) {
    button.parentElement.parentElement.remove();
}
</script>

</div>
<?php require_once(__DIR__ . '/../../shared/footer.php'); ?>

</body>
</html>
