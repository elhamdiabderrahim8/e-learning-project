<?php

declare(strict_types=1);

function load_env(): void
{
    $envPaths = [
        __DIR__ . '/.env',
        __DIR__ . '/../.env',
    ];

    $envPath = null;
    foreach ($envPaths as $candidate) {
        if (file_exists($candidate)) {
            $envPath = $candidate;
            break;
        }
    }

    if ($envPath === null) {
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
    $host = "localhost";
    $port = "3306";
    $name = "elearning";
    $user = "root";
    $pass = "";
    $charset = "utf8mb4";

    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=$charset";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
