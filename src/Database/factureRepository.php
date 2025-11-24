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



?>