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
            pc.id_salarie,
            pc.date_jour,
            pc.heure_debut,
            pc.heure_fin,
            pc.commentaire
        FROM planning_creneaux pc
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

        $id_salarie = (int)$row['id_salarie'];
        $date       = $row['date_jour'];

        // Le label visible dans la cellule
        $label = $row['commentaire'] ?: 'Intervention';

        $matrix[$id_salarie][$date][] = [
            'heure_debut' => $row['heure_debut'],
            'heure_fin'   => $row['heure_fin'],
            'label'       => $label
        ];
    }

    return $matrix;
}

