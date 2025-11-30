<?php  

function getEntrepriseInfo(PDO $pdo) {
    $sql = "SELECT nom, description, telephone, adresse, siret FROM entreprise LIMIT 1";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return []; 
    }

    return [
        'nom'         => $row['nom'],
        'description' => $row['description'],
        'telephone'   => $row['telephone'],
        'adresse'     => $row['adresse'],
        'siret'       => $row['siret']
    ];
}

function updateEntreprise(PDO $pdo, array $data) {

    $sql = "UPDATE entreprise SET 
                nom = :nom,
                description = :description,
                telephone = :telephone,
                adresse = :adresse,
                siret = :siret
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':nom'         => $data['nom'] ?? '',
        ':description' => $data['description'] ?? '',
        ':telephone'   => $data['telephone'] ?? '',
        ':adresse'     => $data['adresse'] ?? '',
        ':siret'       => $data['siret'] ?? ''
    ]);
}


function getAllFactures(PDO $pdo) {
    $sql = "SELECT * FROM Document ORDER BY dateDoc ASC";
    $stmt = $pdo->query($sql);

    $factures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$factures) {
        return [];
    }

    // Récupérer les lignes de chaque facture
    foreach ($factures as &$facture) {
        $facture['lignes'] = getLignesFacture($pdo, $facture['idDoc']);
    }

    return $factures;
}

function getFactureById(PDO $pdo, int $idDoc) {
    $sql = "SELECT * FROM Document WHERE idDoc = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idDoc]);

    $facture = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$facture) {
        return null;
    }


    $facture['lignes'] = getLignesFacture($pdo, $idDoc);

    return $facture;
}

function getLignesFacture(PDO $pdo, int $idDoc) {
    $sql = "SELECT * FROM DetailDocument WHERE idDoc = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idDoc]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createFacture(PDO $pdo, Facture $facture) {
    // 1. Insérer la facture dans la table Document
    $stmt = $pdo->prepare("
        INSERT INTO Document 
        (num, nomClient, telClient, addrClient, villeClient, codePostalClient, siretClient, dateDoc, typeDoc, statusDoc, reglementDoc, datePaiement, nbRelance, idCli)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $facture->num,
        $facture->client,
        $facture->telephone,
        $facture->adresse,
        $facture->ville,
        $facture->codePostal,
        $facture->siret,
        $facture->date,
        $facture->type,
        $facture->status,
        $facture->reglement,
        $facture->datePaiement,
        $facture->nbRelance,
        $facture->idCli ?? null // si tu as un id client lié
    ]);

    $factureId = $pdo->lastInsertId();

    // 2. Insérer les lignes dans DetailDocument
    $stmtLigne = $pdo->prepare("
        INSERT INTO DetailDocument 
        (idDoc, designation, description, unite, quantite, prixUnitaire)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($facture->lignes as $ligne) {
        $stmtLigne->execute([
            $factureId,
            $ligne->designation,
            $ligne->description,
            $ligne->unite,
            $ligne->quantite,
            $ligne->prix
        ]);
    }
}






function loadClients(PDO $pdo)
{
    $sql = "SELECT id_client, nom_client FROM clients ORDER BY nom_client ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

function getClientById(PDO $pdo, int $idCli): ?array {
    $sql = "SELECT * FROM clients WHERE id_client = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idCli]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    return $client ?: null;
}

function generateNextNumeroFacture(PDO $pdo) {
    $sql = "SELECT MAX(num) AS lastNum 
            FROM Document 
            WHERE typeDoc = 'Facture'";

    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si aucune facture → commence à 1
    return $row['lastNum'] ? $row['lastNum'] + 1 : 1;
}

?>
