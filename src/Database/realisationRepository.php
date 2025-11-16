<?php

class RealisationRepository {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllCategories(): array {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM categories ORDER BY nom ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRealisationsByCategory(int $categoryId): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, photo, commentaire, date_creation
            FROM realisations
            WHERE categorie_id = :category_id
            ORDER BY date_creation DESC"
        );
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRealisationsWithCategory(): array {
        $stmt = $this->pdo->prepare(
            "SELECT r.id, r.photo, r.commentaire, r.date_creation,
                    c.id AS category_id, c.nom AS category_name
            FROM realisations r
            JOIN categories c ON r.categorie_id = c.id
            ORDER BY c.nom ASC, r.date_creation DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRealisationsFavorites(): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, photo, commentaire, date_creation
            FROM realisations
            WHERE favoris > 0
            ORDER BY favoris"
        ) ;
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
