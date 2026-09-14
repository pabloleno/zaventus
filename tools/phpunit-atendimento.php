<?php

declare(strict_types=1);

// Executa a suite da aplicacao em clone exclusivo; nunca muda o grupo principal.
if (PHP_SAPI !== 'cli') {
    exit(1);
}
$root = dirname(__DIR__);
$database = getenv('ZAVENTUS_TEST_DATABASE') ?: '';
if (! preg_match('/^zaventus_restore_[a-z0-9_]+_test$/', $database)) {
    fwrite(STDERR, "Informe ZAVENTUS_TEST_DATABASE=zaventus_restore_*_test.\n");
    exit(1);
}
$source = file_get_contents(__DIR__ . '/phase0_baseline.php');
$start = strpos($source, 'function readDotEnv(');
$end = strpos($source, '// Runtime minimo.');
eval(substr($source, $start, $end - $start));
$settings = databaseSettings($root);
if ($database === $settings['database']) {
    throw new LogicException('O banco de testes deve ser diferente do principal.');
}
$settings['database'] = $database;
foreach (['hostname', 'username', 'password', 'port', 'database'] as $key) {
    $_ENV['database.tests.' . $key] = $_SERVER['database.tests.' . $key] = (string) $settings[$key];
}
require $root . '/vendor/phpunit/phpunit/phpunit';
