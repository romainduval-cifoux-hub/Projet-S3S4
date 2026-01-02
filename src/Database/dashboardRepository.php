<?php

function getNombreFacturesEnAttente(PDO $pdo, int $annee): int {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM Document d
            WHERE d.statusDoc = :status
              AND d.typeDoc = 'Facture'
              AND YEAR(d.datePaiement) = :annee
        ");
        $stmt->execute([
            'status' => 'En attente',
            'annee'  => $annee
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    } catch (PDOException $e) {
        error_log("Erreur getNombreFacturesEnAttente : " . $e->getMessage());
        return 0;
    }
}

function getMontantFactureEnAttente(PDO $pdo, int $annee): float {
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(ld.quantite * ld.prixUnitaire), 0) AS total_attente
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
              AND d.typeDoc = 'Facture'
              AND YEAR(d.datePaiement) = :annee
        ");
        $stmt->execute([
            'status' => 'En attente',
            'annee'  => $annee
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total_attente'] ?? 0.0);
    } catch (PDOException $e) {
        error_log("Erreur getMontantFactureEnAttente : " . $e->getMessage());
        return 0.0;
    }
}

function getMontantEnAttenteParMois(PDO $pdo, int $annee): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                MONTH(d.datePaiement) AS mois,
                COALESCE(SUM(ld.quantite * ld.prixUnitaire), 0) AS total_attente
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
              AND d.typeDoc = 'Facture'
              AND YEAR(d.datePaiement) = :annee
            GROUP BY MONTH(d.datePaiement)
            ORDER BY MONTH(d.datePaiement)
        ");
        $stmt->execute([
            'status' => 'En attente',
            'annee'  => $annee
        ]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $montantsParMois = array_fill(1, 12, 0.0);
        foreach ($result as $row) {
            $mois = (int)($row['mois'] ?? 0);
            if ($mois >= 1 && $mois <= 12) {
                $montantsParMois[$mois] = (float)($row['total_attente'] ?? 0.0);
            }
        }
        return $montantsParMois;

    } catch (PDOException $e) {
        error_log("Erreur getMontantEnAttenteParMois : " . $e->getMessage());
        return array_fill(1, 12, 0.0);
    }
}

function getNombreFacturesPayees(PDO $pdo, int $annee): int {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM Document d
            WHERE d.statusDoc = :status
              AND d.typeDoc = 'Facture'
              AND d.datePaiement IS NOT NULL
              AND YEAR(d.datePaiement) = :annee
        ");
        $stmt->execute([
            'status' => 'Payé',
            'annee'  => $annee
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    } catch (PDOException $e) {
        error_log('Erreur getNombreFacturesPayees : ' . $e->getMessage());
        return 0;
    }
}

function getMontantFacturesPayees(PDO $pdo, int $annee): float {
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(ld.quantite * ld.prixUnitaire), 0) AS total_payee
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
              AND d.typeDoc = 'Facture'
              AND d.datePaiement IS NOT NULL
              AND YEAR(d.datePaiement) = :annee
        ");
        $stmt->execute([
            'status' => 'Payé',
            'annee'  => $annee
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total_payee'] ?? 0.0);
    } catch (PDOException $e) {
        error_log('Erreur getMontantFacturesPayees : ' . $e->getMessage());
        return 0.0;
    }
}

function getMontantPayeeParMois(PDO $pdo, int $annee): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                MONTH(d.datePaiement) AS mois,
                COALESCE(SUM(ld.quantite * ld.prixUnitaire), 0) AS total_paye
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
              AND d.typeDoc = 'Facture'
              AND d.datePaiement IS NOT NULL
              AND YEAR(d.datePaiement) = :annee
            GROUP BY MONTH(d.datePaiement)
            ORDER BY MONTH(d.datePaiement)
        ");
        $stmt->execute([
            'status' => 'Payé',
            'annee'  => $annee
        ]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $montantsParMois = array_fill(1, 12, 0.0);
        foreach ($result as $row) {
            $mois = (int)($row['mois'] ?? 0);
            if ($mois >= 1 && $mois <= 12) {
                $montantsParMois[$mois] = (float)($row['total_paye'] ?? 0.0);
            }
        }
        return $montantsParMois;

    } catch (PDOException $e) {
        error_log('Erreur getMontantPayeeParMois : ' . $e->getMessage());
        return array_fill(1, 12, 0.0);
    }
}

function getAnneesFactures(PDO $pdo): array {
    try {
        $stmt = $pdo->query("
            SELECT DISTINCT YEAR(datePaiement) AS annee
            FROM Document
            WHERE typeDoc = 'Facture'
            ORDER BY annee ASC
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $annees = array_values(array_filter(array_map(
            fn($row) => isset($row['annee']) ? (int)$row['annee'] : null,
            $result
        )));

        return $annees;
    } catch (PDOException $e) {
        error_log("Erreur getAnneesFactures : " . $e->getMessage());
        return [];
    }
}
