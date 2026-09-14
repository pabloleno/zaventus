#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Baseline nao destrutivo da Fase 0.
 *
 * O script consulta somente metadados e abre a conexao MySQL dentro de uma
 * transacao explicitamente READ ONLY. Credenciais e conteudo de arquivos
 * sensiveis nunca sao incluidos na saida.
 *
 * Uso:
 *   php tools/phase0_baseline.php
 */

const BASELINE_OK = 'OK';
const BASELINE_WARNING = 'AVISO';
const BASELINE_FAILURE = 'FALHA';

$projectRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');

if ($projectRoot === false) {
    fwrite(STDERR, "Nao foi possivel localizar a raiz do projeto.\n");
    exit(1);
}

/** @var list<array{level:string, section:string, message:string, details:list<string>}> $results */
$results = [];

/**
 * @param list<string> $details
 */
function addResult(array &$results, string $level, string $section, string $message, array $details = []): void
{
    $results[] = [
        'level' => $level,
        'section' => $section,
        'message' => $message,
        'details' => array_values($details),
    ];
}

function relativePath(string $path, string $root): string
{
    $normalizedPath = str_replace('\\', '/', $path);
    $normalizedRoot = rtrim(str_replace('\\', '/', $root), '/');

    if (str_starts_with(strtolower($normalizedPath), strtolower($normalizedRoot . '/'))) {
        return substr($normalizedPath, strlen($normalizedRoot) + 1);
    }

    return basename($normalizedPath);
}

/**
 * Le o .env sem exportar ou imprimir qualquer valor.
 *
 * @return array<string, string>
 */
function readDotEnv(string $path): array
{
    if (! is_file($path) || ! is_readable($path)) {
        return [];
    }

    $values = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES);

    if ($lines === false) {
        return [];
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || str_starts_with($trimmed, '#') || ! str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim(preg_replace('/^export\s+/', '', trim($name)) ?? '');
        $value = trim($value);

        if ($name === '') {
            continue;
        }

        if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
            $quote = $value[0];
            $end = strrpos($value, $quote);
            $value = $end !== false && $end > 0 ? substr($value, 1, $end - 1) : substr($value, 1);
            $value = str_replace(['\\' . $quote, '\\\\'], [$quote, '\\'], $value);
        } else {
            $value = preg_split('/\s+#/', $value, 2)[0] ?? '';
            $value = trim($value);
        }

        $values[$name] = $value;
    }

    for ($pass = 0; $pass < 2; $pass++) {
        foreach ($values as $name => $value) {
            $values[$name] = preg_replace_callback(
                '/\$\{([a-zA-Z0-9_.]+)\}/',
                static function (array $match) use ($values): string {
                    if (array_key_exists($match[1], $values)) {
                        return $values[$match[1]];
                    }

                    $environmentValue = getenv($match[1]);

                    return $environmentValue === false ? $match[0] : $environmentValue;
                },
                $value,
            ) ?? $value;
        }
    }

    return $values;
}

/**
 * Extrai apenas os defaults escalares do grupo default de Database.php.
 * Os valores permanecem exclusivamente em memoria.
 *
 * @return array<string, string|int>
 */
function readDatabaseDefaults(string $path): array
{
    $defaults = [
        'hostname' => 'localhost',
        'username' => '',
        'password' => '',
        'database' => '',
        'DBDriver' => 'MySQLi',
        'charset' => 'utf8',
        'port' => 3306,
    ];

    $source = is_file($path) ? file_get_contents($path) : false;

    if ($source === false) {
        return $defaults;
    }

    $defaultGroupEnd = strpos($source, 'public $tests');
    $defaultGroupSource = $defaultGroupEnd === false ? $source : substr($source, 0, $defaultGroupEnd);

    foreach (['hostname', 'username', 'password', 'database', 'DBDriver', 'charset'] as $key) {
        $pattern = "/'" . preg_quote($key, '/') . "'\\s*=>\\s*'((?:\\\\.|[^'])*)'/";

        if (preg_match($pattern, $defaultGroupSource, $match) === 1) {
            $defaults[$key] = stripcslashes($match[1]);
        }
    }

    if (preg_match("/'port'\\s*=>\\s*(\\d+)/", $defaultGroupSource, $match) === 1) {
        $defaults['port'] = (int) $match[1];
    }

    return $defaults;
}

/**
 * @return array<string, string|int>
 */
function databaseSettings(string $root): array
{
    $settings = readDatabaseDefaults($root . '/app/Config/Database.php');
    $dotenv = readDotEnv($root . '/.env');

    foreach (array_keys($settings) as $key) {
        $environmentName = 'database.default.' . $key;
        $processValue = getenv($environmentName);

        if ($processValue !== false) {
            $settings[$key] = $processValue;
        } elseif (array_key_exists($environmentName, $dotenv)) {
            $settings[$key] = $dotenv[$environmentName];
        }
    }

    $settings['port'] = max(1, (int) $settings['port']);

    return $settings;
}

function canExecuteProcesses(): bool
{
    if (! function_exists('exec')) {
        return false;
    }

    $disabled = array_filter(array_map('trim', explode(',', (string) ini_get('disable_functions'))));

    return ! in_array('exec', $disabled, true);
}

/**
 * @param list<string> $files
 * @return list<string>
 */
function lintPhpFiles(array $files): array
{
    if (! canExecuteProcesses()) {
        return [];
    }

    $invalid = [];

    foreach ($files as $file) {
        $output = [];
        $exitCode = 0;
        $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file) . ' 2>&1';
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $invalid[] = $file;
        }
    }

    return $invalid;
}

function withoutPhpComments(string $source): string
{
    $clean = '';

    foreach (token_get_all($source) as $token) {
        if (is_array($token)) {
            if (in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            $clean .= $token[1];
            continue;
        }

        $clean .= $token;
    }

    return $clean;
}

/**
 * @return array{available:bool, files:list<string>}
 */
function trackedFiles(string $root): array
{
    if (! canExecuteProcesses()) {
        return ['available' => false, 'files' => []];
    }

    $output = [];
    $exitCode = 0;
    $nullDevice = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
    $command = 'git -C ' . escapeshellarg($root) . ' ls-files 2>' . $nullDevice;
    exec($command, $output, $exitCode);

    if ($exitCode !== 0) {
        return ['available' => false, 'files' => []];
    }

    return [
        'available' => true,
        'files' => array_values(array_filter(array_map(
            static fn (string $path): string => str_replace('\\', '/', trim($path)),
            $output,
        ))),
    ];
}

function isSensitiveArtifact(string $path): bool
{
    $normalized = strtolower(str_replace('\\', '/', $path));

    if (preg_match('#(^|/)\.env$#', $normalized) === 1) {
        return true;
    }

    return preg_match('#\.(sql|dump|bak|sqlite|sqlite3|db|pfx|p12|pem|key|jks|keystore)$#', $normalized) === 1;
}

/**
 * @return list<string>
 */
function sensitiveFilesBelow(string $directory, string $root): array
{
    if (! is_dir($directory)) {
        return [];
    }

    $matches = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && isSensitiveArtifact($file->getPathname())) {
            $matches[] = relativePath($file->getPathname(), $root);
        }
    }

    sort($matches);

    return $matches;
}

/**
 * @return mysqli|null
 */
function connectReadOnly(array $settings, array &$results): ?mysqli
{
    if (! extension_loaded('mysqli')) {
        addResult($results, BASELINE_FAILURE, 'Banco', 'A extensao mysqli nao esta disponivel.');

        return null;
    }

    if (strcasecmp((string) $settings['DBDriver'], 'MySQLi') !== 0) {
        addResult($results, BASELINE_FAILURE, 'Banco', 'O baseline suporta apenas o driver MySQLi configurado pelo projeto.');

        return null;
    }

    if (trim((string) $settings['database']) === '') {
        addResult($results, BASELINE_FAILURE, 'Banco', 'A configuracao do banco esta incompleta; revise o .env.');

        return null;
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $connection = mysqli_init();

    if ($connection === false) {
        addResult($results, BASELINE_FAILURE, 'Banco', 'Nao foi possivel inicializar o cliente MySQL.');

        return null;
    }

    $connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $connected = @$connection->real_connect(
        (string) $settings['hostname'],
        (string) $settings['username'],
        (string) $settings['password'],
        (string) $settings['database'],
        (int) $settings['port'],
    );

    if (! $connected) {
        $connection->close();
        addResult($results, BASELINE_FAILURE, 'Banco', 'Falha ao conectar; confirme o MySQL e as credenciais no .env.');

        return null;
    }

    $configuredCharset = trim((string) $settings['charset']);

    if ($configuredCharset !== '' && ! @$connection->set_charset($configuredCharset)) {
        $connection->close();
        addResult($results, BASELINE_FAILURE, 'Banco', 'A conexao recusou o charset configurado.');

        return null;
    }

    if (! $connection->query('SET SESSION TRANSACTION READ ONLY') || ! $connection->query('START TRANSACTION READ ONLY')) {
        $connection->close();
        addResult($results, BASELINE_FAILURE, 'Banco', 'O servidor nao permitiu iniciar a verificacao em modo READ ONLY.');

        return null;
    }

    addResult($results, BASELINE_OK, 'Banco', 'Conexao estabelecida em transacao READ ONLY.');

    return $connection;
}

/**
 * @return array<string, string>|null
 */
function fetchOne(mysqli $connection, string $sql): ?array
{
    $query = $connection->query($sql);

    if (! $query instanceof mysqli_result) {
        return null;
    }

    $row = $query->fetch_assoc();
    $query->free();

    return is_array($row) ? array_map(static fn ($value): string => (string) $value, $row) : null;
}

/**
 * @return list<array<string, string>>|null
 */
function fetchAll(mysqli $connection, string $sql): ?array
{
    $query = $connection->query($sql);

    if (! $query instanceof mysqli_result) {
        return null;
    }

    $rows = [];

    while ($row = $query->fetch_assoc()) {
        $rows[] = array_map(static fn ($value): string => (string) $value, $row);
    }

    $query->free();

    return $rows;
}

// Runtime minimo.
addResult(
    $results,
    version_compare(PHP_VERSION, '8.2.0', '>=') ? BASELINE_OK : BASELINE_FAILURE,
    'Runtime',
    'PHP ' . PHP_VERSION . (version_compare(PHP_VERSION, '8.2.0', '>=') ? ' atende ao minimo 8.2.' : ' esta abaixo do minimo 8.2.'),
);

foreach (['mysqli', 'intl', 'mbstring'] as $extension) {
    addResult(
        $results,
        extension_loaded($extension) ? BASELINE_OK : BASELINE_FAILURE,
        'Runtime',
        'Extensao ' . $extension . (extension_loaded($extension) ? ' disponivel.' : ' ausente.'),
    );
}

// Migrations no filesystem e sintaxe PHP.
$migrationDirectory = $projectRoot . '/app/Database/Migrations';
$migrationFiles = glob($migrationDirectory . '/*.php') ?: [];
sort($migrationFiles);
$migrationVersions = [];
$invalidMigrationNames = [];
$duplicateMigrationVersions = [];

foreach ($migrationFiles as $file) {
    $basename = basename($file);

    if (preg_match('/^(\d{4}-\d{2}-\d{2}-\d{6})_(.+)\.php$/', $basename, $match) !== 1) {
        $invalidMigrationNames[] = relativePath($file, $projectRoot);
        continue;
    }

    if (isset($migrationVersions[$match[1]])) {
        $duplicateMigrationVersions[] = $match[1];
    }

    $migrationVersions[$match[1]] = $basename;
}

addResult(
    $results,
    $migrationFiles === [] ? BASELINE_FAILURE : BASELINE_OK,
    'Migrations',
    $migrationFiles === [] ? 'Nenhuma migration foi encontrada.' : count($migrationFiles) . ' arquivos de migration encontrados.',
);

if ($invalidMigrationNames !== []) {
    addResult($results, BASELINE_FAILURE, 'Migrations', 'Ha migrations fora do padrao de timestamp.', $invalidMigrationNames);
}

if ($duplicateMigrationVersions !== []) {
    addResult($results, BASELINE_FAILURE, 'Migrations', 'Ha timestamps de migration duplicados.', array_values(array_unique($duplicateMigrationVersions)));
}

if (canExecuteProcesses()) {
    $invalidPhp = lintPhpFiles($migrationFiles);
    addResult(
        $results,
        $invalidPhp === [] ? BASELINE_OK : BASELINE_FAILURE,
        'Migrations',
        $invalidPhp === [] ? 'Todas as migrations passaram no php -l.' : 'Ha migrations com erro de sintaxe.',
        array_map(static fn (string $file): string => relativePath($file, $projectRoot), $invalidPhp),
    );
} else {
    addResult($results, BASELINE_WARNING, 'Migrations', 'Nao foi possivel executar php -l porque exec() esta indisponivel.');
}

// Roteamento automatico e quantidade de rotas explicitas.
$routesPath = $projectRoot . '/app/Config/Routes.php';
$routesSource = is_file($routesPath) ? file_get_contents($routesPath) : false;

if ($routesSource === false) {
    addResult($results, BASELINE_FAILURE, 'Rotas', 'app/Config/Routes.php nao foi encontrado ou nao pode ser lido.');
} else {
    $routeFiles = array_merge(
        [$routesPath],
        glob($projectRoot . '/app/Config/Routes/*.php') ?: [],
    );
    $routeSources = [];

    foreach (array_unique($routeFiles) as $routeFile) {
        $source = file_get_contents($routeFile);

        if ($source !== false) {
            $routeSources[] = withoutPhpComments($source);
        }
    }

    $routesCode = implode("\n", $routeSources);
    $autoRouteEnabled = preg_match('/->\s*setAutoRoute\s*\(\s*true\s*\)/i', $routesCode) === 1;
    preg_match_all('/\$routes\s*->\s*(?:get|post|put|patch|delete|match|add)\s*\(/i', $routesCode, $explicitRoutes);

    addResult(
        $results,
        $autoRouteEnabled ? BASELINE_WARNING : BASELINE_OK,
        'Rotas',
        $autoRouteEnabled
            ? 'Auto-routing esta habilitado; mapear rotas explicitas antes de desativa-lo.'
            : 'Auto-routing esta desabilitado.',
    );
    addResult($results, BASELINE_OK, 'Rotas', count($explicitRoutes[0]) . ' declaracoes de rotas explicitas encontradas.');
}

// Arquivos potencialmente sensiveis: somente nomes e estado no Git.
$tracked = trackedFiles($projectRoot);

if (! $tracked['available']) {
    addResult($results, BASELINE_WARNING, 'Arquivos sensiveis', 'Git indisponivel; nao foi possivel validar arquivos versionados.');
} else {
    $trackedEnvironment = array_values(array_filter(
        $tracked['files'],
        static fn (string $path): bool => preg_match('#(^|/)\.env$#i', $path) === 1,
    ));
    $trackedSensitive = array_values(array_filter($tracked['files'], 'isSensitiveArtifact'));

    addResult(
        $results,
        $trackedEnvironment === [] ? BASELINE_OK : BASELINE_FAILURE,
        'Arquivos sensiveis',
        $trackedEnvironment === [] ? 'Nenhum arquivo .env esta versionado.' : 'Arquivo .env versionado; remova-o do indice sem apagar a copia local.',
        $trackedEnvironment,
    );

    $trackedSensitiveExceptEnv = array_values(array_diff($trackedSensitive, $trackedEnvironment));
    addResult(
        $results,
        $trackedSensitiveExceptEnv === [] ? BASELINE_OK : BASELINE_WARNING,
        'Arquivos sensiveis',
        $trackedSensitiveExceptEnv === []
            ? 'Nenhum dump, banco local ou chave privada potencial esta versionado.'
            : 'Ha artefatos potencialmente sensiveis versionados; revisar sem abrir seu conteudo.',
        $trackedSensitiveExceptEnv,
    );
}

$publicSensitive = sensitiveFilesBelow($projectRoot . '/public', $projectRoot);
addResult(
    $results,
    $publicSensitive === [] ? BASELINE_OK : BASELINE_FAILURE,
    'Arquivos sensiveis',
    $publicSensitive === []
        ? 'Nenhum artefato sensivel conhecido foi encontrado dentro de public/.'
        : 'Ha artefatos sensiveis dentro de public/ e acessiveis pelo Apache.',
    $publicSensitive,
);

// Metadados do banco. Nenhuma tabela de negocio e consultada.
$settings = databaseSettings($projectRoot);
$connection = connectReadOnly($settings, $results);

if ($connection instanceof mysqli) {
    $server = fetchOne(
        $connection,
        'SELECT '
        . '@@character_set_database AS database_charset, '
        . '@@collation_database AS database_collation, '
        . '@@character_set_connection AS connection_charset, '
        . '@@collation_connection AS connection_collation, '
        . '@@sql_mode AS sql_mode, '
        . 'VERSION() AS server_version',
    );

    if ($server === null) {
        addResult($results, BASELINE_FAILURE, 'Banco', 'Nao foi possivel consultar charset e sql_mode.');
    } else {
        $databaseUtf8mb4 = strtolower($server['database_charset']) === 'utf8mb4';
        $connectionUtf8mb4 = strtolower($server['connection_charset']) === 'utf8mb4';
        $sqlModes = array_values(array_filter(array_map('trim', explode(',', strtoupper($server['sql_mode'])))));
        $strictMode = in_array('STRICT_TRANS_TABLES', $sqlModes, true) || in_array('STRICT_ALL_TABLES', $sqlModes, true);

        addResult($results, BASELINE_OK, 'Banco', 'Servidor MySQL/MariaDB identificado: ' . $server['server_version'] . '.');
        addResult(
            $results,
            $databaseUtf8mb4 ? BASELINE_OK : BASELINE_WARNING,
            'Charset',
            'Banco: ' . $server['database_charset'] . ' / ' . $server['database_collation']
            . ($databaseUtf8mb4 ? '.' : '; planejar conversao para utf8mb4.'),
        );
        addResult(
            $results,
            $connectionUtf8mb4 ? BASELINE_OK : BASELINE_WARNING,
            'Charset',
            'Conexao: ' . $server['connection_charset'] . ' / ' . $server['connection_collation']
            . ($connectionUtf8mb4 ? '.' : '; configurar utf8mb4 no CodeIgniter.'),
        );
        addResult(
            $results,
            $strictMode ? BASELINE_OK : BASELINE_WARNING,
            'SQL mode',
            $strictMode
                ? 'Modo SQL estrito esta ativo.'
                : 'Modo SQL estrito nao esta ativo; dados invalidos podem ser truncados silenciosamente.',
            ['Modos ativos: ' . ($sqlModes === [] ? '(nenhum)' : implode(', ', $sqlModes))],
        );
    }

    $criticalTables = [
        'migrations',
        'login',
        'clientes',
        'fornecedores',
        'categorias_dos_produtos',
        'produtos',
        'servicos_mao_de_obra',
        'orcamentos',
        'produtos_do_orcamento',
        'vendas',
        'produtos_da_venda',
        'ordens_de_servicos',
        'servicos_mao_de_obra_da_os',
        'contas_a_receber',
        'contas_a_pagar',
        'formas_de_pagamento',
        'caixas',
        'reposicoes',
        'saida_de_mercadorias',
    ];
    $tablesByName = [];
    $tableMetadata = fetchAll(
        $connection,
        'SELECT TABLE_NAME, ENGINE, TABLE_COLLATION '
        . 'FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()',
    );

    if ($tableMetadata === null) {
        addResult($results, BASELINE_FAILURE, 'Tabelas', 'Nao foi possivel consultar o catalogo de tabelas.');
    } else {
        foreach ($tableMetadata as $table) {
            $tablesByName[$table['TABLE_NAME']] = $table;
        }

        $missingTables = array_values(array_diff($criticalTables, array_keys($tablesByName)));
        $nonTransactional = [];
        $legacyCollations = [];

        foreach ($criticalTables as $tableName) {
            if (! isset($tablesByName[$tableName])) {
                continue;
            }

            $metadata = $tablesByName[$tableName];

            if (strcasecmp($metadata['ENGINE'], 'InnoDB') !== 0) {
                $nonTransactional[] = $tableName . ' (' . $metadata['ENGINE'] . ')';
            }

            if (! str_starts_with(strtolower($metadata['TABLE_COLLATION']), 'utf8mb4_')) {
                $legacyCollations[] = $tableName . ' (' . $metadata['TABLE_COLLATION'] . ')';
            }
        }

        addResult(
            $results,
            $missingTables === [] ? BASELINE_OK : BASELINE_FAILURE,
            'Tabelas',
            $missingTables === []
                ? count($criticalTables) . ' tabelas criticas estao presentes.'
                : 'Faltam tabelas criticas.',
            $missingTables,
        );
        addResult(
            $results,
            $nonTransactional === [] ? BASELINE_OK : BASELINE_FAILURE,
            'Tabelas',
            $nonTransactional === [] ? 'Todas as tabelas criticas usam InnoDB.' : 'Ha tabelas criticas sem engine transacional.',
            $nonTransactional,
        );
        addResult(
            $results,
            $legacyCollations === [] ? BASELINE_OK : BASELINE_WARNING,
            'Charset',
            $legacyCollations === []
                ? 'Todas as tabelas criticas usam collation utf8mb4.'
                : 'Tabelas criticas ainda usam collation anterior a utf8mb4.',
            $legacyCollations,
        );
    }

    if (! isset($tablesByName['migrations'])) {
        addResult($results, BASELINE_FAILURE, 'Migrations', 'A tabela de controle migrations nao existe.');
    } else {
        $appliedRows = fetchAll(
            $connection,
            "SELECT version FROM migrations WHERE namespace = 'App' ORDER BY version",
        );

        if ($appliedRows === null) {
            addResult($results, BASELINE_FAILURE, 'Migrations', 'Nao foi possivel ler a tabela de controle migrations.');
        } else {
            $appliedVersions = array_values(array_unique(array_column($appliedRows, 'version')));
            $sourceVersions = array_keys($migrationVersions);
            $pendingVersions = array_values(array_diff($sourceVersions, $appliedVersions));
            $missingSources = array_values(array_diff($appliedVersions, $sourceVersions));
            $pendingFiles = array_map(
                static fn (string $version): string => $migrationVersions[$version] ?? $version,
                $pendingVersions,
            );

            addResult(
                $results,
                $pendingVersions === [] ? BASELINE_OK : BASELINE_WARNING,
                'Migrations',
                $pendingVersions === []
                    ? count($appliedVersions) . ' migrations App estao aplicadas e nao ha pendencias.'
                    : count($pendingVersions) . ' migration(s) ainda nao aplicada(s).',
                $pendingFiles,
            );
            addResult(
                $results,
                $missingSources === [] ? BASELINE_OK : BASELINE_FAILURE,
                'Migrations',
                $missingSources === []
                    ? 'Toda migration aplicada possui arquivo-fonte correspondente.'
                    : 'Ha migrations aplicadas cujo arquivo-fonte nao esta no projeto.',
                $missingSources,
            );
        }
    }

    @$connection->query('ROLLBACK');
    $connection->close();
}

echo "Zaventus - baseline da Fase 0 (somente leitura)\n";
echo str_repeat('=', 54) . "\n";

foreach ($results as $result) {
    echo '[' . $result['level'] . '] ' . $result['section'] . ': ' . $result['message'] . "\n";

    foreach (array_slice($result['details'], 0, 25) as $detail) {
        echo '  - ' . $detail . "\n";
    }

    if (count($result['details']) > 25) {
        echo '  - ... e mais ' . (count($result['details']) - 25) . " item(ns).\n";
    }
}

$totals = [BASELINE_OK => 0, BASELINE_WARNING => 0, BASELINE_FAILURE => 0];

foreach ($results as $result) {
    $totals[$result['level']]++;
}

echo str_repeat('-', 54) . "\n";
echo sprintf(
    "Resumo: %d OK, %d aviso(s), %d falha(s).\n",
    $totals[BASELINE_OK],
    $totals[BASELINE_WARNING],
    $totals[BASELINE_FAILURE],
);

exit($totals[BASELINE_FAILURE] > 0 ? 1 : 0);
