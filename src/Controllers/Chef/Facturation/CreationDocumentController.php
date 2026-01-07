<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/factureRepository.php';
require_once __DIR__ . '/../../../Models/Facture.php'; 
require_once __DIR__ . '/../../../Models/DetailFacture.php'; 


class CreationDocumentController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS,DB_PORT);
    }

public function handleRequest() {

    $typeDoc = $_POST['typeDoc'] ?? 'Facture';
    if (!in_array($typeDoc, ['Facture', 'Devis'], true)) $typeDoc = 'Facture';

    $numFacture = generateNextNumeroDocument($this->pdo, $typeDoc);
    
    $clients = loadClients($this->pdo);
    $clientData = null;
    $erreur = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        // Toujours récupérer ces champs (car form unique)
        $typeDoc = $_POST['typeDoc'] ?? 'Facture';
        if (!in_array($typeDoc, ['Facture', 'Devis'], true)) {
            $typeDoc = 'Facture';
        }

        $idClientSelect = isset($_POST['client']) ? (int)$_POST['client'] : 0;
        $idCliHidden = isset($_POST['idCli']) ? (int)$_POST['idCli'] : 0;

        // Si on a un client dans le select, c'est la source la plus fiable
        $idCli = $idClientSelect > 0 ? $idClientSelect : $idCliHidden;

        // Charger client si possible (pour ré-afficher la vue correctement)
        if ($idCli > 0) {
            $clientData = getClientById($this->pdo, $idCli);
        }

        // ---- Action : charger le client ----
        if ($action === 'selectClient') {
            // Rien d'autre : on laisse juste la vue s'afficher avec $clientData
        }

        // ---- Action : créer le document ----
        if ($action === 'createFacture') {

            if ($idCli <= 0) {
                $erreur = "Erreur : aucun client sélectionné.";
            }

            $datePaiement = $_POST['datePaiement'] ?? '';
            if ($datePaiement === '') {
                $erreur = "Erreur : la date d'échéance est obligatoire.";
            }

            if (!$clientData) {
                $erreur = "Erreur : client introuvable en base.";
            }

            $lignesPost = $_POST['lignes'] ?? [];
            if (!is_array($lignesPost) || count($lignesPost) === 0) {
                $erreur = "Erreur : ajoute au moins une ligne.";
            }

            // Si pas d'erreur -> création
            if ($erreur === null) {

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

                    // Champs doc
                    'dateDoc'          => date('Y-m-d H:i:s'),
                    'typeDoc'          => $typeDoc,
                    'statusDoc'        => 'En attente',
                    'reglementDoc'     => $_POST['reglementDoc'] ?? '',
                    'datePaiement'     => $datePaiement,
                    'nbRelance'        => 0,
                    'idCli'            => $idCli,
                ];

                $facture = new Facture($factureData);

                // Lignes (filtrer un minimum)
                foreach ($lignesPost as $ligneRow) {
                    $designation = trim((string)($ligneRow['designation'] ?? ''));
                    if ($designation === '') continue; // ignore lignes vides

                    $facture->lignes[] = new LigneFacture([
                        'designation'  => $designation,
                        'description'  => $ligneRow['description'] ?? '',
                        'unite'        => $ligneRow['unite'] ?? '',
                        'quantite'     => $ligneRow['quantite'] ?? 1,
                        'prixUnitaire' => $ligneRow['prixUnitaire'] ?? 0,
                    ]);
                }

                if (count($facture->lignes) === 0) {
                    $erreur = "Erreur : toutes les lignes sont vides.";
                } else {
                    createFacture($this->pdo, $facture);

                    // Choisis ta page cible
                    header('Location: ' . BASE_URL . '/public/index.php?page=chef/facturation');
                    exit;
                }
            }
        }
    }

    // Inclure la vue après le traitement POST
    require_once __DIR__ . '/../../../Views/chef/facturation/createDocument.php';
}
}
