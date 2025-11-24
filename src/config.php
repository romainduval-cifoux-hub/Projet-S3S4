<?php
session_start();

ini_set('display_errors', 1);          // en prod: 0
ini_set('display_startup_errors', 1);  // en prod: 0
error_reporting(E_ALL);

// Lecture éventuelle de config.ini (local)
$config = [];
$configFile = __DIR__ . '/../config.ini';
if (file_exists($configFile)) {
    $config = parse_ini_file($configFile, true);
}

// 1) Variables d'env (DigitalOcean)
// 2) config.ini (local)
// 3) valeurs par défaut
define('DB_HOST', getenv('DB_HOST') ?: ($config['database']['host'] ?? 'localhost'));
define('DB_PORT', getenv('DB_PORT') ?: ($config['database']['port'] ?? '3306'));
define('DB_NAME', getenv('DB_NAME') ?: ($config['database']['name'] ?? 'teamjardin'));
define('DB_USER', getenv('DB_USER') ?: ($config['database']['user'] ?? 'root'));
define('DB_PASS', getenv('DB_PASS') ?: ($config['database']['pass'] ?? ''));

define('BASE_URL', $config['app']['base_url'] ?? '/SAE/Projet-S3S4');
