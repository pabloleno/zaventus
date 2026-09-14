<?php

// Servidor de homologacao exclusivamente local, com banco e sessao separados.
if (PHP_SAPI !== 'cli-server') {
    exit(1);
}
$database = getenv('ZAVENTUS_TEST_DATABASE') ?: '';
if (! preg_match('/^zaventus_restore_[a-z0-9_]+_test$/', $database)) {
    http_response_code(503);
    exit('Banco de homologacao nao configurado.');
}
$root = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = realpath($root . '/public' . $path);
if ($file && is_file($file) && str_starts_with(str_replace('\\', '/', $file), str_replace('\\', '/', $root) . '/public/') && strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') {
    return false;
}
$settings = [
    'database.default.database' => $database,
    'app.baseURL' => 'http://127.0.0.1:8097/',
    'app.forceGlobalSecureRequests' => 'false',
    'cookie.secure' => 'false',
    'session.cookieName' => 'zaventus_qa',
    'session.savePath' => $root . '/writable/session-qa',
];
if (! is_dir($settings['session.savePath'])) {
    mkdir($settings['session.savePath'], 0700, true);
}
foreach ($settings as $key => $value) {
    $_ENV[$key] = $_SERVER[$key] = $value;
}
require $root . '/public/index.php';
