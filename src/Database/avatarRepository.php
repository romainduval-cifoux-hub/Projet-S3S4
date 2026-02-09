<?php

function avatar_updateForClient(PDO $pdo, int $userId, string $path): bool
{
    $sql = "UPDATE clients SET photo = :p WHERE user_id = :uid";
    $st = $pdo->prepare($sql);
    return $st->execute([':p' => $path, ':uid' => $userId]);
}

function avatar_updateForSalarie(PDO $pdo, int $userId, string $path): bool
{
    $sql = "UPDATE salaries SET photo = :p WHERE id_salarie = :uid";
    $st = $pdo->prepare($sql);
    return $st->execute([':p' => $path, ':uid' => $userId]);
}
