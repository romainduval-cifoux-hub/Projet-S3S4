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
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
}
