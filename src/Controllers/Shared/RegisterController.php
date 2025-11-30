<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/userRepository.php';

class RegisterController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    }

    public function handleRequest(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $erreur = '';
        $roles = getRoles($this->pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $role     = $_POST['role'] ?? '';

            if (!$username || !$password) {
                $erreur = "Veuillez remplir tous les champs.";
            } else {
                $userCreated = CreerUtilisateur($this->pdo, $username, $password, $role);
                if ($userCreated) {
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
