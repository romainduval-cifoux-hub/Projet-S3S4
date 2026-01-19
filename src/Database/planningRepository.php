<?php

/**
 * Retourne la liste des salariés pour tous les employés et selon la recherche (pour les lignes du planning)
 */
function getSalaries(PDO $pdo, ?string $search = null): array
{
    if ($search) {
        $search = trim($search);
        $parts  = preg_split('/\s+/', $search);

        // Si il y a le prenom et le nom dans la recherche ou inversement

        if (count($parts) >= 2) {
            
            $p1 = '%' . $parts[0] . '%';
            $p2 = '%' . $parts[1] . '%';

            $sql = "SELECT id_salarie, nom_salarie, prenom_salarie
                    FROM salaries
                    WHERE (prenom_salarie LIKE :p1 AND nom_salarie LIKE :p2)
                       OR (prenom_salarie LIKE :p2 AND nom_salarie LIKE :p1)
                    ORDER BY nom_salarie, prenom_salarie";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':p1' => $p1,
                ':p2' => $p2,
            ]);

        // Si il y a juste le prenom ou juste le nom dans la recherche
        } else {
            
            $like = '%' . $search . '%';
            $sql = "SELECT id_salarie, nom_salarie, prenom_salarie
                    FROM salaries
                    WHERE nom_salarie LIKE :q
                       OR prenom_salarie LIKE :q
                    ORDER BY nom_salarie, prenom_salarie";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':q' => $like]);
        }

    } else {
        //si il n'ya pas de recherche on affiche tous les salaries
        $sql = "SELECT id_salarie, nom_salarie, prenom_salarie
                FROM salaries
                ORDER BY nom_salarie, prenom_salarie";
        $stmt = $pdo->query($sql);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Retourne une matrice des créneaux sur une semaine :
 * $matrix[id_salarie][YYYY-MM-DD] = [ array de créneaux... ]
 *
 * $lundi = date du lundi de la semaine (format 'Y-m-d')
 */
function getWeekMatrix(PDO $pdo, string $lundi): array
{
    $start = $lundi;
    $end   = date('Y-m-d', strtotime($lundi . ' +4 day')); // lundi->vendredi

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

    $st = $pdo->prepare($sql);
    $st->execute([':start' => $start, ':end' => $end]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $matrix = [];
    foreach ($rows as $r) {
        $sid  = (int)$r['id_salarie'];
        $jour = $r['date_jour'];

        // IMPORTANT : on empile les créneaux du jour (matin + aprem)
        $matrix[$sid][$jour][] = $r;
    }

    return $matrix;
}


function getDayMatrix(PDO $pdo, string $date_jour): array
{
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
        WHERE pc.date_jour = :d
        ORDER BY pc.id_salarie, pc.heure_debut
    ";

    $st = $pdo->prepare($sql);
    $st->execute([':d' => $date_jour]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $matrix = [];

    foreach ($rows as $r) {
        $sid = (int)$r['id_salarie'];

        if (!isset($matrix[$sid])) $matrix[$sid] = [];

        // label comme tu fais partout
        if (!empty($r['nom_poste'])) $r['label'] = $r['nom_poste'];
        elseif (!empty($r['commentaire'])) $r['label'] = $r['commentaire'];
        else $r['label'] = 'Intervention';

        $matrix[$sid][] = $r;
    }

    return $matrix;
}


function getMonthCounts(PDO $pdo, string $monthStart, string $monthEnd): array
{
    // Retour: ['YYYY-MM-DD' => nb]
    $sql = "
        SELECT pc.date_jour, COUNT(*) AS nb
        FROM planning_creneaux pc
        WHERE pc.date_jour BETWEEN :d1 AND :d2
        GROUP BY pc.date_jour
    ";
    $st = $pdo->prepare($sql);
    $st->execute([
        ':d1' => $monthStart,
        ':d2' => $monthEnd
    ]);

    $out = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $out[$r['date_jour']] = (int)$r['nb'];
    }
    return $out;
}










