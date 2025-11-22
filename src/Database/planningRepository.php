<?php

/**
 * Retourne la liste des salariés (pour les lignes du planning)
 */
function getSalaries(PDO $pdo): array {
    $sql = "SELECT id_salarie, nom_salarie, prenom_salarie
            FROM salaries
            ORDER BY nom_salarie, prenom_salarie";
    $st = $pdo->query($sql);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Retourne une matrice des créneaux sur une semaine :
 * $matrix[id_salarie][YYYY-MM-DD] = [ array de créneaux... ]
 *
 * $lundi = date du lundi de la semaine (format 'Y-m-d')
 */
function getWeekMatrix(PDO $pdo, string $monday): array
{
    $start = $monday;                                      // lundi
    $end   = date('Y-m-d', strtotime($monday.' +4 days')); // vendredi

    // récupérer TOUS les créneaux des 5 jours
    $sql = "
        SELECT
            pc.id_creneau,
            pc.id_salarie,
            pc.date_jour,
            pc.heure_debut,
            pc.heure_fin,
            pc.type_travail,
            pc.commentaire,
            pp.nom_poste
        FROM planning_creneaux pc
        LEFT JOIN planning_postes pp ON pc.id_poste = pp.id_poste
        WHERE pc.date_jour BETWEEN :start AND :end
        ORDER BY pc.id_salarie, pc.date_jour, pc.heure_debut
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':start' => $start,
        ':end'   => $end
    ]);

    $matrix = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $idS  = (int)$row['id_salarie'];
        $jour = $row['date_jour'];

        if (!isset($matrix[$idS][$jour])) {
            $matrix[$idS][$jour] = [];
        }

        $matrix[$idS][$jour][] = [
            'id_creneau'   => (int)$row['id_creneau'],
            'heure_debut'  => $row['heure_debut'],
            'heure_fin'    => $row['heure_fin'],
            'type_travail' => $row['type_travail'],
            'commentaire'  => $row['commentaire'],
            'nom_poste'    => $row['nom_poste'],
        ];
    }

    return $matrix;
}

