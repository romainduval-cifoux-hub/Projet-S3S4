<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/dashboardRepository.php';

class FacturationController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
    }

    public function handleRequest() {

            
            require_once __DIR__ . '/../../../Views/chef/shared/header_chef.php';
            $annee = date('Y'); 


            $nbFactureEnAttente = getNombreFacturesEnAttente($this->pdo);
            $montantFactureEnAttente = getMontantFactureEnAttente($this->pdo);

            $nbFacturePayee = getNombreFacturesPayees($this->pdo);
            $montantFacturePayee = getMontantFacturesPayees($this->pdo);

            $montantsParMois = getMontantEnAttenteParMois($this->pdo);

            $montantPayeparMois = getMontantPayeeParMois($this->pdo);
            
            require_once __DIR__ . '/../../../Views/chef/facturation/dashboard.php';
        }

}