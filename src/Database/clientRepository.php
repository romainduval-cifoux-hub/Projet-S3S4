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
    ?string $adresse,
    ?string $ville,
    ?string $codePostal,
    ?string $siret 
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
    ?string $adresse,
    ?string $ville,
    ?string $codePostal,
    ?string $siret 
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

function client_getDocuments(PDO $pdo, int $clientId): array
{
    $sql = "
        SELECT 
            idDoc,
            num,
            typeDoc,
            statusDoc,
            dateDoc,
            reglementDoc
        FROM Document
        WHERE idCli = :client_id
        ORDER BY dateDoc DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'client_id' => $clientId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function client_getCommentaires(PDO $pdo, int $clientId): array
{
    $sql = "
        SELECT
            id,
            date_commentaire,
            note,
            commentaire
        FROM avis
        WHERE user_id = :client_id
        ORDER BY date_commentaire DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'client_id' => $clientId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function client_supprimerCommentaire(PDO $pdo, int $commentaireId, int $clientId): bool
{
    $sql = "
        DELETE FROM avis
        WHERE id = :commentaire_id
        AND user_id = :client_id
    ";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'commentaire_id' => $commentaireId,
        'client_id'      => $clientId
    ]);
}

function clientExists(PDO $pdo, int $userId): bool
{
    // on réutilise client_getByUserId défini dans ce même fichier
    $client = client_getByUserId($pdo, $userId);
    return $client !== null;
}
