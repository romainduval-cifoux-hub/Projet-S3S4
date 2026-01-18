<?php

function getAvisEtMoyenne(PDO $pdo): array
{
    $sql = "
        SELECT 
            c.nom_client AS nom,
            c.prenom_client AS prenom,
            c.photo AS photo,
            a.date_commentaire AS date,
            a.commentaire,
            a.note
        FROM avis a
        INNER JOIN users u   ON a.user_id = u.id
        INNER JOIN clients c ON c.user_id = u.id
        ORDER BY a.date_commentaire DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcul moyenne
    $moyenne = 0;
    if (count($avis) > 0) {
        $totalNotes = 0;
        foreach ($avis as $a) {
            $totalNotes += (int)$a['note'];
        }
        $moyenne = round($totalNotes / count($avis));
    }

    return [$avis, $moyenne];
}

