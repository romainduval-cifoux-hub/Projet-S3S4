<?php
function getNombreFacturesEnAttente(PDO $pdo): int {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM Document WHERE statusDoc = :status");
        $stmt->execute(['status' => 'en attente']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    } catch (PDOException $e) {
        error_log("Erreur getNombreFacturesEnAttente : " . $e->getMessage());
        return 0;
    }
}


function getMontantFactureEnAttente(PDO $pdo): float {
    try {
        $stmt = $pdo->prepare("
            SELECT SUM(ld.quantite * ld.prixUnitaire) AS total_attente
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
        ");

        $stmt->execute(['status' => 'en attente']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) $result['total_attente'];
    } catch (PDOException $e) {
        error_log("Erreur getMontantEnAttente : " . $e->getMessage());
        return 0.0;
    }
}


function getNombreFacturesPayees(PDO $pdo): int {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM Document WHERE statusDoc = :status");
        $stmt->execute(['status' => 'Payé']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];
    } catch (PDOException $e) {
        error_log("Erreur getNombreFacturesPayees : " . $e->getMessage());
        return 0;
    }
}

function getMontantFacturesPayees(PDO $pdo): float {
    try {
        $stmt = $pdo->prepare("
            SELECT SUM(ld.quantite * ld.prixUnitaire) AS total_payee
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
        ");

        $stmt->execute(['status' => 'Payé']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) $result['total_payee'];
    } catch (PDOException $e) {
        error_log("Erreur getMontantFacturesPayees : " . $e->getMessage());
        return 0.0;
    }
}


function getMontantEnAttenteParMois(PDO $pdo): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                MONTH(d.dateDoc) AS mois,
                SUM(ld.quantite * ld.prixUnitaire) AS total_attente
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = :status
              AND YEAR(d.dateDoc) = YEAR(CURDATE())
            GROUP BY MONTH(d.dateDoc)
            ORDER BY MONTH(d.dateDoc)
        ");

        $stmt->execute(['status' => 'en attente']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialisation d'un tableau pour tous les mois (0 si pas de facture)
        $montantsParMois = array_fill(1, 12, 0.0);

        foreach ($result as $row) {
            $mois = (int)$row['mois'];
            $montantsParMois[$mois] = (float)$row['total_attente'];
        }

        return $montantsParMois;

    } catch (PDOException $e) {
        error_log("Erreur getMontantEnAttenteParMois : " . $e->getMessage());
        return array_fill(1, 12, 0.0);
    }
}

function getMontantPayeeParMois(PDO $pdo): array {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                MONTH(d.dateDoc) AS mois,
                SUM(ld.quantite * ld.prixUnitaire) AS total_paye
            FROM Document d
            JOIN DetailDocument ld ON d.idDoc = ld.idDoc
            WHERE d.statusDoc = 'Payé'
                AND YEAR(d.dateDoc) = YEAR(CURDATE())
            GROUP BY MONTH(d.dateDoc)
            ORDER BY MONTH(d.dateDoc)
        ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // On initialise un tableau de 12 mois à zéro
        $montantsParMois = array_fill(1, 12, 0);

        foreach ($result as $row) {
            $mois = (int) $row['mois'];
            $montantsParMois[$mois] = (float) $row['total_paye'];
        }

        return $montantsParMois;
    } catch (PDOException $e) {
        error_log("Erreur getMontantPayeeParMois : " . $e->getMessage());
        return array_fill(1, 12, 0);
    }
}


function getAnneesFactures(PDO $pdo): array {
    try {
        $stmt = $pdo->query("
            SELECT DISTINCT YEAR(dateDoc) AS annee
            FROM Document
            ORDER BY annee ASC
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // On retourne un tableau simple d'années
        $annees = array_map(fn($row) => (int)$row['annee'], $result);

        return $annees;
    } catch (PDOException $e) {
        error_log("Erreur getAnneesFactures : " . $e->getMessage());
        return [];
    }
}
