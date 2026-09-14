<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit(1);
$root = dirname(__DIR__);
$database = getenv('ZAVENTUS_TEST_DATABASE') ?: '';
$output = $argv[1] ?? '';
if (! preg_match('/^zaventus_restore_[a-z0-9_]+_test$/', $database) || ! is_dir(dirname($output)) || file_exists($output)) {
    throw new LogicException('Informe um clone exclusivo e um arquivo de resultados novo.');
}
$source = file_get_contents(__DIR__ . '/phase0_baseline.php');
$start = strpos($source, 'function readDotEnv(');
$end = strpos($source, '// Runtime minimo.');
eval(substr($source, $start, $end - $start));
$settings = databaseSettings($root);
if ($database === $settings['database']) throw new LogicException('Nunca usar o banco principal.');
$pdo = new PDO('mysql:host=' . $settings['hostname'] . ';port=' . $settings['port'] . ';dbname=' . $database . ';charset=utf8mb4', $settings['username'], $settings['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
if ($pdo->query('SELECT DATABASE()')->fetchColumn() !== $database) throw new LogicException('Conexão divergente.');
date_default_timezone_set('America/Manaus');
$inserir = static function (string $tabela, string $pk, array $dados) use ($pdo): int {
    $modelo = $pdo->query('SELECT * FROM `' . $tabela . '` LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if (! $modelo) throw new LogicException('Cadastro de referência vazio: ' . $tabela);
    unset($modelo[$pk]);
    $dados = array_replace($modelo, $dados, ['created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
    $sql = 'INSERT INTO `' . $tabela . '` (`' . implode('`,`', array_keys($dados)) . '`) VALUES (' . implode(',', array_fill(0, count($dados), '?')) . ')';
    $pdo->prepare($sql)->execute(array_values($dados));
    return (int) $pdo->lastInsertId();
};
$pdo->beginTransaction();
try {
    $senha = 'Qa-' . bin2hex(random_bytes(12));
    $usuario = 'qa_atendimento_' . date('YmdHis');
    $ids = ['usuario' => $usuario, 'senha' => $senha];
    $ids['login'] = $inserir('login', 'id_login', ['usuario' => $usuario, 'senha' => password_hash($senha, PASSWORD_BCRYPT), 'primeiro_nome' => 'Atendente QA', 'tema' => 0]);
    $ids['cliente'] = $inserir('clientes', 'id_cliente', ['nome' => 'Cliente QA Comunicação Visual', 'tipo' => 1, 'email' => 'cliente@example.test', 'celular' => '92999990000']);
    foreach ([['fachada', 'QA Fachada por m²', '300.00', 'metro_quadrado', 'externa', 'grafica'], ['instalacao', 'QA Instalação', '300.00', 'fixo', 'externa', 'nao_necessita'], ['cortesia', 'QA Adesivo cortesia', '80.00', 'unidade', 'interna', 'nao_necessita'], ['banner', 'QA Banner', '70.00', 'fixo', 'interna', 'cliente']] as [$key, $nome, $valor, $tipo, $execucao, $arte]) {
        $ids[$key] = $inserir('servicos_mao_de_obra', 'id_servico', ['nome' => $nome, 'descricao' => $nome, 'valor' => $valor, 'tipo_preco' => $tipo, 'unidade' => 'un', 'largura_padrao' => null, 'altura_padrao' => null, 'unidade_dimensao' => 'm', 'tipo_execucao' => $execucao, 'arte_padrao' => $arte, 'necessita_instalacao' => $key === 'instalacao' ? 1 : 0, 'ativo' => 1, 'imagem' => null]);
    }
    $ids['material'] = $inserir('produtos', 'id_produto', ['nome' => 'QA Vinil adesivo', 'quantidade' => '50.0000', 'quantidade_minima' => '5.0000', 'unidade' => 'M2', 'ativo' => 1]);
    $pdo->commit();
    file_put_contents($output, json_encode($ids, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode($ids, JSON_UNESCAPED_UNICODE) . "\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $e;
}
