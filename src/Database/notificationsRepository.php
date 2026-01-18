<?php

function notif_create(
    PDO $pdo,
    int $id_destinataire,
    ?int $id_expediteur,
    string $titre,
    string $message,
    string $type = 'info',
    ?string $lien = null
): bool {
    $sql = "INSERT INTO notifications
              (id_destinataire, id_expediteur, titre, message, type, lien)
            VALUES
              (:dest, :exp, :titre, :msg, :type, :lien)";
    $st = $pdo->prepare($sql);
    return $st->execute([
        ':dest'  => $id_destinataire,
        ':exp'   => $id_expediteur,
        ':titre' => $titre,
        ':msg'   => $message,
        ':type'  => $type,
        ':lien'  => $lien
    ]);
}

function notif_getUnread(PDO $pdo, int $id_user): array
{
    $sql = "SELECT *
            FROM notifications
            WHERE id_destinataire = :u AND is_read = 0
            ORDER BY date_creation DESC";
    $st = $pdo->prepare($sql);
    $st->execute([':u' => $id_user]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

function notif_getLatest(PDO $pdo, int $id_user, int $limit = 20): array
{
    $sql = "SELECT *
            FROM notifications
            WHERE id_destinataire = :u
            ORDER BY date_creation DESC
            LIMIT :lim";
    $st = $pdo->prepare($sql);
    $st->bindValue(':u', $id_user, PDO::PARAM_INT);
    $st->bindValue(':lim', $limit, PDO::PARAM_INT);
    $st->execute();
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

function notif_markRead(PDO $pdo, int $id_notification, int $id_user): bool
{
    $sql = "UPDATE notifications
            SET is_read = 1, date_lu = NOW()
            WHERE id_notification = :id AND id_destinataire = :u";
    $st = $pdo->prepare($sql);
    return $st->execute([
        ':id' => $id_notification,
        ':u'  => $id_user
    ]);
}


function notif_markAllRead(PDO $pdo, int $id_user): bool
{
    $sql = "UPDATE notifications
            SET is_read = 1, date_lu = NOW()
            WHERE id_destinataire = :u AND is_read = 0";
    $st = $pdo->prepare($sql);
    return $st->execute([':u' => $id_user]);
}

function notif_getById(PDO $pdo, int $id_notif, int $id_user): ?array
{
    $sql = "SELECT *
            FROM notifications
            WHERE id_notification = :id AND id_destinataire = :u
            LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute([':id' => $id_notif, ':u' => $id_user]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function user_getAllAdminIds(PDO $pdo): array
{
    $st = $pdo->query("SELECT id FROM users WHERE role='admin' ORDER BY id ASC");
    $ids = $st->fetchAll(PDO::FETCH_COLUMN);
    return array_map('intval', $ids ?: []);
}

