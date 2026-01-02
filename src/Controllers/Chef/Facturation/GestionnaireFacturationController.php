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

    if (($_POST['action'] ?? '') === 'payer' && !empty($_POST['idDoc'])) {
        $idDoc = (int)$_POST['idDoc'];
        marquerFacturePayee($this->pdo, $idDoc);

        header('Location: ' . BASE_URL . '/public/index.php?page=chef/facturation');
        exit;
    }

    $clients = getClientsFactures($this->pdo);

    $idCli = (isset($_POST['idCli']) && $_POST['idCli'] !== '') ? (int)$_POST['idCli'] : null;

    $factures = $idCli ? getFacturesByClient($this->pdo, $idCli) : getAllFactures($this->pdo);

    require_once __DIR__ . '/../../../Views/chef/facturation/gestionFacture.php';
}

}
