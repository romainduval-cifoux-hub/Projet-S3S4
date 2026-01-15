<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/employeRepository.php'; 


class EmployeProfilController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkSalarie(): void
    {
        if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'salarie') {
            http_response_code(403);
            exit("Accès réservé aux employés.");
        }
    }

    public function handleRequest(string $action = 'profil'): void
    {
        $this->checkSalarie();

        switch ($action) {
            case 'profil':
                $this->profil();
                break;

            case 'save':
                $this->save();
                break;

            default:
                http_response_code(404);
                echo "404 - Page non trouvée";
                exit;
        }
    }

    
    private function profil(array $errors = [], ?array $form = null): void
    {
        $idSalarie = (int)$_SESSION['user_id'];

        $salarie = $form ?? emp_getById($this->pdo, $idSalarie);

        $notifsUnread = notif_getUnread($this->pdo, $idSalarie);
        $nbNotifs = count($notifsUnread);

        $pageTitle = "Mes informations – Team Jardin";

        
        require __DIR__ . '/../../Views/employe/formprofil.php';
    }

    
    private function save(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/public/index.php?page=employe/profil");
            exit;
        }

        $idSalarie = (int)$_SESSION['user_id'];

        $nom     = trim($_POST['nom'] ?? '');
        $prenom  = trim($_POST['prenom'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        $ville   = trim($_POST['ville'] ?? '');
        $cp      = trim($_POST['code_postal'] ?? '');
        $salaire = trim($_POST['salaire'] ?? '');

        $errors = [];

        if ($nom === '' || $prenom === '') {
            $errors[] = "Nom et prénom obligatoires.";
        }

        $salaireFloat = null;
        if ($salaire !== '') {
            if (!is_numeric($salaire)) {
                $errors[] = "Salaire invalide.";
            } else {
                $salaireFloat = (float)$salaire;
            }
        }

        if (!empty($errors)) {
            $form = [
                'nom_salarie' => $nom,
                'prenom_salarie' => $prenom,
                'adresse_salarie' => $adresse,
                'ville_salarie' => $ville,
                'code_postal_salarie' => $cp,
                'salaire' => $salaireFloat,
            ];
            $this->profil($errors, $form);
            return;
        }

        $ok = emp_updateProfil(
            $this->pdo,
            $idSalarie,
            $nom,
            $prenom,
            $adresse ?: null,
            $ville ?: null,
            $cp ?: null,
            $salaireFloat
        );

        if (!$ok) {
            $this->profil(["Erreur lors de la mise à jour du profil."], null);
            return;
        }

        header("Location: " . BASE_URL . "/public/index.php?page=employe/profil&ok=1");
        exit;
    }
}
