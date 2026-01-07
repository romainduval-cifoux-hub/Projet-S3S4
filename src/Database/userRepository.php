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

function findUserByUsername(PDO $pdo, string $username): ?array {
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE username = :u LIMIT 1");
    $stmt->execute(['u' => $username]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    return $u ?: null;
}

function createPasswordReset(PDO $pdo, int $userId, string $tokenHash, string $expiresAt): bool {
    $stmt = $pdo->prepare("
        INSERT INTO password_resets (user_id, token_hash, expires_at)
        VALUES (:uid, :th, :exp)
    ");
    return $stmt->execute([
        'uid' => $userId,
        'th'  => $tokenHash,
        'exp' => $expiresAt
    ]);
}

function findValidPasswordReset(PDO $pdo, string $tokenHash): ?array {
    $stmt = $pdo->prepare("
        SELECT id, user_id
        FROM password_resets
        WHERE token_hash = :th
          AND used_at IS NULL
          AND expires_at > NOW()
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute(['th' => $tokenHash]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function markPasswordResetUsed(PDO $pdo, int $resetId): bool {
    $stmt = $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id");
    return $stmt->execute(['id' => $resetId]);
}

function updateUserPassword(PDO $pdo, int $userId, string $newPassword): bool {
    $stmt = $pdo->prepare("UPDATE users SET password = :p WHERE id = :id");
    return $stmt->execute([
        'p'  => md5($newPassword),
        'id' => $userId
    ]);
}

function usernameExists(PDO $pdo, string $username): bool {
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);

    return (bool) $stmt->fetchColumn();
}
