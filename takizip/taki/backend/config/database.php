<?php

declare(strict_types=1);

function load_env(): void
{
    $envPath = __DIR__ . '/../../.env';
    if (!file_exists($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (!getenv($key)) {
            putenv("{$key}={$value}");
        }
    }
}

load_env();

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('SUPABASE_DB_HOST') ?: (getenv('DB_HOST') ?: '127.0.0.1');
    $port = getenv('SUPABASE_DB_PORT') ?: (getenv('DB_PORT') ?: '5432');
    $name = getenv('SUPABASE_DB_NAME') ?: (getenv('DB_NAME') ?: 'postgres');
    $user = getenv('SUPABASE_DB_USER') ?: (getenv('DB_USER') ?: 'postgres');
    $pass = getenv('SUPABASE_DB_PASS') ?: (getenv('DB_PASS') ?: '');
    $sslMode = getenv('SUPABASE_DB_SSLMODE') ?: 'require';

    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;sslmode=%s', $host, $port, $name, $sslMode);

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
