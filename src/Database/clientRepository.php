<?php

/**
 * Récupère la fiche client à partir de l'id user (FK = id_client)
 */
function client_getByUserId(PDO $pdo, int $userId): ?array
{
    $sql = "SELECT *
            FROM clients
            WHERE id_client = :id";
    $st = $pdo->prepare($sql);
    $st->execute([':id' => $userId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Crée une fiche client liée à un user existant
 */
function client_create(
    PDO $pdo,
    int $userId,
    string $nom,
    string $prenom,
    string $adresse = null,
    string $ville = null,
    string $codePostal = null,
    string $siret = null
): bool {
    $sql = "INSERT INTO clients (
                id_client,
                nom_client,
                prenom_client,
                adresse_client,
                ville_client,
                code_postal_client,
                siret_client
            )
            VALUES (
                :id,
                :nom,
                :prenom,
                :adresse,
                :ville,
                :cp,
                :siret
            )";

    $st = $pdo->prepare($sql);
    return $st->execute([
        ':id'      => $userId,
        ':nom'     => $nom,
        ':prenom'  => $prenom,
        ':adresse' => $adresse,
        ':ville'   => $ville,
        ':cp'      => $codePostal,
        ':siret'   => $siret
    ]);
}

/**
 * Met à jour la fiche client liée à un user
 */
function client_update(
    PDO $pdo,
    int $userId,
    string $nom,
    string $prenom,
    string $adresse = null,
    string $ville = null,
    string $codePostal = null,
    string $siret = null
): bool {
    $sql = "UPDATE clients
            SET nom_client = :nom,
                prenom_client = :prenom,
                adresse_client = :adresse,
                ville_client = :ville,
                code_postal_client = :cp,
                siret_client = :siret
            WHERE id_client = :id";

    $st = $pdo->prepare($sql);
    return $st->execute([
        ':id'      => $userId,
        ':nom'     => $nom,
        ':prenom'  => $prenom,
        ':adresse' => $adresse,
        ':ville'   => $ville,
        ':cp'      => $codePostal,
        ':siret'   => $siret
    ]);
}

function clientExists(PDO $pdo, int $userId): bool
{
    // on réutilise client_getByUserId défini dans ce même fichier
    $client = client_getByUserId($pdo, $userId);
    return $client !== null;
}
