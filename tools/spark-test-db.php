<?php

/**
 * Executa comandos Spark apontando o grupo default para um banco descartavel.
 *
 * O MigrationRunner desta versao do CodeIgniter abre a conexao antes de
 * aplicar o filtro informado por `migrate -g`. Por isso, usar apenas o grupo
 * `tests` nao garante que as migrations deixem o banco principal intacto.
 * Este invólucro injeta o nome seguro antes do bootstrap da aplicacao.
 *
 * Uso:
 *   php tools/spark-test-db.php migrate -n App
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este comando so pode ser executado pela linha de comando.\n");
    exit(1);
}

$database = getenv('ZAVENTUS_TEST_DATABASE') ?: 'zaventus_test';

if (
    preg_match('/^[a-zA-Z0-9_]+$/', $database) !== 1
    || preg_match('/(?:_test|_testing)$/i', $database) !== 1
) {
    fwrite(
        STDERR,
        "Abortado: ZAVENTUS_TEST_DATABASE deve terminar em _test ou _testing.\n",
    );
    exit(1);
}

if (($argv[1] ?? '') === '') {
    fwrite(
        STDERR,
        "Uso: php tools/spark-test-db.php <comando-spark> [opcoes]\n",
    );
    exit(1);
}

// $_ENV/$_SERVER precisam ser preenchidos antes do carregamento do .env.
// A configuracao do CI4 prioriza essas chaves ao construir Config\Database.
$overrides = [
    'database' => $database,
    'hostname' => getenv('ZAVENTUS_TEST_HOSTNAME'),
    'username' => getenv('ZAVENTUS_TEST_USERNAME'),
    'password' => getenv('ZAVENTUS_TEST_PASSWORD'),
    'port'     => getenv('ZAVENTUS_TEST_PORT'),
];

if (
    $overrides['port'] !== false
    && filter_var(
        $overrides['port'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 65535]],
    ) === false
) {
    fwrite(STDERR, "Abortado: ZAVENTUS_TEST_PORT deve ser uma porta valida.\n");
    exit(1);
}

foreach ($overrides as $key => $value) {
    if ($value === false) {
        continue;
    }

    $environmentKey = 'database.default.' . $key;
    $_ENV[$environmentKey]    = (string) $value;
    $_SERVER[$environmentKey] = (string) $value;
}

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'spark';
