<?php

declare(strict_types=1);

function ensureDirectory(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

function canWrite(string $path): bool
{
    $dir = is_dir($path) ? $path : dirname($path);
    if (!is_dir($dir)) {
        return false;
    }
    $tmp = @tempnam($dir, 'webp-');
    if ($tmp === false) {
        return false;
    }
    @unlink($tmp);
    return true;
}

function loadImage(string $sourcePath, string $ext)
{
    if ($ext === 'jpg' || $ext === 'jpeg') {
        return @imagecreatefromjpeg($sourcePath);
    }
    if ($ext === 'png') {
        $img = @imagecreatefrompng($sourcePath);
        if ($img !== false) {
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
        }
        return $img;
    }
    if ($ext === 'gif') {
        $img = @imagecreatefromgif($sourcePath);
        if ($img !== false) {
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);
        }
        return $img;
    }
    return false;
}

function convertToWebp(string $sourcePath, string $destPath, int $quality): array
{
    $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
    $img = loadImage($sourcePath, $ext);
    if ($img === false) {
        return ['ok' => false, 'error' => 'cannot_load'];
    }

    ensureDirectory(dirname($destPath));
    if (!canWrite($destPath)) {
        imagedestroy($img);
        return ['ok' => false, 'error' => 'cannot_write'];
    }

    $result = @imagewebp($img, $destPath, $quality);
    imagedestroy($img);

    if (!$result) {
        return ['ok' => false, 'error' => 'webp_failed'];
    }

    clearstatcache(true, $destPath);
    return ['ok' => true, 'bytes' => filesize($destPath) ?: 0];
}

function shouldConvert(string $sourcePath, string $destPath): bool
{
    if (!file_exists($destPath)) {
        return true;
    }
    return filemtime($destPath) < filemtime($sourcePath);
}

function scanFiles(string $dir): array
{
    $out = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $fileInfo) {
        if (!$fileInfo->isFile()) {
            continue;
        }
        $ext = strtolower($fileInfo->getExtension());
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif'], true)) {
            continue;
        }
        $out[] = $fileInfo->getPathname();
    }
    return $out;
}

$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "Cannot resolve project root\n");
    exit(1);
}

$graphicsDir = $root . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'graphics';
if (!is_dir($graphicsDir)) {
    fwrite(STDERR, "Missing graphics dir: {$graphicsDir}\n");
    exit(1);
}

if (!function_exists('imagewebp')) {
    fwrite(STDERR, "GD WebP support not available (imagewebp missing)\n");
    exit(1);
}

$quality = 82;
$files = scanFiles($graphicsDir);

$converted = 0;
$skipped = 0;
$failed = 0;
$failReasons = [];
$failSamples = [];

foreach ($files as $source) {
    $dest = preg_replace('/\.(png|jpe?g|gif)$/i', '.webp', $source);
    if ($dest === null) {
        $skipped++;
        continue;
    }
    if (!shouldConvert($source, $dest)) {
        $skipped++;
        continue;
    }

    $res = convertToWebp($source, $dest, $quality);
    if (!$res['ok']) {
        $failed++;
        $reason = $res['error'] ?? 'unknown';
        $failReasons[$reason] = ($failReasons[$reason] ?? 0) + 1;
        if (count($failSamples) < 8) {
            $failSamples[] = $reason . ' | ' . $source;
        }
        continue;
    }
    $converted++;
}

fwrite(STDOUT, "Converted: {$converted}\nSkipped: {$skipped}\nFailed: {$failed}\n");

if ($failed > 0) {
    fwrite(STDOUT, "Failure reasons:\n");
    foreach ($failReasons as $reason => $count) {
        fwrite(STDOUT, "- {$reason}: {$count}\n");
    }
    fwrite(STDOUT, "Samples:\n");
    foreach ($failSamples as $sample) {
        fwrite(STDOUT, "- {$sample}\n");
    }
}
