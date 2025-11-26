<?php
require_once __DIR__ . '/../config.php'; 

function getPDO(
    string $host,
    string $name,
    string $user,
    string $pass,
    string $port = '3306'
): PDO {
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    try {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 5,
        ];

        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;

    } catch (PDOException $e) {
        error_log('Erreur de connexion PDO : ' . $e->getMessage());
        http_response_code(500);
        exit('Erreur de connexion à la base de données.');
    }
}
