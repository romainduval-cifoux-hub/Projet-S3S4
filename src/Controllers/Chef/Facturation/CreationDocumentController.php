<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/factureRepository.php';
require_once __DIR__ . '/../../../Models/Facture.php'; 
require_once __DIR__ . '/../../../Models/DetailFacture.php'; 


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
                // Créer un tableau "row" pour initialiser l'objet Facture
                $factureData = [
                    'idDoc'           => null, // sera généré par la base
                    'num'             => $numFacture,
                    'nomClient'       => $_POST['nomClient'] ?? '',
                    'telClient'       => $_POST['telClient'] ?? '',
                    'addrClient'      => $_POST['addrClient'] ?? '',
                    'villeClient'     => $_POST['villeClient'] ?? '',
                    'codePostalClient'=> $_POST['codePostalClient'] ?? '',
                    'siretClient'     => $_POST['siretClient'] ?? '',
                    'dateDoc'         => $_POST['dateDoc'] ?? date('Y-m-d H:i:s'),
                    'typeDoc'         => $_POST['typeDoc'] ?? 'Facture',
                    'statusDoc'       => 'En attente',
                    'reglementDoc'    => $_POST['reglementDoc'] ?? '',
                    'datePaiement'    => null,
                    'nbRelance'       => 0,
                ];

                $facture = new Facture($factureData);

                // Récupérer les lignes
                $lignesPost = $_POST['lignes'] ?? [];
                foreach ($lignesPost as $ligneRow) {
                    $facture->lignes[] = new LigneFacture($ligneRow);
                }

                // Enregistrer la facture en base
                createFacture($this->pdo, $facture);
                header('Location: ' . BASE_URL . '/public/index.php?page=chef/facturation/dashboard');

            }
        }

        // Inclure la vue après le traitement POST
        require_once __DIR__ . '/../../../Views/chef/facturation/createDocument.php';
    }
}
