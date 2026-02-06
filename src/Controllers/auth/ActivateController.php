<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/accountActivationRepository.php';

class ActivateController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
    }

    public function handleRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $token = $_GET['token'] ?? '';

        if (!$token || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            $_SESSION['error'] = "Lien d'activation invalide.";
            header('Location: ' . BASE_URL . '/public/index.php?page=login');
            exit;
        }

        $ok = activateAccountWithToken($this->pdo, $token);

        if ($ok) {
            $_SESSION['success'] = "Compte activé. Vous pouvez vous connecter.";
        } else {
            $_SESSION['error'] = "Lien expiré ou déjà utilisé.";
        }

        header('Location: ' . BASE_URL . '/public/index.php?page=login');
        exit;
    }
}
