<?php
session_start();

ini_set('display_errors', 1);          
ini_set('display_startup_errors', 1);  
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
define('MAILGUN_API_KEY', getenv('MAILGUN_API_KEY')); 
define(
    'MAILGUN_DOMAIN',
    'sandboxc2bf159c8d384bfd98a19f3948da7db1.mailgun.org'
);
define(
    'MAIL_FROM',
    'Team Jardin <postmaster@sandboxc2bf159c8d384bfd98a19f3948da7db1.mailgun.org>'
);

$envBaseUrl  = getenv('BASE_URL');
$fileBaseUrl = $config['app']['base_url'] ?? '';


$baseUrl = $envBaseUrl !== false ? rtrim($envBaseUrl, '/') : rtrim($fileBaseUrl, '/');
define('BASE_URL', $baseUrl);
