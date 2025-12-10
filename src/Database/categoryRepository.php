<?php
require_once __DIR__ . '/db.php';

class CategoryRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories ORDER BY nom ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAllCategories : " . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getCategoryById : " . $e->getMessage());
            return null;
        }
    }

    public function create(string $nom): bool {
        $stmt = $this->pdo->prepare("INSERT INTO categories (nom) VALUES (:nom)");
        return $stmt->execute(['nom' => $nom]);
    }

    public function update(int $id, string $nom): bool {
        $stmt = $this->pdo->prepare("UPDATE categories SET nom = :nom WHERE id = :id");
        return $stmt->execute(['nom' => $nom, 'id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
