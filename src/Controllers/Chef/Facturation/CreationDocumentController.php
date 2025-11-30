<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/factureRepository.php'; 

class GestionnaireFacturationController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    }

    public function handleRequest() {


        require_once __DIR__ . '/../../../Views/chef/shared/header_chef.php';

        $factures = getAllFactures($this->pdo);
        require_once __DIR__ . '/../../../Views/chef/facturation/gestionFacture.php';



    }

}
