<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/userRepository.php';
require_once __DIR__ . '/../../Database/accountActivationRepository.php';
require_once __DIR__ . '/../../Utils/mailer.php';

class RegisterController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
    }

    public function handleRequest(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $erreur = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username  = $_POST['username'] ?? '';
            $password  = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';
            $role      = 'client';

            if (!$username || !$password || !$password2) {
                $erreur = "Veuillez remplir tous les champs.";
            } elseif (usernameExists($this->pdo, $username)) {
                $erreur = "Cet email est déjà utilisé.";
            } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
                $erreur = "Le mot de passe doit contenir au moins 8 caractères, une lettre et un chiffre.";
            } elseif ($password !== $password2) {
                $erreur = "Les mots de passe ne correspondent pas.";
            } else {
                $userId = CreerUtilisateur($this->pdo, $username, $password, $role);

                if ($userId) {
                    $token = createActivationToken($this->pdo, $userId, 60);

                    $activationLink = BASE_URL . '/public/index.php?page=activate&token=' . urlencode($token);

                    $subject = "Activation de votre compte";
                    $text =
                        "Bonjour,\n\n" .
                        "Merci pour votre inscription.\n" .
                        "Pour activer votre compte, cliquez sur ce lien :\n" .
                        $activationLink . "\n\n" .
                        "Ce lien expire dans 1 heure.\n";

                    $sent = sendMailViaMailgun($username, $subject, $text);

                    if (!$sent) {
                        $this->pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $userId]);
                        $erreur = "Impossible d'envoyer l'email d'activation. Réessayez plus tard.";
                    } else {
                        $_SESSION['success'] = "Compte créé. Vérifiez vos emails pour activer votre compte.";
                        header('Location: ' . BASE_URL . '/public/index.php?page=login');
                        exit;
                    }
                } else {
                    $erreur = "Impossible de créer le compte.";
                }
            }
        }

        require __DIR__ . '/../../Views/shared/register.php';
    }
}
