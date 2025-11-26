<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database/db.php';
require_once __DIR__ . '/../Database/employeRepository.php';

class EmployeController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function create(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès refusé");
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $nom      = trim($_POST['nom'] ?? '');
            $prenom   = trim($_POST['prenom'] ?? '');
            $adresse  = trim($_POST['adresse'] ?? '');
            $ville    = trim($_POST['ville'] ?? '');
            $cp       = trim($_POST['cp'] ?? '');
            $salaire  = $_POST['salaire'] !== '' ? (float)$_POST['salaire'] : null;

            if ($username === '' || $password === '' || $nom === '' || $prenom === '') {
                $errors[] = "Les champs identifiant, mot de passe, nom et prénom sont obligatoires.";
            }

            if (empty($errors)) {
                $ok = emp_createSalarie(
                    $this->pdo,
                    $username,
                    $password,
                    $nom,
                    $prenom,
                    $adresse,
                    $ville,
                    $cp,
                    $salaire
                );

                if ($ok) {
                    header('Location: ' . BASE_URL . '/public/index.php?page=employe/list');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création de l'employé.";
                }
            }
        }

        // Variables pour la vue
        $pageTitle = "Ajouter un employé";
        require __DIR__ . '/../Views/chef/planning/crudemploye.php';
    }

    public function liste(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit("Accès refusé");
        }

        $salaries = emp_getAllSalaries($this->pdo);
        $pageTitle = "Liste des employés";
        require __DIR__ . '/../Views/chef/planning/listemploye.php';
    }
}
