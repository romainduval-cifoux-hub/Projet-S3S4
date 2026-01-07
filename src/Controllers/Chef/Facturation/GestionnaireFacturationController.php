<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../Database/factureRepository.php';
require_once __DIR__ . '/../../../Libs/fpdf186/fpdf.php';

function pdf_txt($s): string {
    $s = (string)$s;
    $out = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $s);
    return $out !== false ? $out : $s;
}

function money_eur(float $n): string {
    return number_format($n, 2, ',', ' ') . ' EUR';
}

class GestionnaireFacturationController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS,DB_PORT);
    }

    public function handleRequest() {

        // =========================
        // PDF : avant tout HTML
        // =========================
        if (($_POST['action'] ?? '') === 'pdf' && !empty($_POST['idDoc'])) {

            $idDoc = (int)$_POST['idDoc'];
            $facture = getFactureById($this->pdo, $idDoc);

            if (!$facture) {
                http_response_code(404);
                exit('Document introuvable');
            }

            $entreprise = getEntrepriseInfo($this->pdo);

            // ====== PARAMS STYLE ======
            $tvaTaux = 0.20;
            $vert = [77, 110, 55];
            $vertClair = [226, 236, 219];

            $numero = $facture['num'] ?? $idDoc;
            $dateEmission = !empty($facture['dateDoc']) ? date('d/m/Y', strtotime($facture['dateDoc'])) : '';

            // ====== CALCULS ======
            $totalHT = 0.0;
            foreach (($facture['lignes'] ?? []) as $l) {
                $qte = (float)($l['quantite'] ?? 0);
                $pu  = (float)($l['prixUnitaire'] ?? 0);
                $totalHT += $qte * $pu;
            }
            $tva = $totalHT * $tvaTaux;
            $totalTTC = $totalHT + $tva;

            // ====== PDF ======
            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();

            // --- Bandeau haut ---
            $pdf->SetFillColor($vert[0], $vert[1], $vert[2]);
            $pdf->Rect(0, 0, 210, 18, 'F');

            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetXY(10, 5);

            // Optionnel mais recommandé : afficher "Devis" si besoin
            $libelleDoc = $facture['typeDoc'] ?? 'Document';
            $pdf->Cell(100, 8, pdf_txt($libelleDoc . ' ' . $numero), 0, 0, 'L');

            $pdf->SetFont('Arial', '', 11);
            $pdf->SetXY(120, 6);
            $pdf->Cell(80, 6, pdf_txt('Date d\'émission : ' . $dateEmission), 0, 0, 'R');

            $pdf->SetTextColor(0, 0, 0);

            // --- Zone infos ---
            $yTop = 25;

            // Logo (optionnel)
            $logoPath = __DIR__ . '/../../../../public/assets/shared/img/logoTeamJardin.png';
            if (file_exists($logoPath)) {
                $pdf->Image($logoPath, 12, $yTop, 28);
            }

            // Entreprise (gauche)
            $pdf->SetXY(50, $yTop);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(80, 6, pdf_txt($entreprise['nom'] ?? 'NOM DE L\'ENTREPRISE'), 0, 1);

            $pdf->SetX(50);
            $pdf->SetFont('Arial', '', 9);

            if (!empty($entreprise['description'])) {
                $pdf->Cell(80, 5, pdf_txt($entreprise['description']), 0, 1);
                $pdf->SetX(50);
            }
            if (!empty($entreprise['telephone'])) {
                $pdf->Cell(80, 5, pdf_txt($entreprise['telephone']), 0, 1);
                $pdf->SetX(50);
            }

            $pdf->MultiCell(80, 5, pdf_txt($entreprise['adresse'] ?? ''));

            $pdf->SetX(50);
            if (!empty($entreprise['siret'])) {
                $pdf->Cell(80, 5, pdf_txt('SIRET : ' . $entreprise['siret']), 0, 1);
            }

            // Client (droite)
            $pdf->SetXY(130, $yTop);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(70, 5, pdf_txt('Nom du client : ' . ($facture['nomClient'] ?? '')), 0, 1, 'R');

            $pdf->SetX(130);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(70, 5, pdf_txt('Téléphone : ' . ($facture['telClient'] ?? '')), 0, 1, 'R');

            $pdf->SetX(130);
            $pdf->Cell(70, 5, pdf_txt('Adresse postale : ' . ($facture['addrClient'] ?? '')), 0, 1, 'R');

            $pdf->SetX(130);
            $pdf->Cell(70, 5, pdf_txt('Ville : ' . ($facture['villeClient'] ?? '')), 0, 1, 'R');

            $pdf->SetX(130);
            $pdf->Cell(70, 5, pdf_txt('Code postal : ' . ($facture['codePostalClient'] ?? '')), 0, 1, 'R');

            // --- Bandeau vert milieu ---
            $pdf->SetFillColor($vert[0], $vert[1], $vert[2]);
            $pdf->Rect(0, 78, 210, 16, 'F');

            // --- Tableau lignes ---
            $pdf->SetY(105);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor($vertClair[0], $vertClair[1], $vertClair[2]);

            $w = [
                'designation' => 45,
                'description' => 45,
                'unite'       => 25,
                'qte'         => 25,
                'pu'          => 25,
                'montant'     => 25
            ];

            $pdf->SetX(10);
            $pdf->Cell($w['designation'], 10, pdf_txt('Désignation'), 1, 0, 'C', true);
            $pdf->Cell($w['description'], 10, pdf_txt('Description'), 1, 0, 'C', true);
            $pdf->Cell($w['unite'],       10, pdf_txt('Unité'),       1, 0, 'C', true);
            $pdf->Cell($w['qte'],         10, pdf_txt('Quantité'),    1, 0, 'C', true);
            $pdf->Cell($w['pu'],          10, pdf_txt('PU HT'),       1, 0, 'C', true);
            $pdf->Cell($w['montant'],     10, pdf_txt('Montant HT'),  1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 9);

            foreach (($facture['lignes'] ?? []) as $l) {
                $designation = $l['designation'] ?? '';
                $description = $l['description'] ?? '';
                $unite       = $l['unite'] ?? '';
                $qte         = (float)($l['quantite'] ?? 0);
                $pu          = (float)($l['prixUnitaire'] ?? 0);
                $montant     = $qte * $pu;

                $pdf->SetX(10);
                $pdf->Cell($w['designation'], 10, pdf_txt($designation), 1, 0, 'C');
                $pdf->Cell($w['description'], 10, pdf_txt($description), 1, 0, 'C');
                $pdf->Cell($w['unite'],       10, pdf_txt($unite),       1, 0, 'C');
                $pdf->Cell($w['qte'],         10, pdf_txt((string)$qte), 1, 0, 'C');
                $pdf->Cell($w['pu'],          10, pdf_txt(money_eur($pu)), 1, 0, 'C');
                $pdf->Cell($w['montant'],     10, pdf_txt(money_eur($montant)), 1, 1, 'C');
            }

            // --- Bloc totaux ---
            $pdf->Ln(8);
            $blocTotauxX = 100;

            $pdf->SetX($blocTotauxX);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor($vertClair[0], $vertClair[1], $vertClair[2]);
            $pdf->Cell(30, 10, pdf_txt('Somme total HT'), 1, 0, 'C', true);
            $pdf->Cell(30, 10, pdf_txt('TVA'), 1, 0, 'C', true);
            $pdf->Cell(40, 10, pdf_txt('Total TTC'), 1, 1, 'C', true);

            $pdf->SetX($blocTotauxX);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(30, 10, pdf_txt(money_eur($totalHT)), 1, 0, 'C');
            $pdf->Cell(30, 10, pdf_txt((int)($tvaTaux * 100) . '%'), 1, 0, 'C');
            $pdf->Cell(40, 10, pdf_txt(money_eur($totalTTC)), 1, 1, 'C');

            // --- Pied de page ---
            $pdf->Ln(14);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(10);
            $pdf->Cell(60, 6, pdf_txt('Mode de règlement :'), 0, 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(60, 6, pdf_txt($facture['reglementDoc'] ?? ''), 0, 1);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(10);
            $pdf->Cell(60, 6, pdf_txt('Date d\'échéance :'), 0, 0);
            $pdf->SetFont('Arial', '', 10);

            $dateEcheance = !empty($facture['datePaiement'])
                ? date('d/m/Y', strtotime($facture['datePaiement']))
                : '-';

            $pdf->Cell(60, 6, pdf_txt($dateEcheance), 0, 1);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(10);
            $pdf->Cell(60, 6, pdf_txt('IBAN :'), 0, 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(120, 6, pdf_txt($entreprise['iban'] ?? ''), 0, 1);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(10);
            $pdf->Cell(60, 6, pdf_txt('BIC :'), 0, 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(120, 6, pdf_txt($entreprise['bic'] ?? ''), 0, 1);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY($blocTotauxX, $pdf->GetY() + 8);
            $pdf->Cell(100, 6, pdf_txt('Bon pour accord, date et signature :'), 0, 1, 'L');

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $pdf->Output('I', 'document_' . $numero . '.pdf');
            exit;
        }

        // =========================
        // HTML normal
        // =========================
        require_once __DIR__ . '/../../../Views/chef/shared/header_chef.php';

        // --- Filtres (POST ou GET) ---
        $idCli = null;
        if (isset($_POST['idCli']) && $_POST['idCli'] !== '') $idCli = (int)$_POST['idCli'];
        if (isset($_GET['idCli']) && $_GET['idCli'] !== '')  $idCli = (int)$_GET['idCli'];

        $types = $_POST['types'] ?? ($_GET['types'] ?? ['Facture', 'Devis']);
        if (!is_array($types)) $types = [$types];
        $types = array_values(array_intersect($types, ['Facture', 'Devis']));

        $filtreEnAttente = $_POST['en_attente'] ?? ($_GET['en_attente'] ?? '1');
        $filtreEnAttente = ($filtreEnAttente === '1') ? '1' : '0';

        // --- Actions ---
        $action = $_POST['action'] ?? null;

        if (!empty($_POST['idDoc']) && in_array($action, ['payer','accepter','refuser'], true)) {
            $idDoc = (int)$_POST['idDoc'];

            $doc = getFactureById($this->pdo, $idDoc);
            if (!$doc) {
                http_response_code(404);
                exit('Document introuvable');
            }

            $typeDoc = $doc['typeDoc'] ?? '';

            if ($action === 'payer') {
                if ($typeDoc !== 'Facture') {
                    http_response_code(400);
                    exit('Action invalide: ce document n\'est pas une Facture');
                }
                marquerFacturePayee($this->pdo, $idDoc);
            }

            if ($action === 'accepter') {
                if ($typeDoc !== 'Devis') {
                    http_response_code(400);
                    exit('Action invalide: ce document n\'est pas un Devis');
                }
                marquerDevisAccepte($this->pdo, $idDoc);
            }

            if ($action === 'refuser') {
                if ($typeDoc !== 'Devis') {
                    http_response_code(400);
                    exit('Action invalide: ce document n\'est pas un Devis');
                }
                marquerDevisRefuse($this->pdo, $idDoc);
            }

            // Redirect avec filtres
            $query = http_build_query([
                'page' => 'chef/facturation',
                'idCli' => $idCli,
                'types' => $types,
                'en_attente' => $filtreEnAttente
            ]);

        }

        // --- Data ---
        $clients = getClientsFactures($this->pdo);

        $factures = $idCli ? getFacturesByClient($this->pdo, $idCli) : getAllFactures($this->pdo);

        // Filtre typeDoc
        if (count($types) === 0) {
            $factures = [];
        } else {
            $factures = array_values(array_filter($factures, function ($f) use ($types) {
                return isset($f['typeDoc']) && in_array($f['typeDoc'], $types, true);
            }));
        }

        // Filtre en attente
        if ($filtreEnAttente === '1') {
            $factures = array_values(array_filter($factures, function ($f) {
                return ($f['statusDoc'] ?? '') === 'En attente';
            }));
        }

        require_once __DIR__ . '/../../../Views/chef/facturation/gestionFacture.php';
    }
}
