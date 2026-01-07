<?php

class AdminRealisationRepository {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT r.*, c.nom AS categorie_nom
            FROM realisations r
            LEFT JOIN categories c ON r.categorie_id = c.id
            ORDER BY r.date_creation DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM realisations WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO realisations (photo, commentaire, categorie_id, favoris) 
             VALUES (:photo, :commentaire, :categorie_id, :favoris)"
        );
        return $stmt->execute([
            'photo' => $data['photo'],
            'commentaire' => $data['commentaire'],
            'categorie_id' => $data['categorie_id'],
            'favoris' => $data['favoris'] ?? 0
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE realisations 
             SET photo = :photo, commentaire = :commentaire, categorie_id = :categorie_id, favoris = :favoris
             WHERE id = :id"
        );
        return $stmt->execute([
            'id' => $id,
            'photo' => $data['photo'],
            'commentaire' => $data['commentaire'],
            'categorie_id' => $data['categorie_id'],
            'favoris' => $data['favoris'] ?? 0
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM realisations WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getAllCategories(): array {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM categories ORDER BY nom ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search(array $filters): array {
        $sql = "
            SELECT r.*, c.nom AS categorie_nom
            FROM realisations r
            LEFT JOIN categories c ON r.categorie_id = c.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['q'])) {
            $sql .= " AND r.commentaire LIKE :q";
            $params['q'] = '%' . $filters['q'] . '%';
        }

        if ($filters['categorie_id'] !== null && $filters['categorie_id'] !== '') {
            $sql .= " AND r.categorie_id = :categorie_id";
            $params['categorie_id'] = (int)$filters['categorie_id'];
        }

        if ($filters['favoris'] !== null && $filters['favoris'] !== '') {
            $sql .= " AND r.favoris = :favoris";
            $params['favoris'] = (int)$filters['favoris'];
        }

        $sql .= " ORDER BY r.date_creation DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
