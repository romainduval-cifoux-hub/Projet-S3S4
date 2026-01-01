<?php

require_once(__DIR__ . '/../../../config.php'); 
require_once(__DIR__ . '/../../../Database/db.php');     

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

    <h1> Numéro de facture : <?= $numFacture ?></h1>

    <h2>Créer une nouvelle facture</h2>

    <!-- ================= FORMULAIRE 1 : Sélection du client ================= -->
    <form method="POST" action="">
        <fieldset>
            <legend>Sélectionner un client</legend>

            <label>Type de document :</label>
            <select name="typeDoc">
                <option value="Facture">Facture</option>
                <option value="Devis">Devis</option>
            </select>


            <label>Client :</label>
            <select name="client" required>
                <option value="">-- choisir un client --</option>
                <?php foreach ($clients as $cli): ?>
                    <option value="<?= $cli['id_client'] ?>"
                        <?= isset($clientData['id_client']) && $clientData['id_client'] == $cli['id_client'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cli['nom_client']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="action" value="selectClient">Choisir</button>
        </fieldset>
    </form>

    <hr>

    <!-- ================= FORMULAIRE 2 : Création de la facture ================= -->
    <form method="POST" action="">
        <fieldset>
            
            <legend>Informations client</legend>

            <input type="hidden" name="idCli" value="<?= $clientData['id_client'] ?? '' ?>">
            <p><strong>Nom client :</strong> <?= htmlspecialchars($clientData['nom_client'] ?? '') ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($clientData['telephone_client'] ?? '') ?></p>
            <p><strong>Adresse :</strong> <?= htmlspecialchars($clientData['adresse_client'] ?? '') ?></p>
            <p><strong>Ville :</strong> <?= htmlspecialchars($clientData['ville_client'] ?? '') ?></p>
            <p><strong>Code postal :</strong> <?= htmlspecialchars($clientData['code_postal_client'] ?? '') ?></p>
            <p><strong>SIRET :</strong> <?= htmlspecialchars($clientData['siret_client'] ?? '') ?></p>

        </fieldset>

        <hr>

        <fieldset>
            <legend>Lignes de la facture</legend>
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
                    <tr>
                        <td><input type="text" name="lignes[0][designation]" required></td>
                        <td><input type="text" name="lignes[0][description]"></td>
                        <td><input type="text" name="lignes[0][unite]"></td>
                        <td><input type="number" name="lignes[0][quantite]" min="1" value="1"></td>
                        <td><input type="number" name="lignes[0][prixUnitaire]" step="0.01" value="0"></td>
                        <td><button type="button" onclick="removeLine(this)">X</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" onclick="addLine()">+ Ajouter une ligne</button>
        </fieldset>

        <hr>

        <fieldset>
            <legend>Informations Facture</legend>
            <label>Date d'échéance :</label>
            <input type="date" name="dateDoc" value="<?= date('dd-mm-YYYY') ?>">

            <label>Mode de règlement :</label>
            <select name="reglementDoc">
                <option value="">Non spécifié</option>
                <option value="Carte bancaire">Carte bancaire</option>
                <option value="Virement">Virement</option>
                <option value="Chèque">Chèque</option>
                <option value="Espèces">Espèces</option>
            </select>

            <br><br>
            <button type="submit" name="action" value="createFacture">Créer la facture</button>
        </fieldset>
    </form>

</main>

<script>
let indexLigne = 1;

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
</body>
</html>
