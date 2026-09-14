<?php

namespace App\Tests\Libraries;

use App\Libraries\AnexoAtendimento;
use CodeIgniter\Database\MySQLi\Builder;
use CodeIgniter\Database\MySQLi\Connection;
use CodeIgniter\Database\MySQLi\Result;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Test\CIUnitTestCase;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Throwable;

/**
 * @group atendimento-database
 *
 * Upload precisa confirmar a própria transação. Estas fixtures são confirmadas
 * somente no clone autorizado e removidas por seus IDs, junto aos arquivos exatos.
 */
final class AnexoAtendimentoTest extends CIUnitTestCase
{
    private ?AnexoTestConnection $anexoDb = null;
    private AnexoAtendimento $anexos;
    private array $fixtures = [];
    private array $ordens = [];
    private array $uploads = [];
    private string $staging = '';
    private int $usuario;
    private int $cliente;
    private int $vendedor;
    private int $ordem;
    private int $item;

    protected function setUp(): void
    {
        parent::setUp();
        $config = config('Database');
        $nome = (string) ($config->tests['database'] ?? '');
        if (ENVIRONMENT !== 'testing' || ! preg_match('/\Azaventus_restore_[a-z0-9_]+_test\z/', $nome)
            || $nome === ($config->default['database'] ?? '')) {
            throw new LogicException('Anexos: configure o clone exclusivo zaventus_restore_*_test, diferente do principal.');
        }
        $this->anexoDb = new AnexoTestConnection($config->tests);
        $efetivo = $this->anexoDb->query('SELECT DATABASE() AS nome')->getRowArray()['nome'] ?? '';
        if ($efetivo !== $nome) {
            $this->anexoDb->close();
            $this->anexoDb = null;
            throw new LogicException('Anexos: a conexão efetiva não corresponde ao clone autorizado.');
        }
        self::assertTrue($this->anexoDb->tableExists('anexos_os'), 'Aplique as migrations no clone antes dos testes.');
        $this->staging = WRITEPATH . 'cache/anexo-test-' . bin2hex(random_bytes(12));
        self::assertTrue(mkdir($this->staging, 0700));
        $suffix = bin2hex(random_bytes(8));
        $this->usuario = $this->fixture('login', ['usuario' => 'anexo_test_' . $suffix, 'primeiro_nome' => 'Anexo Teste']);
        $this->cliente = $this->fixture('clientes', ['nome' => 'Cliente Anexo ' . $suffix]);
        $this->vendedor = $this->fixture('vendedores', ['nome' => 'Vendedor Anexo ' . $suffix, 'status' => 'Ativo']);
        $this->ordem = $this->novaOrdem();
        $this->item = $this->novoItem($this->ordem);
        $this->anexos = new AnexoAtendimento($this->anexoDb);
    }

    protected function tearDown(): void
    {
        try {
            if ($this->anexoDb !== null) {
                $this->anexoDb->falharQuery = null;
                $this->anexoDb->falharCommit = false;
                $this->anexoDb->falharInicio = false;
                while ($this->anexoDb->transDepth > 0) {
                    self::assertTrue($this->anexoDb->transRollback());
                }
                $this->anexoDb->resetTransStatus();
                foreach ($this->ordens as $id) {
                    self::assertTrue($this->anexoDb->table('anexos_os')->where('id_ordem', $id)->delete());
                    self::assertTrue($this->anexoDb->table('ordens_de_servicos_historico')->where('id_ordem', $id)->delete());
                }
                foreach (array_reverse($this->fixtures) as [$tabela, $pk, $id]) {
                    self::assertTrue($this->anexoDb->table($tabela)->where($pk, $id)->delete());
                }
                $this->anexoDb->close();
            }
        } finally {
            foreach ($this->uploads as $upload) {
                if ($upload->destino !== null) {
                    $relative = str_replace('\\', '/', substr($upload->destino, strlen(rtrim(WRITEPATH, '/\\')) + 1));
                    AnexoAtendimento::removerArquivo($relative);
                    $directory = dirname($upload->destino);
                    if (is_dir($directory) && count(scandir($directory)) === 2) {
                        rmdir($directory);
                    }
                }
                if (is_file($upload->origem)) {
                    unlink($upload->origem);
                }
            }
            if ($this->staging !== '' && is_dir($this->staging) && count(scandir($this->staging)) === 2) {
                rmdir($this->staging);
            }
            parent::tearDown();
        }
    }

    public function testPngPrivadoPreservaItemAutoriaETamanhoRealComNomeSeguro(): void
    {
        $file = $this->upload('..\\..\\ar' . "\r\n" . 'te.PNG', $this->png(), 'application/pdf', 1);
        $id = $this->anexos->salvar($this->ordem, $file, [
            'categoria' => 'arte', 'id_servico_os' => (string) $this->item,
            'created_by' => 2147483647, 'arquivo' => '../../public/malicioso.php',
        ], $this->usuario);
        $row = $this->anexos->obter($id);
        self::assertSame($this->ordem, (int) $row['id_ordem']);
        self::assertSame($this->item, (int) $row['id_servico_os']);
        self::assertSame($this->usuario, (int) $row['created_by']);
        self::assertSame('arte', $row['categoria']);
        self::assertSame('arte.PNG', $row['nome']);
        self::assertSame('image/png', $row['mime']);
        self::assertSame(strlen($this->png()), (int) $row['tamanho']);
        self::assertMatchesRegularExpression('#\Auploads/atendimentos/' . $this->ordem . '/[a-f0-9]{48}\.png\z#', $row['arquivo']);
        self::assertSame($this->png(), file_get_contents(WRITEPATH . $row['arquivo']));
        self::assertFalse(is_file(FCPATH . $row['arquivo']));
        self::assertFalse(is_file($file->origem));
        self::assertSame(0, $this->anexoDb->transDepth);
        $historico = $this->anexoDb->table('ordens_de_servicos_historico')->where('id_ordem', $this->ordem)->get()->getResultArray();
        self::assertCount(1, $historico);
        self::assertSame('anexado', $historico[0]['evento']);
        self::assertSame($this->usuario, (int) $historico[0]['id_login']);
        self::assertSame('aguardando_aprovacao', $historico[0]['status_anterior']);
        self::assertSame('aguardando_aprovacao', $historico[0]['status_novo']);
        self::assertSame('Arte: arte.PNG', $historico[0]['observacoes']);
    }

    public function testPdfAceitoSemItemEDoisEnviosNaoSobrescrevemArquivo(): void
    {
        $pdf = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF\n";
        $primeiro = $this->anexos->salvar($this->ordem, $this->upload('projeto.pdf', $pdf, 'text/plain'), [], $this->usuario);
        $segundo = $this->anexos->salvar($this->ordem, $this->upload('projeto.pdf', $pdf), ['id_servico_os' => '0'], $this->usuario);
        $a = $this->anexos->obter($primeiro);
        $b = $this->anexos->obter($segundo);
        self::assertNull($a['id_servico_os']);
        self::assertSame('cliente', $a['categoria']);
        self::assertSame('application/pdf', $a['mime']);
        self::assertNotSame($a['arquivo'], $b['arquivo']);
        self::assertSame($pdf, file_get_contents(WRITEPATH . $a['arquivo']));
        self::assertSame($pdf, file_get_contents(WRITEPATH . $b['arquivo']));
    }

    public function testRecusaExtensaoExecutavelConteudoFalsoEImagemCorrompida(): void
    {
        foreach ([
            ['arte.php', $this->png(), 'image/png'],
            ['arte.pdf', '<?php echo "arquivo";', 'application/pdf'],
            ['arte.png', "\x89PNG\r\n\x1a\n" . str_repeat('a', 80), 'image/png'],
        ] as [$nome, $bytes, $mime]) {
            $file = $this->upload($nome, $bytes, $mime);
            $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], $this->usuario));
            self::assertNull($file->destino);
        }
        $this->semRegistros();
    }

    public function testRecusaArquivoVazioGrandeAusenteOuComErroDeUpload(): void
    {
        $vazio = $this->upload('vazio.png', '');
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $vazio, [], $this->usuario));
        $grande = $this->upload('grande.png', $this->png(), 'image/png', 1);
        $handle = fopen($grande->origem, 'r+b');
        try {
            self::assertTrue(ftruncate($handle, 20 * 1024 * 1024 + 1));
        } finally {
            fclose($handle);
        }
        clearstatcache(true, $grande->origem);
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $grande, [], $this->usuario));
        $this->falha(fn () => $this->anexos->salvar($this->ordem, null, [], $this->usuario));
        $erro = $this->upload('incompleto.png', $this->png(), 'image/png', null, UPLOAD_ERR_PARTIAL);
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $erro, [], $this->usuario));
        self::assertNull($grande->destino);
        $this->semRegistros();
    }

    public function testRecusaCategoriaInvalidaEIdentificadorAmbiguo(): void
    {
        foreach ([['categoria' => 'qualquer'], ['categoria' => ['arte']], ['id_servico_os' => $this->item . 'abc'], ['id_servico_os' => -1]] as $dados) {
            $file = $this->upload('arte.png', $this->png());
            $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, $dados, $this->usuario));
            self::assertNull($file->destino);
        }
        $this->semRegistros();
    }

    public function testRecusaServicoDeOutraOrdemRemovidoOuExcluido(): void
    {
        $outroItem = $this->novoItem($this->novaOrdem());
        $removido = $this->novoItem($this->ordem, ['removido_at' => date('Y-m-d H:i:s')]);
        $excluido = $this->novoItem($this->ordem, ['deleted_at' => date('Y-m-d H:i:s')]);
        foreach ([$outroItem, $removido, $excluido] as $item) {
            $file = $this->upload('arte.png', $this->png());
            $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, ['id_servico_os' => $item], $this->usuario));
            self::assertNull($file->destino);
            self::assertSame(0, $this->anexoDb->transDepth);
        }
        $this->semRegistros();
    }

    public function testRecusaUsuarioInexistenteOrdemCanceladaExcluidaEInexistente(): void
    {
        $file = $this->upload('arte.png', $this->png());
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], 0));
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], 2147483647));
        $cancelada = $this->novaOrdem(['status_operacional' => 'cancelado', 'situacao' => 'Cancelada']);
        $excluida = $this->novaOrdem(['deleted_at' => date('Y-m-d H:i:s')]);
        foreach ([$cancelada, $excluida, 2147483647] as $id) {
            $this->falha(fn () => $this->anexos->salvar($id, $file, [], $this->usuario));
        }
        self::assertNull($file->destino);
        $this->semRegistros();
    }

    public function testTransacaoExternaRecusadaAntesDeMoverArquivo(): void
    {
        self::assertTrue($this->anexoDb->transBegin());
        $file = $this->upload('arte.png', $this->png());
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], $this->usuario), RuntimeException::class, 'operação própria');
        self::assertSame(1, $this->anexoDb->transDepth, 'A biblioteca não deve encerrar a transação do chamador.');
        self::assertNull($file->destino);
        self::assertTrue($this->anexoDb->transRollback());
        $this->semRegistros();
    }

    public function testFalhaAoIniciarTransacaoNaoMoveArquivo(): void
    {
        $this->anexoDb->falharInicio = true;
        $file = $this->upload('arte.png', $this->png());
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], $this->usuario), RuntimeException::class, 'iniciar');
        self::assertNull($file->destino);
        self::assertSame(0, $this->anexoDb->transDepth);
        $this->semRegistros();
    }

    public function testFalhaDeConsultaNaoMoveArquivo(): void
    {
        $this->anexoDb->falharQuery = 'FOR UPDATE';
        $file = $this->upload('arte.png', $this->png());
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], $this->usuario), RuntimeException::class, 'consultar');
        self::assertNull($file->destino);
        self::assertSame(0, $this->anexoDb->transDepth);
        $this->semRegistros();
    }

    public function testFalhasNosInsertsDesfazemMetadadosEArquivo(): void
    {
        foreach (['INSERT INTO `anexos_os`', 'INSERT INTO `ordens_de_servicos_historico`'] as $sql) {
            $this->anexoDb->falharQuery = $sql;
            $file = $this->upload('arte.png', $this->png());
            $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], $this->usuario), RuntimeException::class, 'registrar');
            self::assertNotNull($file->destino, 'A falha ocorre após a gravação do arquivo.');
            self::assertFileDoesNotExist($file->destino);
            self::assertSame(0, $this->anexoDb->transDepth);
            $this->semRegistros();
        }
    }

    public function testCommitFalsoDesfazMetadadosHistoricoEArquivo(): void
    {
        $this->anexoDb->falharCommit = true;
        $file = $this->upload('arte.png', $this->png());
        $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], $this->usuario), RuntimeException::class, 'concluir');
        self::assertNotNull($file->destino);
        self::assertFileDoesNotExist($file->destino);
        self::assertSame(0, $this->anexoDb->transDepth);
        $this->semRegistros();
    }

    public function testFalhaDeMovimentoCompensaMesmoSeArquivoFoiCriado(): void
    {
        foreach ([false, true] as $gravarAntesDaFalha) {
            $file = $this->upload('arte.png', $this->png());
            $file->falharMovimento = true;
            $file->gravarAntesDaFalha = $gravarAntesDaFalha;
            $this->falha(fn () => $this->anexos->salvar($this->ordem, $file, [], $this->usuario), RuntimeException::class, 'armazenar');
            self::assertNotNull($file->destino);
            self::assertFileDoesNotExist($file->destino);
            $this->semRegistros();
        }
    }

    public function testDownloadEExclusaoRecusamTraversalArquivoDeOutraOrdemELixeira(): void
    {
        $file = $this->upload('arte.png', $this->png());
        $id = $this->anexos->salvar($this->ordem, $file, [], $this->usuario);
        $original = $this->anexos->obter($id);
        $sentinela = $this->upload('sentinela.png', $this->png());
        $caminhoSentinela = 'cache/' . basename($this->staging) . '/' . basename($sentinela->origem);
        foreach (['../../../' . $caminhoSentinela, $caminhoSentinela, $original['arquivo'] . '/../../sentinela.png', str_replace('/', '\\', $original['arquivo'])] as $path) {
            self::assertTrue($this->anexoDb->table('anexos_os')->where('id_anexo', $id)->update(['arquivo' => $path]));
            $this->falha(fn () => $this->anexos->obter($id), PageNotFoundException::class);
            AnexoAtendimento::removerArquivo($path);
            self::assertFileExists($sentinela->origem);
            self::assertFileExists($file->destino);
        }
        self::assertTrue($this->anexoDb->table('anexos_os')->where('id_anexo', $id)->update(['arquivo' => $original['arquivo'], 'id_ordem' => $this->novaOrdem()]));
        $this->falha(fn () => $this->anexos->obter($id), PageNotFoundException::class);
        self::assertTrue($this->anexoDb->table('anexos_os')->where('id_anexo', $id)->update(['id_ordem' => $this->ordem]));
        self::assertTrue($this->anexoDb->table('ordens_de_servicos')->where('id_ordem', $this->ordem)->update(['deleted_at' => date('Y-m-d H:i:s')]));
        $this->falha(fn () => $this->anexos->obter($id), PageNotFoundException::class);
        self::assertTrue($this->anexoDb->table('ordens_de_servicos')->where('id_ordem', $this->ordem)->update(['deleted_at' => null]));
        self::assertSame($id, (int) $this->anexos->obter($id)['id_anexo']);
        AnexoAtendimento::removerArquivo($original['arquivo']);
        self::assertFileDoesNotExist($file->destino);
        $this->falha(fn () => $this->anexos->obter($id), PageNotFoundException::class);
    }

    private function upload(string $nome, string $bytes, string $mime = 'image/png', ?int $size = null, int $error = UPLOAD_ERR_OK): AnexoTestUploadedFile
    {
        $path = $this->staging . DIRECTORY_SEPARATOR . bin2hex(random_bytes(12));
        self::assertSame(strlen($bytes), file_put_contents($path, $bytes));
        $file = new AnexoTestUploadedFile($path, $nome, $mime, $size ?? strlen($bytes), $error);
        $this->uploads[] = $file;
        return $file;
    }

    private function png(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl6vN0AAAAASUVORK5CYII=', true);
    }

    private function novaOrdem(array $dados = []): int
    {
        $id = $this->fixture('ordens_de_servicos', array_replace([
            'numero' => 'TEST-' . bin2hex(random_bytes(10)), 'id_cliente' => $this->cliente, 'id_vendedor' => $this->vendedor,
            'id_tecnico' => null, 'id_atendente' => $this->usuario, 'created_by' => $this->usuario,
            'situacao' => 'Em aberto', 'status_operacional' => 'aguardando_aprovacao', 'deleted_at' => null,
        ], $dados));
        $this->ordens[] = $id;
        return $id;
    }

    private function novoItem(int $ordem, array $dados = []): int
    {
        return $this->fixture('servicos_mao_de_obra_da_os', array_replace([
            'id_ordem' => $ordem, 'nome' => 'Impressão teste', 'quantidade' => '1', 'valor' => '70.00',
            'deleted_at' => null, 'removido_at' => null,
        ], $dados));
    }

    private function fixture(string $tabela, array $dados): int
    {
        $pk = null;
        foreach ($this->anexoDb->getFieldData($tabela) as $campo) {
            if ($campo->primary_key) {
                $pk = $campo->name;
            }
            if ($campo->primary_key || array_key_exists($campo->name, $dados) || $campo->nullable || $campo->default !== null) {
                continue;
            }
            $tipo = strtolower($campo->type);
            $dados[$campo->name] = match (true) {
                $tipo === 'date' => date('Y-m-d'),
                $tipo === 'datetime', $tipo === 'timestamp' => date('Y-m-d H:i:s'),
                $tipo === 'time' => date('H:i:s'),
                preg_match('/int|decimal|float|double|bit/', $tipo) === 1 => 0,
                default => '',
            };
        }
        self::assertNotNull($pk);
        self::assertTrue($this->anexoDb->table($tabela)->insert($dados), 'Fixture: ' . $tabela);
        $id = (int) $this->anexoDb->insertID();
        $this->fixtures[] = [$tabela, $pk, $id];
        return $id;
    }

    private function semRegistros(): void
    {
        self::assertSame(0, $this->anexoDb->table('anexos_os')->whereIn('id_ordem', $this->ordens)->countAllResults());
        self::assertSame(0, $this->anexoDb->table('ordens_de_servicos_historico')->whereIn('id_ordem', $this->ordens)->countAllResults());
    }

    private function falha(callable $operacao, string $classe = InvalidArgumentException::class, string $trecho = ''): void
    {
        try {
            $operacao();
        } catch (Throwable $e) {
            self::assertInstanceOf($classe, $e);
            if ($trecho !== '') {
                self::assertStringContainsString($trecho, $e->getMessage());
            }
            return;
        }
        self::fail('A operação deveria ter sido recusada.');
    }
}

/** Apenas o transporte HTTP é substituído; MIME, extensão e conteúdo são reais. */
final class AnexoTestUploadedFile extends UploadedFile
{
    public string $origem;
    public ?string $destino = null;
    public bool $falharMovimento = false;
    public bool $gravarAntesDaFalha = false;

    public function __construct(string $path, string $originalName, ?string $mimeType = null, ?int $size = null, ?int $error = null, ?string $clientPath = null)
    {
        parent::__construct($path, $originalName, $mimeType, $size, $error, $clientPath);
        $this->origem = $path;
    }

    public function isValid(): bool
    {
        return $this->getError() === UPLOAD_ERR_OK && is_file($this->origem);
    }

    public function move(string $targetPath, ?string $name = null, bool $overwrite = false)
    {
        $this->destino = rtrim($targetPath, '/\\') . DIRECTORY_SEPARATOR . $name;
        if (! $this->falharMovimento || $this->gravarAntesDaFalha) {
            $this->hasMoved = rename($this->origem, $this->destino);
        }
        return ! $this->falharMovimento && $this->hasMoved;
    }
}

/** Falhas determinísticas do driver, com transações reais no clone. */
class AnexoTestConnection extends Connection
{
    public ?string $falharQuery = null;
    public bool $falharCommit = false;
    public bool $falharInicio = false;

    public function query(string $sql, $binds = null, bool $setEscapeFlags = true, string $queryClass = '')
    {
        if ($this->falharQuery !== null && str_contains($sql, $this->falharQuery)) {
            $this->falharQuery = null;
            return false;
        }
        return parent::query($sql, $binds, $setEscapeFlags, $queryClass);
    }

    public function transBegin(bool $testMode = false): bool
    {
        if ($this->falharInicio) {
            $this->falharInicio = false;
            return false;
        }
        return parent::transBegin($testMode);
    }

    public function transCommit(): bool
    {
        if ($this->falharCommit) {
            $this->falharCommit = false;
            return false;
        }
        return parent::transCommit();
    }
}

// BaseConnection resolve estes nomes a partir da classe concreta da conexão.
class AnexoTestBuilder extends Builder
{
}

class AnexoTestResult extends Result
{
}
