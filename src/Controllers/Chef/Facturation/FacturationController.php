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
            $annee = isset($_POST['annee']) ? (int)$_POST['annee'] : (int)date('Y');


            $annees = getAnneesFactures($this->pdo);

            if (empty($annees)) {
                $annees = [(int)date('Y')];
            }



            $nbFactureEnAttente = getNombreFacturesEnAttente($this->pdo, $annee);
            $montantFactureEnAttente = getMontantFactureEnAttente($this->pdo, $annee);
            $nbFacturePayee = getNombreFacturesPayees($this->pdo, $annee);
            $montantFacturePayee = getMontantFacturesPayees($this->pdo, $annee);
            $montantsParMois = getMontantEnAttenteParMois($this->pdo, $annee);
            $montantPayeparMois = getMontantPayeeParMois($this->pdo, $annee);

            
            require_once __DIR__ . '/../../../Views/chef/facturation/dashboard.php';
        }

}