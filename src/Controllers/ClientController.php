<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Database/db.php';
require_once __DIR__ . '/../Database/clientRepository.php';

class ClientController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function profil(): void {
        if (($_SESSION['role'] ?? '') !== 'client') {
            http_response_code(403);
            exit("Accès refusé");
        }

        $client = client_getByUserId($this->pdo, $_SESSION['user_id']);

        $pageTitle = "Mon profil client";
        require __DIR__ . '/../Views/client/profil.php';
    }

    public function save(): void {

        if (($_SESSION['role'] ?? '') !== 'client') {
            http_response_code(403);
            exit("Accès refusé");
        }

        // Récupération du formulaire
        $nom    = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $ville = trim($_POST['ville'] ?? '');
        $cp = trim($_POST['code_postal'] ?? '');
        $siret = trim($_POST['siret'] ?? '');

        $errors = [];

        if ($nom === '' || $prenom === '') {
            $errors[] = "Nom et prénom obligatoires.";
        }

        if (!empty($errors)) {
            $client = [];
            $pageTitle = "Mon profil client";
            require __DIR__ . '/../Views/client/profil.php';
            return;
        }

        // Vérifie si un profil existe déjà
        $existing = client_getByUserId($this->pdo, $_SESSION['user_id']);

        if ($existing) {
            // Mise à jour
            client_update(
                $this->pdo,
                $_SESSION['user_id'],
                $nom,
                $prenom,
                $adresse,
                $ville,
                $cp,
                $siret
            );
        } else {
            // Création du profil
            client_create(
                $this->pdo,
                $_SESSION['user_id'],
                $nom,
                $prenom,
                $adresse,
                $ville,
                $cp,
                $siret
            );
        }

        header("Location: " . BASE_URL . "/public/index.php?page=home&profil=ok");
        exit;
    }
}
