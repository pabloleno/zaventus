<?php

declare(strict_types=1);

// Backup e conferencia em banco novo. Nunca restaura sobre um banco existente.
if (PHP_SAPI !== 'cli') {
    exit(1);
}

$root = dirname(__DIR__);
$directory = $argv[1] ?? '';
$restoreName = $argv[2] ?? '';
if (! is_dir($directory) || ! preg_match('/^zaventus_restore_[a-z0-9_]+_test$/', $restoreName)) {
    fwrite(STDERR, "Informe diretorio de backup existente e banco exclusivo zaventus_restore_*_test.\n");
    exit(1);
}

// Reaproveita o leitor de configuracao do diagnostico, sem imprimir credenciais.
$source = file_get_contents(__DIR__ . '/phase0_baseline.php');
$start = strpos($source, 'function readDotEnv(');
$end = strpos($source, '// Runtime minimo.');
eval(substr($source, $start, $end - $start));
$settings = databaseSettings($root);
$dsn = 'mysql:host=' . $settings['hostname'] . ';port=' . $settings['port'] . ';dbname=' . $settings['database'] . ';charset=utf8mb4';
$pdo = new PDO($dsn, $settings['username'], $settings['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$existing = $pdo->prepare('SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?');
$existing->execute([$restoreName]);
if ((int) $existing->fetchColumn() !== 0) {
    throw new RuntimeException('O banco de conferencia ja existe. Nenhuma restauracao executada.');
}

function snapshot(PDO $pdo): array
{
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    sort($tables);
    $result = [];
    foreach ($tables as $table) {
        $quoted = '`' . str_replace('`', '``', $table) . '`';
        $rows = $pdo->query('SELECT * FROM ' . $quoted)->fetchAll(PDO::FETCH_ASSOC);
        $encoded = array_map(static fn ($row) => json_encode($row, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE), $rows);
        sort($encoded);
        $result[$table] = ['rows' => count($rows), 'sha256' => hash('sha256', implode("\n", $encoded))];
    }
    return $result;
}

require $root . '/app/ThirdParty/mysqldump/autoload.php';
$dumpPath = $directory . '/zaventus.sql';
$dump = new Ifsnop\Mysqldump\Mysqldump($dsn, $settings['username'], $settings['password'], [
    'add-drop-table' => false, 'single-transaction' => true, 'lock-tables' => false,
    'add-locks' => false, 'default-character-set' => 'utf8mb4',
]);
$dump->start($dumpPath);
$before = snapshot($pdo);
$pdo->exec('CREATE DATABASE `' . $restoreName . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
$restore = new mysqli($settings['hostname'], $settings['username'], $settings['password'], $restoreName, (int) $settings['port']);
$restore->set_charset('utf8mb4');
$restore->multi_query(file_get_contents($dumpPath));
do {
    if ($result = $restore->store_result()) {
        $result->free();
    }
    if (! $restore->more_results()) {
        break;
    }
} while ($restore->next_result());
if ($restore->errno) {
    throw new RuntimeException('Falha na restauracao de conferencia. Codigo: ' . $restore->errno);
}
$clone = new PDO(str_replace('dbname=' . $settings['database'] . ';', 'dbname=' . $restoreName . ';', $dsn), $settings['username'], $settings['password']);
$after = snapshot($clone);
if ($before !== $after) {
    throw new RuntimeException('A conferencia por tabela divergiu; nao aplicar migrations no banco diario.');
}
file_put_contents($directory . '/conferencia.json', json_encode([
    'created_at' => date(DATE_ATOM), 'source_database' => $settings['database'],
    'restore_database' => $restoreName, 'dump_sha256' => hash_file('sha256', $dumpPath),
    'tables' => $before, 'verified' => true,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo 'Backup restaurado e conferido: ' . count($before) . " tabelas; todos os registros identicos.\n";
echo 'Banco isolado: ' . $restoreName . "\n";
