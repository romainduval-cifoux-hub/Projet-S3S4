<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/factureRepository.php'; 

class FormEditBusinessInfoController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
    }

    public function handleRequest() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom'         => $_POST['nom'] ?? '',
                'description' => $_POST['description'] ?? '',
                'telephone'   => $_POST['telephone'] ?? '',
                'adresse'     => $_POST['adresse'] ?? '',
                'siret'       => $_POST['siret'] ?? ''
            ];

            updateEntreprise($this->pdo, $data);
            $successMessage = "Les informations ont été mises à jour avec succès.";
        }


        $dataBusiness = getEntrepriseInfo($this->pdo);


        require_once __DIR__ . '/../../../Views/chef/shared/header_chef.php';
        require_once __DIR__ . '/../../../Views/chef/facturation/formEditBusinessInfo.php';
    }

}
