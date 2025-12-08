<?php

function getAvisEtMoyenne(PDO $pdo): array
{
    $sql = "
        SELECT 
            c.nom_client     AS nom,
            c.prenom_client  AS prenom,
            a.date_commentaire AS date,
            a.commentaire,
            a.note
        FROM avis a
        INNER JOIN users u   ON a.user_id = u.id
        INNER JOIN clients c ON c.id_client = u.id
        ORDER BY a.date_commentaire DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ajout de la photo (fixe) pour chaque avis
    foreach ($avis as &$item) {
        $item['photo'] = BASE_URL . '/public/assets/vitrine/img/pp1.jpg';
    }
    unset($item);

    // Calcul de la moyenne
    $moyenne = 0;
    if (count($avis) > 0) {
        $totalNotes = 0;
        foreach ($avis as $a) {
            $totalNotes += (int)$a['note'];
        }
        $moyenne = round($totalNotes / count($avis)); // entier entre 1 et 5
    }

    // On renvoie les deux infos
    return [$avis, $moyenne];
}
