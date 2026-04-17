<?php

function set_course_flash(string $type, string $message): void
{
    $_SESSION['course_flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function redirect_course_offers(): void
{
    header('Location: offres.php');
    exit();
}

function normalize_course_upload(array $file): array
{
    $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorCode !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Veuillez choisir une image valide.');
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('Image invalide.');
    }

    $raw = file_get_contents($tmpName);
    if ($raw === false || $raw === '') {
        throw new RuntimeException('Impossible de lire l image envoyee.');
    }

    $imageInfo = @getimagesize($tmpName);
    if (!$imageInfo || empty($imageInfo['mime']) || strpos((string) $imageInfo['mime'], 'image/') !== 0) {
        throw new RuntimeException('Le fichier choisi doit etre une image.');
    }

    $imageName = (string) ($file['name'] ?? 'image.jpg');
    $mime = (string) $imageInfo['mime'];

    if (!function_exists('imagecreatefromstring') || !function_exists('imagejpeg')) {
        if (strlen($raw) > 1024 * 1024) {
            throw new RuntimeException('Image trop lourde. Utilisez une image plus petite.');
        }

        return [
            'data' => $raw,
            'type' => $mime,
            'name' => $imageName,
        ];
    }

    $source = @imagecreatefromstring($raw);
    if (!$source) {
        if (strlen($raw) > 1024 * 1024) {
            throw new RuntimeException('Image trop lourde ou format non supporte.');
        }

        return [
            'data' => $raw,
            'type' => $mime,
            'name' => $imageName,
        ];
    }

    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);
    $maxWidth = 1600;
    $maxHeight = 1600;
    $ratio = min($maxWidth / max(1, $sourceWidth), $maxHeight / max(1, $sourceHeight), 1);
    $targetWidth = max(1, (int) round($sourceWidth * $ratio));
    $targetHeight = max(1, (int) round($sourceHeight * $ratio));

    $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
    if (!$canvas) {
        imagedestroy($source);
        throw new RuntimeException('Impossible de preparer l image.');
    }

    imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
    imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

    ob_start();
    imagejpeg($canvas, null, 78);
    $jpegData = (string) ob_get_clean();

    imagedestroy($canvas);
    imagedestroy($source);

    if ($jpegData === '') {
        throw new RuntimeException('Impossible de compresser l image.');
    }

    if (strlen($jpegData) > 1024 * 1024) {
        throw new RuntimeException('Image trop lourde apres compression. Choisissez une image plus petite.');
    }

    $safeBaseName = pathinfo($imageName, PATHINFO_FILENAME);
    if ($safeBaseName === '') {
        $safeBaseName = 'image';
    }

    return [
        'data' => $jpegData,
        'type' => 'image/jpeg',
        'name' => $safeBaseName . '.jpg',
    ];
}
