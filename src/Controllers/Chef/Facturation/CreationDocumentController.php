<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/factureRepository.php'; 

class CreationDocumentController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    }

    public function handleRequest() {

        $numFacture = generateNextNumeroFacture($this->pdo);
        $clients = loadClients($this->pdo);
        $clientData = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'selectClient' && !empty($_POST['client'])) {
                $idCli = intval($_POST['client']);
                $clientData = getClientById($this->pdo, $idCli);
            }

            if ($action === 'createFacture') {
                // Ici tu récupères toutes les infos POST et tu crées la facture
            }
        }

        require_once __DIR__ . '/../../../Views/chef/facturation/createDocument.php';
    }
}
