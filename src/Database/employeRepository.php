<?php

/**
 * Crée un user + un salarié lié (id_salarie = id du user)
 */
function emp_createSalarie(
    PDO $pdo,
    string $username,
    string $password_clair,
    string $nom,
    string $prenom,
    ?string $adresse,
    ?string $ville,
    ?string $cp,
    ?float $salaire
): bool {
    try {
        $pdo->beginTransaction();

        // 1) Créer l'utilisateur
        $stmtUser = $pdo->prepare("
            INSERT INTO users (username, password, role)
            VALUES (:u, :p, 'salarie')
        ");
        $stmtUser->execute([
            ':u' => $username,
            ':p' => md5($password_clair), 
        ]);

        $idUser = (int)$pdo->lastInsertId();

        // 2) Créer le salarié lié
        $stmtSal = $pdo->prepare("
            INSERT INTO salaries (
                id_salarie,
                nom_salarie,
                prenom_salarie,
                adresse_salarie,
                ville_salarie,
                code_postal_salarie,
                salaire,
                date_embauche
            )
            VALUES (
                :id, :nom, :prenom, :adr, :ville, :cp, :sal, CURDATE()
            )
        ");

        $stmtSal->execute([
            ':id'    => $idUser,
            ':nom'   => $nom,
            ':prenom'=> $prenom,
            ':adr'   => $adresse,
            ':ville' => $ville,
            ':cp'    => $cp,
            ':sal'   => $salaire,
        ]);

        $pdo->commit();
        return true;

    } catch (Throwable $e) {
        $pdo->rollBack();
        // Tu peux logger $e->getMessage() ici
        return false;
    }
}

/**
 * Récupère tous les salariés (pour la liste)
 */
function emp_getAllSalaries(PDO $pdo): array
{
    $sql = "SELECT s.id_salarie, s.photo, s.nom_salarie, s.prenom_salarie, u.username
            FROM salaries s
            JOIN users u ON s.id_salarie = u.id
            ORDER BY s.nom_salarie, s.prenom_salarie";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
