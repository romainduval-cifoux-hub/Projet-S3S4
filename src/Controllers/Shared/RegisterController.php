<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/userRepository.php';

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
        $roles = getRoles($this->pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';
            $role     = $_POST['role'] ?? '';

            if (!$username || !$password || !$password2) {
                $erreur = "Veuillez remplir tous les champs.";
            }
            elseif (usernameExists($this->pdo, $username)) {
                $erreur = "Cet email est déjà utilisé.";
            } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
                $erreur = "Le mot de passe doit contenir au moins 8 caractères, une lettre et un chiffre.";
            } elseif ($password !== $password2) {
                $erreur = "Les mots de passe ne correspondent pas.";
            } else {
                $userCreated = CreerUtilisateur($this->pdo, $username, $password, $role);

                if ($userCreated) {
                    $_SESSION['success'] = "Votre compte a été créé avec succès !";
                    header('Location: ' . BASE_URL . '/public/index.php?page=login');
                    exit;
                } else {
                    $erreur = "Impossible de créer le compte.";
                }
            }
        }

        require __DIR__ . '/../../Views/shared/register.php';
    }
}
