<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Database/db.php';
require_once __DIR__ . '/../../Database/factureRepository.php';
require_once __DIR__ . '/../../Libs/fpdf186/fpdf.php';
require_once __DIR__ . '/../../Utils/pdf_helpers.php';
require_once __DIR__ . '/../../Utils/document_pdf_builder.php';


class ClientDocumentController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function pdf(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Vérif client connecté
        $role = $_SESSION['role'] ?? null;
        $userId = (int)($_SESSION['user_id'] ?? 0);

        if ($userId <= 0 || $role !== 'client') {
            http_response_code(403);
            exit('Accès interdit');
        }

        $idDoc = (int)($_POST['idDoc'] ?? $_GET['idDoc'] ?? 0);
        if ($idDoc <= 0) {
            http_response_code(400);
            exit('Document invalide');
        }

        // Vérifier que le doc appartient au client connecté
        if (!$this->documentAppartientAuClient($idDoc, $userId)) {
            http_response_code(403);
            exit('Vous n’avez pas accès à ce document');
        }

        $doc = getFactureById($this->pdo, $idDoc);
        if (!$doc) {
            http_response_code(404);
            exit('Document introuvable');
        }

        $entreprise = getEntrepriseInfo($this->pdo);

        $pdf = new FPDF('P', 'mm', 'A4');
        buildDocumentPdf($pdf, $doc, $entreprise);

        while (ob_get_level() > 0) ob_end_clean();
        $numero = $doc['num'] ?? $idDoc;

        // ✅ AFFICHAGE dans le navigateur (pas de download)
        $pdf->Output('I', 'document_' . $numero . '.pdf');
        exit;
    }


    private function documentAppartientAuClient(int $idDoc, int $userId): bool
    {
        $sql = "
        SELECT 1
        FROM Document d
        JOIN clients c ON c.id_client = d.idCli
        WHERE d.idDoc = :idDoc AND c.user_id = :userId
        LIMIT 1
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idDoc' => $idDoc, 'userId' => $userId]);
        return (bool)$stmt->fetchColumn();
    }
}
