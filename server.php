<?php

$publicPath = __DIR__.'/public';
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');

// Let PHP's built-in web server return files from public/ directly (Vite CSS,
// JavaScript, manifest, icons, etc.). All other requests go through Laravel.
if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

require_once $publicPath.'/index.php';
