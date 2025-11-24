<?php
/**
 * Récupère un créneau par son id
 */
function ch_getCreneauById(PDO $pdo, int $id): ?array
{
    $sql = "SELECT 
                id_creneau,
                id_planning,
                id_salarie,
                date_jour,
                heure_debut,
                heure_fin,
                type_travail,
                commentaire
            FROM planning_creneaux
            WHERE id_creneau = ?";
    $st = $pdo->prepare($sql);
    $st->execute([$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Supprime un créneau par son id
 */
function ch_deleteCreneau(PDO $pdo, int $id_creneau): bool
{
    $st = $pdo->prepare("DELETE FROM planning_creneaux WHERE id_creneau = ?");
    return $st->execute([$id_creneau]);
}


/**
 * Met à jour un créneau selon les nouveau attributs
 */
function ch_updateCreneau(
    PDO $pdo,
    int $id_creneau,
    int $id_salarie,
    string $date_jour,
    string $periode,
    ?int $id_client,
    string $commentaire
): bool {

    // périodes → heure
    if ($periode === 'am') {
        $hDeb = '08:00:00';
        $hFin = '12:00:00';
    } else {
        $hDeb = '13:00:00';
        $hFin = '17:00:00';
    }

    // Préfix client si présent
    if ($id_client !== null) {
        $client = ch_getClientFullName($pdo, $id_client);
        if ($client) {
            $commentaire = "Chantier chez $client" . ($commentaire ? " – $commentaire" : "");
        }
    }

    $sql = "UPDATE planning_creneaux
            SET id_salarie = :s,
                date_jour = :d,
                heure_debut = :h1,
                heure_fin = :h2,
                commentaire = :c
            WHERE id_creneau = :id";

    $st = $pdo->prepare($sql);

    return $st->execute([
        ':s' => $id_salarie,
        ':d' => $date_jour,
        ':h1'=> $hDeb,
        ':h2'=> $hFin,
        ':c' => $commentaire,
        ':id'=> $id_creneau
    ]);
}


/**
 * Récupère la liste des salariés pour affecter un chantier.
 */



function ch_getSalaries(PDO $pdo): array
{
    $sql = "SELECT id_salarie, nom_salarie, prenom_salarie
            FROM salaries
            ORDER BY nom_salarie, prenom_salarie";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère la liste des clients.
 */
function ch_getClients(PDO $pdo): array
{
    $sql = "SELECT id_client, nom_client, prenom_client
            FROM clients
            ORDER BY nom_client, prenom_client";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retourne "Prénom Nom" du client ou null.
 */
function ch_getClientFullName(PDO $pdo, int $id_client): ?string
{
    $st = $pdo->prepare("SELECT nom_client, prenom_client FROM clients WHERE id_client = ?");
    $st->execute([$id_client]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) return null;

    return $row['prenom_client'] . ' ' . $row['nom_client'];
}

/**
 * Retourne l'id_planning pour la semaine de $dateJour (lundi–vendredi) et ce manager.
 * Crée un planning s'il n'existe pas encore.
 */
function ch_getOrCreatePlanningForDate(PDO $pdo, int $managerId, string $dateJour, string $commentaireInitial = 'Planning généré automatiquement'): int
{
    $ts       = strtotime($dateJour);
    $lundi    = date('Y-m-d', strtotime('monday this week', $ts));
    $vendredi = date('Y-m-d', strtotime($lundi . ' +4 day'));

    // Chercher un planning existant
    $stPlan = $pdo->prepare("
        SELECT id_planning 
        FROM plannings
        WHERE id_manager = :m
          AND date_debut = :d
          AND date_fin   = :f
        LIMIT 1
    ");
    $stPlan->execute([
        ':m' => $managerId,
        ':d' => $lundi,
        ':f' => $vendredi
    ]);

    $planning = $stPlan->fetch(PDO::FETCH_ASSOC);
    if ($planning) {
        return (int)$planning['id_planning'];
    }

    // Sinon on le crée
    $nom_planning = "Semaine du " . date('d/m', strtotime($lundi)) . " au " . date('d/m', strtotime($vendredi));

    $stInsertPlan = $pdo->prepare("
        INSERT INTO plannings (nom_planning, date_debut, date_fin, id_manager, statut, commentaire)
        VALUES (:nom, :d, :f, :m, 'brouillon', :com)
    ");
    $stInsertPlan->execute([
        ':nom' => $nom_planning,
        ':d'   => $lundi,
        ':f'   => $vendredi,
        ':m'   => $managerId,
        ':com' => $commentaireInitial
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Crée un créneau de demi-journée pour un salarié à une date donnée.
 *
 * $periode = 'am' (matin 8–12) ou 'pm' (après-midi 13–17)
 * $id_client optionnel → si fourni, on préfixe le commentaire avec "Chantier chez Prénom Nom".
 *
 * Gère aussi la création / récupération du planning de la semaine,
 * et le tout est fait dans une transaction.
 */
function ch_createCreneauAvecPlanning(
    PDO $pdo,
    int $managerId,
    int $id_salarie,
    string $date_jour,
    string $periode,
    ?int $id_client = null,
    string $commentaire = ''
): bool {
    // Normaliser la période
    $periode = ($periode === 'pm') ? 'pm' : 'am';

    // Déterminer le créneau horaire
    if ($periode === 'am') {
        $heure_debut = '08:00:00';
        $heure_fin   = '12:00:00';
    } else {
        $heure_debut = '13:00:00';
        $heure_fin   = '17:00:00';
    }

    // Si un client est précisé, préfixer le commentaire
    if ($id_client !== null) {
        $clientFullName = ch_getClientFullName($pdo, $id_client);
        if ($clientFullName !== null) {
            $prefix = "Chantier chez " . $clientFullName;
            $commentaire = $prefix . ($commentaire ? " – " . $commentaire : "");
        }
    }

    try {
        $pdo->beginTransaction();

        // 1) Récupérer / créer le planning de la bonne semaine
        $id_planning = ch_getOrCreatePlanningForDate($pdo, $managerId, $date_jour);

        // 2) Insérer le créneau
        $stCreneau = $pdo->prepare("
            INSERT INTO planning_creneaux
                (id_planning, id_salarie, date_jour, heure_debut, heure_fin, type_travail, commentaire)
            VALUES
                (:plan, :sal, :jour, :hdeb, :hfin, 'travail', :com)
        ");
        $stCreneau->execute([
            ':plan' => $id_planning,
            ':sal'  => $id_salarie,
            ':jour' => $date_jour,
            ':hdeb' => $heure_debut,
            ':hfin' => $heure_fin,
            ':com'  => $commentaire
        ]);

        $pdo->commit();
        return true;

    } catch (Throwable $e) {
        $pdo->rollBack();
        // tu peux logger $e->getMessage() ici si besoin
        return false;
    }


}







/**
 * Retourne tous les créneaux d'un salarié pour un jour donné.
 */
function ch_getCreneauxJour(PDO $pdo, int $id_salarie, string $date_jour): array
{
    $sql = "SELECT 
                id_creneau,
                heure_debut,
                heure_fin
            FROM planning_creneaux
            WHERE id_salarie = :s 
              AND date_jour  = :d";
    
    $st = $pdo->prepare($sql);
    $st->execute([
        ':s' => $id_salarie,
        ':d' => $date_jour
    ]);

    return $st->fetchAll(PDO::FETCH_ASSOC);
}
