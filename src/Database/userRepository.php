<?php

function login(PDO $pdo, string $username, string $password): ?array {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute([
        'username' => $username,
        'password' => md5($password)
    ]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function CreerUtilisateur(PDO $pdo, string $username, string $password, string $role): bool {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    return $stmt->execute([
        'username' => $username,
        'password' => md5($password),
        'role'     => $role
    ]);
}

function getRoles(PDO $pdo): array {
    $stmt = $pdo->query("SELECT DISTINCT role FROM users"); 
    return $stmt->fetchAll(PDO::FETCH_COLUMN); 
}

/**
 * Recherche d'utilisateur par username
 */
function searchUser(PDO $conn, string $recherche): array {
    $like = '%' . $recherche . '%';
    $sql  = "SELECT id, username, role
             FROM users
             WHERE username LIKE ? 
             ORDER BY username ASC";
    $st = $conn->prepare($sql);
    $st->execute([$like, $like]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}
