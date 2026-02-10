<?php

function createActivationToken(PDO $pdo, int $userId, int $ttlMinutes = 60): string
{
    $token = bin2hex(random_bytes(32)); // 64 chars
    $hash  = hash('sha256', $token);

    $stmt = $pdo->prepare("
        INSERT INTO account_activations (user_id, token_hash, expires_at)
        VALUES (:uid, :hash, DATE_ADD(NOW(), INTERVAL :ttl MINUTE))
    ");
    $stmt->execute([
        ':uid'  => $userId,
        ':hash' => $hash,
        ':ttl'  => $ttlMinutes,
    ]);

    return $token;
}

function activateAccountWithToken(PDO $pdo, string $token): bool
{
    $hash = hash('sha256', $token);

    $stmt = $pdo->prepare("
        SELECT id, user_id
        FROM account_activations
        WHERE token_hash = :hash
          AND used_at IS NULL
          AND expires_at > NOW()
        LIMIT 1
    ");
    $stmt->execute([':hash' => $hash]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return false;

    $activationId = (int)$row['id'];
    $userId       = (int)$row['user_id'];

    $pdo->beginTransaction();
    try {
        $pdo->prepare("
            UPDATE users
            SET is_active = 1, activated_at = NOW()
            WHERE id = :id
        ")->execute([':id' => $userId]);

        $pdo->prepare("
            UPDATE account_activations
            SET used_at = NOW()
            WHERE id = :id
        ")->execute([':id' => $activationId]);

        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
