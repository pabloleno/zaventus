<?php

declare(strict_types=1);

// Confere cada registro e campo anterior contra o clone do backup, sem gravar.
if (PHP_SAPI !== 'cli') exit(1);
$root = dirname(__DIR__);
$backup = $argv[1] ?? '';
if (! preg_match('/^zaventus_restore_[a-z0-9_]+_test$/', $backup)) {
    throw new LogicException('Informe o clone verificado do backup anterior às migrations.');
}
$source = file_get_contents(__DIR__ . '/phase0_baseline.php');
$start = strpos($source, 'function readDotEnv(');
$end = strpos($source, '// Runtime minimo.');
eval(substr($source, $start, $end - $start));
$settings = databaseSettings($root);
if ($backup === $settings['database']) throw new LogicException('Os bancos devem ser diferentes.');
$pdo = new PDO('mysql:host=' . $settings['hostname'] . ';port=' . $settings['port'] . ';dbname=' . $settings['database'] . ';charset=utf8mb4', $settings['username'], $settings['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$quote = static fn (string $value) => '`' . str_replace('`', '``', $value) . '`';
$tables = $pdo->query('SHOW TABLES FROM ' . $quote($backup))->fetchAll(PDO::FETCH_COLUMN);
$checked = $rowsChecked = 0;
$pdo->exec('SET TRANSACTION READ ONLY');
$pdo->beginTransaction();
try {
    foreach ($tables as $table) {
        if ($table === 'migrations') continue;
        $columns = $pdo->query('SHOW COLUMNS FROM ' . $quote($backup) . '.' . $quote($table))->fetchAll(PDO::FETCH_ASSOC);
        $primary = array_column(array_filter($columns, static fn ($c) => $c['Key'] === 'PRI'), 'Field');
        if ($primary === []) throw new LogicException('Tabela sem chave: ' . $table);
        $select = implode(',', array_map($quote, array_column($columns, 'Field')));
        $order = implode(',', array_map($quote, $primary));
        $old = $pdo->query('SELECT ' . $select . ' FROM ' . $quote($backup) . '.' . $quote($table) . ' ORDER BY ' . $order)->fetchAll(PDO::FETCH_ASSOC);
        $now = $pdo->query('SELECT ' . $select . ' FROM ' . $quote($table) . ' ORDER BY ' . $order)->fetchAll(PDO::FETCH_ASSOC);
        if (count($old) !== count($now)) throw new RuntimeException('Contagem divergente em ' . $table);
        foreach ($old as $index => $row) {
            foreach ($columns as $column) {
                $key = $column['Field'];
                $a = $row[$key];
                $b = $now[$index][$key];
                if ($a === $b) continue;
                if ($key === 'deleted_at' && $a === '0000-00-00 00:00:00' && $b === null) continue;
                if ($a !== null && $b !== null && preg_match('/^(?:decimal|int|bigint|tinyint|smallint|mediumint)/', $column['Type']) && is_numeric($a) && is_numeric($b) && bccomp((string) $a, (string) $b, 8) === 0) continue;
                throw new RuntimeException('Campo divergente em ' . $table . '.' . $key . ' (registro ' . $index . ').');
            }
        }
        $checked++;
        $rowsChecked += count($old);
    }
    echo "Dados preservados: $checked tabelas, $rowsChecked registros, todos os campos anteriores conferidos.\n";
} finally {
    $pdo->rollBack();
}
