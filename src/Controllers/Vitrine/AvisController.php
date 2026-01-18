<?php

require_once __DIR__ . '/../../Database/db.php';

class AvisController
{

    public function add()
{

    // Vérifier si utilisateur connecté et est un client
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== 'client') {
        $_SESSION['avis_error'] = "Vous devez être connecté en tant que client pour laisser un avis.";
        header("Location: index.php?#main-avis");
        exit;
    }

    // Vérifier si formulaire valide
    if (!isset($_POST['note'], $_POST['commentaire']) || empty(trim($_POST['commentaire'])) || empty($_POST['note'])) {
        $_SESSION['avis_error'] = "Veuillez remplir correctement la note et le commentaire.";
        header("Location: index.php?#main-avis");
        exit;
    }

    $note = (int)$_POST['note'];
    if ($note < 1 || $note > 5) {
        $_SESSION['avis_error'] = "La note doit être comprise entre 1 et 5.";
        header("Location: index.php?#main-avis");
        exit;
    }

    $pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);

    $userId = (int)$_SESSION['user_id'];
    $commentaire = trim($_POST['commentaire']);

    // Vérifier que le client n'a pas déjà posté un avis aujourd'hui
    $sqlCheck = "
        SELECT COUNT(*)
        FROM avis
        WHERE user_id = :user_id
          AND DATE(date_commentaire) = CURDATE()
    ";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute(['user_id' => $userId]);

    if ((int)$stmtCheck->fetchColumn() > 0) {
        $_SESSION['avis_error'] = "Vous avez déjà laissé un avis aujourd’hui. Revenez demain 🙂";
        header("Location: index.php?#main-avis");
        exit;
    }

    // Insertion
    $sql = "
        INSERT INTO avis (user_id, date_commentaire, commentaire, note)
        VALUES (:user_id, NOW(), :commentaire, :note)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id'     => $userId,
        'commentaire' => $commentaire,
        'note'        => $note
    ]);

    $_SESSION['avis_success'] = "Merci pour votre avis 🙏";
    header("Location: index.php?#main-avis");
    exit;
}
}