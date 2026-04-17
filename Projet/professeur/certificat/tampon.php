<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(0);

$rawName = trim((string) ($_GET['nom'] ?? 'PROFESSEUR'));
if ($rawName === '') {
    $rawName = 'PROFESSEUR';
}

$nom_prof = function_exists('mb_strtoupper')
    ? mb_strtoupper($rawName, 'UTF-8')
    : strtoupper($rawName);

$safeName = htmlspecialchars($nom_prof, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

header('Content-Type: image/svg+xml; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400" role="img" aria-label="Tampon officiel">
    <defs>
        <radialGradient id="sealGlow" cx="50%" cy="50%" r="60%">
            <stop offset="0%" stop-color="#fff9e8" stop-opacity="0.45" />
            <stop offset="100%" stop-color="#fff9e8" stop-opacity="0" />
        </radialGradient>
    </defs>

    <circle cx="200" cy="200" r="180" fill="url(#sealGlow)" />
    <circle cx="200" cy="200" r="176" fill="none" stroke="#b8860b" stroke-width="8" />
    <circle cx="200" cy="200" r="128" fill="none" stroke="#b8860b" stroke-width="3" opacity="0.95" />

    <text x="200" y="118" text-anchor="middle" fill="#b8860b" font-family="Arial, Helvetica, sans-serif" font-size="34" font-weight="700" letter-spacing="6">ENJAH</text>
    <text x="200" y="215" text-anchor="middle" fill="#b8860b" font-family="Arial, Helvetica, sans-serif" font-size="22" font-weight="700">TAMPON</text>
    <text x="200" y="245" text-anchor="middle" fill="#b8860b" font-family="Arial, Helvetica, sans-serif" font-size="18" font-weight="600">OFFICIEL</text>

    <text x="200" y="182" text-anchor="middle" fill="#8f6a08" font-family="Georgia, 'Times New Roman', serif" font-size="16" font-style="italic">Professeur</text>
    <text x="200" y="188" text-anchor="middle" fill="#8f6a08" font-family="Georgia, 'Times New Roman', serif" font-size="24" font-weight="700"><?php echo $safeName; ?></text>

    <path d="M 90 282 C 130 252, 270 252, 310 282" fill="none" stroke="#b8860b" stroke-width="3" opacity="0.9" />
    <circle cx="200" cy="325" r="8" fill="#b8860b" />
</svg>