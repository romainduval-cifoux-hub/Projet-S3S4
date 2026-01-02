<?php

function getNombreFacturesEnAttente(PDO $pdo, int $annee): int {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM Document
            WHERE statusDoc = :status
              AND YEAR(datePaiement) = :annee
        ");
        $stmt->execute([
            'status' => 'en attente',
            'annee'  => $annee
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    } catch (PDOException $e) {
        error_log("Erreur getNombreFacturesEnAttente : " . $e->getMessage());
        return 0;
    }
}

function getMontantFactureEnAttente(PDO $pdo, int $annee): float {
    try {
        $stmt = $pdo->prepare("
            SELECT SUM(ld.quantite * ld.prixUnitaire) AS total_attente
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
              AND YEAR(d.datePaiement) = :annee
        ");
        $stmt->execute([
            'status' => 'en attente',
            'annee'  => $annee
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($result['total_attente'] ?? 0.0);
    } catch (PDOException $e) {
        error_log("Erreur getMontantEnAttente : " . $e->getMessage());
        return 0.0;
    }
}

function getMontantEnAttenteParMois(PDO $pdo, int $annee): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                MONTH(d.datePaiement) AS mois,
                SUM(ld.quantite * ld.prixUnitaire) AS total_attente
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
              AND YEAR(d.datePaiement) = :annee
            GROUP BY MONTH(d.datePaiement)
            ORDER BY MONTH(d.datePaiement)
        ");
        $stmt->execute([
            'status' => 'en attente',
            'annee'  => $annee
        ]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $montantsParMois = array_fill(1, 12, 0.0);
        foreach ($result as $row) {
            $mois = (int) ($row['mois'] ?? 0);
            if ($mois >= 1 && $mois <= 12) {
                $montantsParMois[$mois] = (float) ($row['total_attente'] ?? 0.0);
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
            FROM Document
            WHERE statusDoc = :status
              AND datePaiement IS NOT NULL
              AND YEAR(datePaiement) = :annee
        ");
        $stmt->execute([
            'status' => 'Payé',
            'annee'  => $annee
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['total'] ?? 0);
    } catch (PDOException $e) {
        error_log('Erreur getNombreFacturesPayees : ' . $e->getMessage());
        return 0;
    }
}

function getMontantFacturesPayees(PDO $pdo, int $annee): float {
    try {
        $stmt = $pdo->prepare("
            SELECT SUM(ld.quantite * ld.prixUnitaire) AS total_payee
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
              AND d.datePaiement IS NOT NULL
              AND YEAR(d.datePaiement) = :annee
        ");
        $stmt->execute([
            'status' => 'Payé',
            'annee'  => $annee
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($result['total_payee'] ?? 0.0);
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
                SUM(ld.quantite * ld.prixUnitaire) AS total_paye
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
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
            $mois = (int) ($row['mois'] ?? 0);
            if ($mois >= 1 && $mois <= 12) {
                $montantsParMois[$mois] = (float) ($row['total_paye'] ?? 0.0);
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
            ORDER BY annee ASC
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $annees = array_map(fn($row) => (int)$row['annee'], $result);

        return $annees;
    } catch (PDOException $e) {
        error_log("Erreur getAnneesFactures : " . $e->getMessage());
        return [];
    }
}
