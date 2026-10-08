<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicDirectory = realpath(__DIR__);
$requestedFile = realpath(__DIR__.$path);

if (
    $path !== '/'
    && $publicDirectory !== false
    && $requestedFile !== false
    && str_starts_with($requestedFile, $publicDirectory.DIRECTORY_SEPARATOR)
    && is_file($requestedFile)
) {
    return false;
}

require __DIR__.'/index.php';
