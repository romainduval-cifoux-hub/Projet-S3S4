<?php

require_once __DIR__ . '/chantierRepository.php'; // pour ch_getOrCreatePlanningForDate


function conge_createDemande(
    PDO $pdo,
    int $id_salarie,
    string $date_debut,
    string $date_fin,
    string $motif
): bool {
    $sql = "INSERT INTO conges (id_salarie, date_debut, date_fin, motif, statut)
            VALUES (:s, :dd, :df, :motif, 'en_attente')";
    $st = $pdo->prepare($sql);
    return $st->execute([
        ':s'     => $id_salarie,
        ':dd'    => $date_debut,
        ':df'    => $date_fin,
        ':motif' => $motif
    ]);
}

/**
 * Demandes d'un salarié (pour affichage côté employé)
 */
function conge_getBySalarie(PDO $pdo, int $id_salarie): array
{
    $sql = "SELECT *
            FROM conges
            WHERE id_salarie = :s
            ORDER BY date_demande DESC";
    $st = $pdo->prepare($sql);
    $st->execute([':s' => $id_salarie]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Demandes en attente pour l'admin
 */
function conge_getEnAttente(PDO $pdo): array
{
    $sql = "SELECT 
                c.*,
                sa.prenom_salarie,
                sa.nom_salarie,
                u.username
            FROM conges c
            JOIN users u ON u.id = c.id_salarie
            LEFT JOIN salaries sa ON sa.id_salarie = c.id_salarie
            WHERE c.statut = 'en_attente'
            ORDER BY c.date_demande ASC";
    
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupérer une demande
 */
function conge_getById(PDO $pdo, int $id_conge): ?array
{
    $st = $pdo->prepare("SELECT * FROM conges WHERE id_conge = ?");
    $st->execute([$id_conge]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Met à jour le statut + infos décision
 */
function conge_updateStatut(
    PDO $pdo,
    int $id_conge,
    string $statut,
    int $id_manager
): bool {
    $sql = "UPDATE conges
            SET statut = :st,
                id_manager_decision = :m,
                date_decision = NOW()
            WHERE id_conge = :id";
    $st = $pdo->prepare($sql);
    return $st->execute([
        ':st' => $statut,
        ':m'  => $id_manager,
        ':id' => $id_conge
    ]);
}




/**
 * Pour chaque jour ouvré entre date_debut et date_fin,
 * crée 2 créneaux "congé" (matin + après-midi) pour le salarié.
 */
function conge_appliquerAuPlanning(PDO $pdo, int $id_salarie, int $managerId, string $date_debut, string $date_fin): bool
{
    $tsStart = strtotime($date_debut);
    $tsEnd   = strtotime($date_fin);

    try {
        $pdo->beginTransaction();

        for ($ts = $tsStart; $ts <= $tsEnd; $ts += 86400) {
            $dayOfWeek = (int)date('N', $ts); // 1 = lun, 7 = dim
            if ($dayOfWeek > 5) {
                continue; // on saute samedi/dimanche
            }

            $jour = date('Y-m-d', $ts);

            // 1) Planning de la semaine
            $id_planning = ch_getOrCreatePlanningForDate($pdo, $managerId, $jour, 'Planning congés auto');

            // 2) On peut choisir de supprimer les créneaux existants ce jour-là
            //    OU refuser s'il y a déjà qqch. Ici, on refuse s'il y a du travail.
            $stChk = $pdo->prepare("
                SELECT COUNT(*) 
                FROM planning_creneaux
                WHERE id_salarie = :s
                  AND date_jour = :d
                  AND type_travail <> 'congé'
            ");
            $stChk->execute([':s' => $id_salarie, ':d' => $jour]);
            $nb = (int)$stChk->fetchColumn();

            if ($nb > 0) {
                // on peut choisir de lancer une exception pour tout annuler
                throw new Exception("Conflit planning sur $jour");
            }

            // 3) On insère 2 demi-journées de congé
            $insert = $pdo->prepare("
                INSERT INTO planning_creneaux
                    (id_planning, id_salarie, date_jour, heure_debut, heure_fin, type_travail, commentaire)
                VALUES
                    (:p, :s, :d, :h1, :h2, 'congé', :com),
                    (:p, :s, :d, :h3, :h4, 'congé', :com)
            ");
            $insert->execute([
                ':p'   => $id_planning,
                ':s'   => $id_salarie,
                ':d'   => $jour,
                ':h1'  => '08:00:00',
                ':h2'  => '12:00:00',
                ':h3'  => '13:00:00',
                ':h4'  => '17:00:00',
                ':com' => 'Congé'
            ]);
        }

        $pdo->commit();
        return true;

    } catch (Throwable $e) {
        $pdo->rollBack();
        // tu peux logger $e->getMessage()
        return false;
    }
}




