<?php

/** Liste des salariés (pour la colonne de gauche) */
function getSalaries(PDO $pdo): array {
    $sql = "SELECT id_salarie, nom_salarie, prenom_salarie
            FROM salaries
            ORDER BY nom_salarie, prenom_salarie";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/** Récupère (ou crée) un planning couvrant une période */
function getOrCreatePlanningId(PDO $pdo, string $nom, string $dateDebut, string $dateFin, int $managerId): int {
    // existe ?
    $st = $pdo->prepare("SELECT id_planning FROM plannings WHERE date_debut = ? AND date_fin = ? LIMIT 1");
    $st->execute([$dateDebut, $dateFin]);
    $id = $st->fetchColumn();
    if ($id) return (int)$id;

    // sinon crée
    $ins = $pdo->prepare("INSERT INTO plannings (nom_planning, date_debut, date_fin, id_manager, statut)
                          VALUES (?, ?, ?, ?, 'brouillon')");
    $ins->execute([$nom, $dateDebut, $dateFin, $managerId]);
    return (int)$pdo->lastInsertId();
}

/** Ajout d’un créneau avec contrôle anti-chevauchement (même salarié, même jour) */
function addCreneau(PDO $pdo, int $idPlanning, int $idSalarie, string $dateJour, string $heureDebut, string $heureFin, string $typeTravail = 'travail', ?int $idPoste = null, string $commentaire = ''): bool {
    // anti-chevauchement: (début < fin existante) ET (fin > début existante)
    $chk = $pdo->prepare("
        SELECT COUNT(*) FROM planning_creneaux
        WHERE id_salarie = :s
          AND date_jour = :d
          AND NOT (heure_fin <= :hdeb OR heure_debut >= :hfin)
    ");
    $chk->execute(['s'=>$idSalarie, 'd'=>$dateJour, 'hdeb'=>$heureDebut, 'hfin'=>$heureFin]);
    if ((int)$chk->fetchColumn() > 0) return false;

    $sql = "INSERT INTO planning_creneaux
              (id_planning, id_salarie, date_jour, heure_debut, heure_fin, type_travail, id_poste, commentaire)
            VALUES
              (:p, :s, :d, :hdeb, :hfin, :type, :poste, :com)";
    $st = $pdo->prepare($sql);
    return $st->execute([
        'p'=>$idPlanning, 's'=>$idSalarie, 'd'=>$dateJour,
        'hdeb'=>$heureDebut, 'hfin'=>$heureFin,
        'type'=>$typeTravail, 'poste'=>$idPoste, 'com'=>$commentaire
    ]);
}

/** Suppression d’un créneau */
function deleteCreneau(PDO $pdo, int $idCreneau): bool {
    return $pdo->prepare("DELETE FROM planning_creneaux WHERE id_creneau = ?")->execute([$idCreneau]);
}

/** Mise à jour d’un créneau (avec contrôle overlap) */
function updateCreneau(PDO $pdo, int $idCreneau, string $heureDebut, string $heureFin, ?int $idPoste = null, string $typeTravail='travail', string $commentaire=''): bool {
    // infos pour vérifier l’overlap
    $cur = $pdo->prepare("SELECT id_salarie, date_jour FROM planning_creneaux WHERE id_creneau = ?");
    $cur->execute([$idCreneau]);
    $row = $cur->fetch(PDO::FETCH_ASSOC);
    if (!$row) return false;

    $chk = $pdo->prepare("
        SELECT COUNT(*) FROM planning_creneaux
        WHERE id_salarie = :s AND date_jour = :d AND id_creneau <> :id
          AND NOT (heure_fin <= :hdeb OR heure_debut >= :hfin)
    ");
    $chk->execute([
        's'=>$row['id_salarie'], 'd'=>$row['date_jour'], 'id'=>$idCreneau,
        'hdeb'=>$heureDebut, 'hfin'=>$heureFin
    ]);
    if ((int)$chk->fetchColumn() > 0) return false;

    $up = $pdo->prepare("
        UPDATE planning_creneaux
           SET heure_debut = :hdeb, heure_fin = :hfin, id_poste = :poste, type_travail = :type, commentaire = :com
         WHERE id_creneau = :id
    ");
    return $up->execute([
        'hdeb'=>$heureDebut, 'hfin'=>$heureFin, 'poste'=>$idPoste, 'type'=>$typeTravail,
        'com'=>$commentaire, 'id'=>$idCreneau
    ]);
}

/**
 * Lecture des créneaux d’une semaine (lundi..dimanche)
 * Retourne une matrice: [id_salarie][YYYY-MM-DD] => [creneaux...]
 */
function getWeekMatrix(PDO $pdo, string $lundi): array {
    $start = new DateTime($lundi);
    $end   = (clone $start)->modify('+6 day');

    $st = $pdo->prepare("
      SELECT pc.id_creneau, pc.id_salarie, pc.date_jour, pc.heure_debut, pc.heure_fin,
             pc.type_travail, pc.commentaire,
             p.nom_poste, p.lieu,
             s.nom_salarie, s.prenom_salarie
        FROM planning_creneaux pc
        LEFT JOIN planning_postes p ON p.id_poste = pc.id_poste
        JOIN salaries s ON s.id_salarie = pc.id_salarie
       WHERE pc.date_jour BETWEEN :d1 AND :d2
       ORDER BY s.nom_salarie, s.prenom_salarie, pc.date_jour, pc.heure_debut
    ");
    $st->execute(['d1'=>$start->format('Y-m-d'), 'd2'=>$end->format('Y-m-d')]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $matrix = [];
    foreach ($rows as $r) {
        $matrix[$r['id_salarie']][$r['date_jour']][] = $r;
    }
    return $matrix;
}
