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



?>