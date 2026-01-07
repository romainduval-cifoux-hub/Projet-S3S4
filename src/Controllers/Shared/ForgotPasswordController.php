<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/userRepository.php';
require_once __DIR__ . '/../../Utils/mailer.php';

class ForgotPasswordController
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
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');

            $message = "Si un compte existe pour cet email, un lien de réinitialisation a été envoyé.";

            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $user = findUserByUsername($this->pdo, $username);

                if ($user) {
                    $token = bin2hex(random_bytes(32));     
                    $tokenHash = hash('sha256', $token);    

                    $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

                    createPasswordReset($this->pdo, (int)$user['id'], $tokenHash, $expiresAt);

                    $resetLink = BASE_URL . "/public/index.php?page=reset-password&token=" . urlencode($token);

                    $subject = "Réinitialisation de votre mot de passe - Team Jardin";
                    $body =
                        "Bonjour,\n\n" .
                        "Cliquez sur ce lien pour réinitialiser votre mot de passe :\n" .
                        $resetLink . "\n\n" .
                        "Ce lien expire dans 1 heure.\n";

                    sendMailViaMailgun($username, $subject, $body);
                }
            }
        }

        require __DIR__ . '/../../Views/shared/forgot_password.php';
    }
}
