<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$config = [];
$configFile = __DIR__ . '/../config.ini';
if (file_exists($configFile)) {
    $parsed = parse_ini_file($configFile, true);
    if (is_array($parsed)) {
        $config = $parsed;
    }
}

function env(string $key, $default = null)
{
    if (array_key_exists($key, $_ENV)) {
        $v = $_ENV[$key];
        return ($v === '' || $v === null) ? $default : $v;
    }

    $v = getenv($key);
    if ($v !== false && $v !== '') {
        return $v;
    }

    if (array_key_exists($key, $_SERVER)) {
        $v = $_SERVER[$key];
        return ($v === '' || $v === null) ? $default : $v;
    }

    return $default;
}

define('DB_HOST', env('DB_HOST', $config['database']['host'] ?? 'localhost'));
define('DB_PORT', env('DB_PORT', $config['database']['port'] ?? '3306'));
define('DB_NAME', env('DB_NAME', $config['database']['name'] ?? 'teamjardin'));
define('DB_USER', env('DB_USER', $config['database']['user'] ?? 'root'));
define('DB_PASS', env('DB_PASS', $config['database']['pass'] ?? ''));
define('DB_SSLMODE', env('DB_SSLMODE', $config['database']['sslmode'] ?? ''));

define('MAILGUN_API_KEY', (string) env('MAILGUN_API_KEY', ''));

define(
    'MAILGUN_DOMAIN',
    (string) env(
        'MAILGUN_DOMAIN',
        $config['mailgun']['domain'] ?? 'sandboxc2bf159c8d384bfd98a19f3948da7db1.mailgun.org'
    )
);

define(
    'MAIL_FROM',
    (string) env(
        'MAIL_FROM',
        $config['mailgun']['from'] ?? 'Team Jardin <postmaster@sandboxc2bf159c8d384bfd98a19f3948da7db1.mailgun.org>'
    )
);

$envBaseUrl  = (string) env('BASE_URL', '');
$fileBaseUrl = (string) ($config['app']['base_url'] ?? '');

$baseUrl = trim($envBaseUrl);
if ($baseUrl === '') {
    $baseUrl = trim($fileBaseUrl);
}
$baseUrl = rtrim($baseUrl, '/');

if ($baseUrl === '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? '';
    if ($host !== '') {
        $baseUrl = $scheme . '://' . $host;
    }
}

$baseUrl = preg_replace('#^http://#i', 'https://', $baseUrl);

define('BASE_URL', $baseUrl);
