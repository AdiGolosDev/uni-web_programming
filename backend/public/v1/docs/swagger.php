<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', '0');

require __DIR__ . '/../../../vendor/autoload.php';

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$path = preg_replace('#public/v1/docs/swagger\.php$#', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
define('API_BASE_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . $path);

$files = array_merge([__DIR__ . '/doc_setup.php'], glob(__DIR__ . '/paths/*.php'));
foreach ($files as $file) {
    require_once $file;
}

$openapi = (new \OpenApi\Generator())->generate($files);

header('Content-Type: application/json');
echo $openapi->toJson();