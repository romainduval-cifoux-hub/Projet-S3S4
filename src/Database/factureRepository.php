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

function createFacture(PDO $pdo, array $factureData, array $lignes) {

    try {
        // Début transaction
        $pdo->beginTransaction();

        $sqlDoc = "INSERT INTO Document (
                        num, nomClient, telClient, addrClient, villeClient, 
                        codePostalClient, siretClient, dateDoc, typeDoc, statusDoc, 
                        reglementDoc, datePaiement, nbRelance, idCli
                   ) VALUES (
                        :num, :nomClient, :telClient, :addrClient, :villeClient,
                        :codePostalClient, :siretClient, :dateDoc, :typeDoc, :statusDoc,
                        :reglementDoc, :datePaiement, :nbRelance, :idCli
                   )";

        $stmt = $pdo->prepare($sqlDoc);

        $stmt->execute([
            ':num' => $factureData['num'] ?? generateNextNumeroFacture($pdo),
            ':nomClient'         => $factureData['nomClient'] ?? null,
            ':telClient'         => $factureData['telClient'] ?? null,
            ':addrClient'        => $factureData['addrClient'] ?? null,
            ':villeClient'       => $factureData['villeClient'] ?? null,
            ':codePostalClient'  => $factureData['codePostalClient'] ?? null,
            ':siretClient'       => $factureData['siretClient'] ?? null,
            ':dateDoc'           => $factureData['dateDoc'] ?? date('Y-m-d H:i:s'),
            ':typeDoc'           => $factureData['typeDoc'] ?? 'FACTURE',
            ':statusDoc'         => $factureData['statusDoc'] ?? 'En attente',
            ':reglementDoc'      => $factureData['reglementDoc'] ?? null,
            ':datePaiement'      => $factureData['datePaiement'] ?? null,
            ':nbRelance'         => $factureData['nbRelance'] ?? 0,
            ':idCli'             => $factureData['idCli'] ?? null
        ]);

        $idDoc = $pdo->lastInsertId();

        $sqlLig = "INSERT INTO DetailDocument (
                        idDoc, designation, description, unite, quantite, prixUnitaire
                   ) VALUES (
                        :idDoc, :designation, :description, :unite, :quantite, :prixUnitaire
                   )";

        $stmtLig = $pdo->prepare($sqlLig);

        foreach ($lignes as $ligne) {
            $stmtLig->execute([
                ':idDoc'        => $idDoc,
                ':designation'  => $ligne['designation'] ?? '',
                ':description'  => $ligne['description'] ?? '',
                ':unite'        => $ligne['unite'] ?? '',
                ':quantite'     => $ligne['quantite'] ?? 0,
                ':prixUnitaire' => $ligne['prixUnitaire'] ?? 0.00
            ]);
        }

        $pdo->commit();

        return $idDoc;

    } catch (Exception $e) {

        // Rollback en cas d’erreur
        $pdo->rollBack();
        throw new Exception("Erreur lors de la création de la facture : " . $e->getMessage());
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
