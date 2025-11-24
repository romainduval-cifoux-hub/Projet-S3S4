<?php

require_once __DIR__ . '/../Database/db.php';

class AvisController {

    public function add() {

        // Vérifier si utilisateur connecté et est un client
       if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== 'client') {
            header("Location: index.php?page=home&error=not_client");
            exit;
       }

        // Vérifier si formulaire valide
        if (!isset($_POST['note'], $_POST['commentaire']) || empty($_POST['commentaire'])) {
            header("Location: index.php?page=home&error=form_invalid");
            exit;
        }

        $pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);

        $userId = $_SESSION['user_id']; 
        $note = (int)$_POST['note'];
        $commentaire = trim($_POST['commentaire']);

        $sql = "INSERT INTO avis (user_id, date_commentaire, commentaire, note)
                VALUES (:user_id, NOW(), :commentaire, :note)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id'     => $userId,
            'commentaire' => $commentaire,
            'note'        => $note
        ]);

        header("Location: index.php?page=home&success=avis_added");
        exit;
    }
}