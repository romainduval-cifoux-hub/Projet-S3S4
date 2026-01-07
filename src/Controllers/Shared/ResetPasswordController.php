<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/userRepository.php';

class ResetPasswordController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
    }

    public function handleRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $erreur = '';
        $token = $_GET['token'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';

            if ($password === '' || $password2 === '') {
                $erreur = "Veuillez remplir tous les champs.";
            } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
                $erreur = "Le mot de passe doit contenir au moins 8 caractères, une lettre et un chiffre.";
            } elseif ($password !== $password2) {
                $erreur = "Les mots de passe ne correspondent pas.";
            } else {
                $tokenHash = hash('sha256', $token);
                $reset = findValidPasswordReset($this->pdo, $tokenHash);

                if (!$reset) {
                    $erreur = "Lien invalide ou expiré.";
                } else {
                    updateUserPassword($this->pdo, (int)$reset['user_id'], $password);
                    markPasswordResetUsed($this->pdo, (int)$reset['id']);

                    $_SESSION['success'] = "Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.";
                    header('Location: ' . BASE_URL . '/public/index.php?page=login');
                    exit;
                }
            }
        }

        require __DIR__ . '/../../Views/shared/reset_password.php';
    }
}
