param(
    [string]$EnvFile = ".env",
    [string]$MysqlExe = "",
    [switch]$SkipSeed
)

$ErrorActionPreference = "Stop"

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$envPath = if ([System.IO.Path]::IsPathRooted($EnvFile)) { $EnvFile } else { Join-Path $scriptDir $EnvFile }

$config = @{}
if (Test-Path -Path $envPath) {
    foreach ($rawLine in (Get-Content -Path $envPath)) {
        $line = $rawLine.Trim()
        if ($line -eq "" -or $line.StartsWith("#") -or -not $line.Contains("=")) {
            continue
        }

        $parts = $line.Split("=", 2)
        $key = $parts[0].Trim()
        $value = $parts[1].Trim().Trim('"').Trim("'")
        $config[$key] = $value
    }
}

$dbHost = if ($config.ContainsKey("DB_HOST")) { $config["DB_HOST"] } else { "127.0.0.1" }
$dbPort = if ($config.ContainsKey("DB_PORT")) { $config["DB_PORT"] } else { "3306" }
$dbName = if ($config.ContainsKey("DB_NAME")) { $config["DB_NAME"] } else { "elearning" }
$dbUser = if ($config.ContainsKey("DB_USER")) { $config["DB_USER"] } else { "root" }
$dbPass = if ($config.ContainsKey("DB_PASS")) { $config["DB_PASS"] } else { "" }

$schemaFile = Join-Path $scriptDir "schema.sql"
$seedFile = Join-Path $scriptDir "seed.sql"

if (-not (Test-Path -Path $schemaFile)) {
    throw "schema.sql not found in $scriptDir"
}

if ($MysqlExe -eq "") {
    $candidates = @(
        "mysql",
        "C:\\xampp\\mysql\\bin\\mysql.exe",
        "C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe"
    )

    foreach ($candidate in $candidates) {
        if ($candidate -eq "mysql") {
            $cmd = Get-Command mysql -ErrorAction SilentlyContinue
            if ($cmd) {
                $MysqlExe = $cmd.Source
                break
            }
        } elseif (Test-Path -Path $candidate) {
            $MysqlExe = $candidate
            break
        }
    }
}

if ($MysqlExe -eq "") {
    throw "mysql client not found. Install XAMPP MySQL or pass -MysqlExe path."
}

$authArgs = @("-h", $dbHost, "-P", $dbPort, "-u", $dbUser)
if ($dbPass -ne "") {
    $authArgs += "-p$dbPass"
}

Write-Host "Using mysql client: $MysqlExe" -ForegroundColor Cyan
Write-Host "Target DB: $dbName on ${dbHost}:$dbPort" -ForegroundColor Cyan

& $MysqlExe @authArgs -e "CREATE DATABASE IF NOT EXISTS ``$dbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if ($LASTEXITCODE -ne 0) {
    throw "Failed to create or verify database '$dbName'."
}

(Get-Content -Path $schemaFile -Raw) | & $MysqlExe @authArgs $dbName
if ($LASTEXITCODE -ne 0) {
    throw "Failed to import schema.sql"
}

if (-not $SkipSeed -and (Test-Path -Path $seedFile)) {
    (Get-Content -Path $seedFile -Raw) | & $MysqlExe @authArgs $dbName
    if ($LASTEXITCODE -ne 0) {
        throw "Failed to import seed.sql"
    }
    Write-Host "Seed imported successfully." -ForegroundColor Green
}

Write-Host "MySQL setup completed successfully." -ForegroundColor Green
Write-Host "You can now run: php -S localhost:8000 -t kmr/student" -ForegroundColor Green
