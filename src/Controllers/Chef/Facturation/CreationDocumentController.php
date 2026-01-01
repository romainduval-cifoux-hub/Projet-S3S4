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

// ... dans handleRequest()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'selectClient' && !empty($_POST['client'])) {
        $idCli = (int) $_POST['client'];
        $clientData = getClientById($this->pdo, $idCli);
    }

    if ($action === 'createFacture') {

        // 1) Récupérer l'id client envoyé en hidden
        $idCli = isset($_POST['idCli']) ? (int) $_POST['idCli'] : 0;
        if ($idCli <= 0) {
            die("Erreur : aucun client sélectionné (idCli manquant).");
        }

        // 2) Recharger le client depuis la BD (source de vérité)
        $clientData = getClientById($this->pdo, $idCli);
        if (!$clientData) {
            die("Erreur : client introuvable en base (idCli=$idCli).");
        }

        // 3) Construire la facture en utilisant les champs de la BD, pas le POST
        $factureData = [
            'idDoc'            => null,
            'num'              => $numFacture,

            // Champs client depuis la BD
            'nomClient'        => $clientData['nom_client'] ?? '',
            'telClient'        => $clientData['telephone_client'] ?? '',
            'addrClient'       => $clientData['adresse_client'] ?? '',
            'villeClient'      => $clientData['ville_client'] ?? '',
            'codePostalClient' => $clientData['code_postal_client'] ?? '',
            'siretClient'      => $clientData['siret_client'] ?? '',

            // Champs facture depuis le POST
            'dateDoc'          => $_POST['dateDoc'] ?? date('Y-m-d'),
            'typeDoc'          => $_POST['typeDoc'] ?? 'Facture',
            'statusDoc'        => 'En attente',
            'reglementDoc'     => $_POST['reglementDoc'] ?? '',
            'datePaiement'     => null,
            'nbRelance'        => 0,
        ];

        $facture = new Facture($factureData);

        // 4) Lignes
        $lignesPost = $_POST['lignes'] ?? [];
        foreach ($lignesPost as $ligneRow) {
            $facture->lignes[] = new LigneFacture($ligneRow);
        }

        // 5) Enregistrer + redirect
        createFacture($this->pdo, $facture);
        header('Location: ' . BASE_URL . '/public/index.php?page=chef/facturation/dashboard');
        exit;
    }
}

        // Inclure la vue après le traitement POST
        require_once __DIR__ . '/../../../Views/chef/facturation/createDocument.php';
    }
}
