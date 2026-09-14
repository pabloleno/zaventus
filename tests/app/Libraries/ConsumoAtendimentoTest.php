<?php

namespace App\Tests\Libraries;

use App\Libraries\ConsumoAtendimento;
use App\Models\ProdutoModel;
use App\Models\ServicoMaoDeObraModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Events\Events;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;
use InvalidArgumentException;
use LogicException;
use RuntimeException;

final class ConsumoAtendimentoTest extends CIUnitTestCase
{
    private ?BaseConnection $estoqueDb = null;
    private bool $transacao = false;
    private ConsumoAtendimento $consumo;
    private int $ordem;
    private int $produto;
    private int $servico;
    private int $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $config = config('Database');
        $nome = (string) ($config->tests['database'] ?? '');
        if (ENVIRONMENT !== 'testing' || preg_match('/\A[A-Za-z0-9_]+_test\z/', $nome) !== 1 || $nome === ($config->default['database'] ?? '')) {
            throw new LogicException('Estoque: use exclusivamente um clone _test diferente do banco principal.');
        }
        $this->estoqueDb = Database::connect('tests', false);
        if (($this->estoqueDb->query('SELECT DATABASE() AS nome')->getRowArray()['nome'] ?? '') !== $nome) {
            throw new LogicException('A conexão efetiva não corresponde ao clone autorizado.');
        }
        if (! $this->estoqueDb->fieldExists('estornado_at', 'reposicoes') || ! $this->estoqueDb->fieldExists('id_servico_os', 'saida_de_mercadorias')) {
            throw new LogicException('Aplique as migrations de atendimento no clone antes dos testes de consumo.');
        }
        $this->transacao = $this->estoqueDb->transBegin();
        self::assertTrue($this->transacao);
        $this->usuario = $this->fixture('login', ['usuario' => 'consumo_' . bin2hex(random_bytes(8)), 'primeiro_nome' => 'Teste Estoque']);
        $cliente = $this->fixture('clientes', ['nome' => 'Cliente de teste de consumo']);
        $vendedor = $this->fixture('vendedores', ['nome' => 'Atendente de teste de consumo']);
        $fornecedor = $this->fixture('fornecedores', ['nome_da_empresa' => 'Fornecedor de teste']);
        $categoria = $this->fixture('categorias_dos_produtos', ['nome' => 'Materiais de teste']);
        $this->produto = $this->fixture('produtos', [
            'nome' => 'Lona de teste', 'unidade' => 'm²', 'quantidade' => '10.0000', 'ativo' => 1,
            'id_categoria' => $categoria, 'id_fornecedor' => $fornecedor, 'deleted_at' => '0000-00-00 00:00:00',
        ]);
        $this->ordem = $this->fixture('ordens_de_servicos', [
            'id_cliente' => $cliente, 'id_vendedor' => $vendedor, 'id_tecnico' => null,
            'status_operacional' => 'em_producao', 'situacao' => 'Em andamento', 'deleted_at' => null,
        ]);
        $this->servico = $this->fixture('servicos_mao_de_obra_da_os', [
            'id_ordem' => $this->ordem, 'nome' => 'Banner cortesia', 'quantidade' => '1.0000',
            'valor' => '0.00', 'valor_catalogo' => '70.00', 'cortesia' => 1, 'removido_at' => null, 'deleted_at' => null,
        ]);
        $this->consumo = new ConsumoAtendimento($this->estoqueDb);
    }

    protected function tearDown(): void
    {
        try {
            if ($this->estoqueDb !== null && $this->transacao) {
                $this->estoqueDb->transRollback();
            }
            $this->estoqueDb?->close();
        } finally {
            parent::tearDown();
        }
    }

    public function testConsomeFracaoEmCortesiaSemCriarReceita(): void
    {
        $antes = $this->estoqueDb->table('pagamentos_do_cliente')->countAllResults();
        $id = $this->consumo->registrar($this->ordem, $this->dados('1,08'), $this->usuario);
        self::assertSame('8.9200', $this->saldo());
        $saida = $this->saida($id);
        self::assertSame('1.0800', $saida['quantidade']);
        self::assertSame($this->servico, (int) $saida['id_servico_os']);
        self::assertSame($this->usuario, (int) $saida['created_by']);
        self::assertSame($antes, $this->estoqueDb->table('pagamentos_do_cliente')->countAllResults());
        self::assertSame(1, $this->historico('consumo'));
    }

    public function testChaveRepetidaNaoDuplicaConsumoOuHistorico(): void
    {
        $dados = $this->dados('2.5000');
        $id = $this->consumo->registrar($this->ordem, $dados, $this->usuario);
        self::assertSame($id, $this->consumo->registrar($this->ordem, $dados, $this->usuario));
        self::assertSame('7.5000', $this->saldo());
        self::assertSame(1, $this->historico('consumo'));
        $this->expectException(InvalidArgumentException::class);
        $dados['quantidade'] = '3.0000';
        $this->consumo->registrar($this->ordem, $dados, $this->usuario);
    }

    public function testEstornoDevolveUmaVezEPreservaRegistro(): void
    {
        $dados = $this->dados('3.1250');
        $id = $this->consumo->registrar($this->ordem, $dados, $this->usuario);
        $this->estoqueDb->table('ordens_de_servicos')->where('id_ordem', $this->ordem)->update(['status_operacional' => 'cancelado']);
        $this->consumo->estornar($this->ordem, $id, $this->usuario, 'Material devolvido');
        $this->consumo->estornar($this->ordem, $id, $this->usuario, 'Repetição');
        self::assertSame($id, $this->consumo->registrar($this->ordem, $dados, $this->usuario));
        self::assertSame('10.0000', $this->saldo());
        self::assertSame('Material devolvido', $this->saida($id)['estorno_motivo']);
        self::assertSame($this->usuario, (int) $this->saida($id)['estornado_by']);
        self::assertNull($this->saida($id)['deleted_at']);
        self::assertSame(1, $this->historico('estorno_consumo'));
    }

    public function testRecusaSaldoInsuficienteSemMovimentoParcial(): void
    {
        try {
            $this->consumo->registrar($this->ordem, $this->dados('10.0001'), $this->usuario);
            self::fail('Saldo insuficiente deveria ser recusado.');
        } catch (InvalidArgumentException $exception) {
            self::assertStringContainsString('insuficiente', $exception->getMessage());
        }
        self::assertSame('10.0000', $this->saldo());
        self::assertSame(0, $this->historico('consumo'));
    }

    public function testRecusaMaterialInativo(): void
    {
        $this->estoqueDb->table('produtos')->where('id_produto', $this->produto)->update(['ativo' => 0]);
        $this->expectException(InvalidArgumentException::class);
        $this->consumo->registrar($this->ordem, $this->dados('1'), $this->usuario);
    }

    public function testRecusaOrdemCancelada(): void
    {
        $this->estoqueDb->table('ordens_de_servicos')->where('id_ordem', $this->ordem)->update(['status_operacional' => 'cancelado']);
        $this->expectException(InvalidArgumentException::class);
        $this->consumo->registrar($this->ordem, $this->dados('1'), $this->usuario);
    }

    public function testRecusaOrdemExcluida(): void
    {
        $this->estoqueDb->table('ordens_de_servicos')->where('id_ordem', $this->ordem)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        $this->expectException(InvalidArgumentException::class);
        $this->consumo->registrar($this->ordem, $this->dados('1'), $this->usuario);
    }

    public function testRecusaServicoRemovido(): void
    {
        $this->estoqueDb->table('servicos_mao_de_obra_da_os')->where('id_servico', $this->servico)->update(['removido_at' => date('Y-m-d H:i:s')]);
        $this->expectException(InvalidArgumentException::class);
        $this->consumo->registrar($this->ordem, $this->dados('1'), $this->usuario);
    }

    public function testRecusaServicoDeOutraOrdem(): void
    {
        $ordem = $this->estoqueDb->table('ordens_de_servicos')->where('id_ordem', $this->ordem)->get()->getRowArray();
        $outraOrdem = $this->fixture('ordens_de_servicos', [
            'id_cliente' => $ordem['id_cliente'], 'id_vendedor' => $ordem['id_vendedor'], 'id_tecnico' => null,
            'status_operacional' => 'em_producao', 'situacao' => 'Em andamento', 'deleted_at' => null,
        ]);
        $this->estoqueDb->table('servicos_mao_de_obra_da_os')->where('id_servico', $this->servico)->update(['id_ordem' => $outraOrdem]);
        $this->expectException(InvalidArgumentException::class);
        $this->consumo->registrar($this->ordem, $this->dados('1'), $this->usuario);
    }

    public function testSaidaVinculadaNaoPodeSerEstornadaPelaRotinaManual(): void
    {
        $id = $this->consumo->registrar($this->ordem, $this->dados('1'), $this->usuario);
        $this->expectException(InvalidArgumentException::class);
        $this->consumo->estornarManual($id, $this->usuario, 'Tentativa pelo estoque');
    }

    public function testReposicaoFracionadaIdempotenteComEstornoRastreavel(): void
    {
        $dados = $this->dados('0.3333');
        unset($dados['id_servico_os']);
        $id = $this->consumo->registrarManual($dados, $this->usuario, true);
        self::assertSame($id, $this->consumo->registrarManual($dados, $this->usuario, true));
        self::assertSame('10.3333', $this->saldo());
        $this->consumo->estornarManual($id, $this->usuario, 'Entrada duplicada', true);
        $this->consumo->estornarManual($id, $this->usuario, 'Repetição', true);
        self::assertSame('10.0000', $this->saldo());
        $registro = $this->estoqueDb->table('reposicoes')->where('id_reposicao', $id)->get()->getRowArray();
        self::assertSame('Entrada duplicada', $registro['estorno_motivo']);
        self::assertSame($this->usuario, (int) $registro['created_by']);
        self::assertNull($registro['deleted_at']);
    }

    public function testReposicaoConsumidaNaoPodeGerarSaldoNegativoNoEstorno(): void
    {
        $dados = $this->dados('1');
        unset($dados['id_servico_os']);
        $id = $this->consumo->registrarManual($dados, $this->usuario, true);
        $this->consumo->registrar($this->ordem, $this->dados('10.5000'), $this->usuario);
        self::assertSame('0.5000', $this->saldo());
        $this->expectException(InvalidArgumentException::class);
        $this->consumo->estornarManual($id, $this->usuario, 'Estorno impossível', true);
    }

    public function testModelMaterialPreservaNomeLongoFracaoEPrecisaoMonetaria(): void
    {
        $nome = str_repeat('Material de teste ', 25);
        $model = new ProdutoModel($this->estoqueDb);
        self::assertTrue($model->update($this->produto, ['nome' => $nome, 'quantidade' => '1,2345', 'valor_de_custo' => '9999999999999.99']));
        $produto = $model->find($this->produto);
        self::assertSame(trim($nome), $produto['nome']);
        self::assertSame('1.2345', $produto['quantidade']);
        self::assertSame('9999999999999.99', $produto['valor_de_custo']);
    }

    public function testFalhaAposBaixaDesfazMovimentoSemApagarTrabalhoAnteriorDaTransacao(): void
    {
        $this->estoqueDb->table('produtos')->where('id_produto', $this->produto)->update(['nome' => 'Alteração anterior']);
        $dados = $this->dados('1.2500');
        $listener = static function ($query): void {
            if (preg_match('/^UPDATE\s+`produtos`/i', $query->getQuery()) === 1) {
                throw new RuntimeException('Falha simulada após atualizar o saldo.');
            }
        };
        Events::on('DBQuery', $listener);
        try {
            $this->consumo->registrar($this->ordem, $dados, $this->usuario);
            self::fail('A falha simulada deveria interromper o consumo.');
        } catch (RuntimeException $exception) {
            self::assertStringContainsString('Falha simulada', $exception->getMessage());
        } finally {
            Events::removeListener('DBQuery', $listener);
        }
        self::assertSame('10.0000', $this->saldo());
        self::assertSame('Alteração anterior', $this->estoqueDb->table('produtos')->where('id_produto', $this->produto)->get()->getRowArray()['nome']);
        self::assertSame(0, $this->estoqueDb->table('saida_de_mercadorias')->where('chave_operacao', $dados['chave_operacao'])->countAllResults());
        self::assertSame(0, $this->historico('consumo'));
        self::assertSame(1, $this->estoqueDb->transDepth);
    }

    public function testFalhaAposDevolucaoDesfazEstornoESaldoEmTransacaoExterna(): void
    {
        $id = $this->consumo->registrar($this->ordem, $this->dados('2'), $this->usuario);
        $listener = static function ($query): void {
            if (preg_match('/^UPDATE\s+`produtos`/i', $query->getQuery()) === 1) {
                throw new RuntimeException('Falha simulada após devolver o saldo.');
            }
        };
        Events::on('DBQuery', $listener);
        try {
            $this->consumo->estornar($this->ordem, $id, $this->usuario, 'Devolução de teste');
            self::fail('A falha simulada deveria interromper o estorno.');
        } catch (RuntimeException $exception) {
            self::assertStringContainsString('Falha simulada', $exception->getMessage());
        } finally {
            Events::removeListener('DBQuery', $listener);
        }
        self::assertSame('8.0000', $this->saldo());
        self::assertNull($this->saida($id)['estornado_at']);
        self::assertNull($this->saida($id)['estorno_motivo']);
        self::assertSame(0, $this->historico('estorno_consumo'));
        self::assertSame(1, $this->estoqueDb->transDepth);
    }

    public function testModelCatalogoPreservaNomeLongoEPrecoExato(): void
    {
        $id = $this->fixture('servicos_mao_de_obra', ['nome' => 'Serviço teste']);
        $nome = str_repeat('Serviço ', 15);
        $model = new ServicoMaoDeObraModel($this->estoqueDb);
        self::assertTrue($model->update($id, ['nome' => $nome, 'valor' => '9999999999999.99']));
        $servico = $model->find($id);
        self::assertSame(trim($nome), $servico['nome']);
        self::assertSame('9999999999999.99', $servico['valor']);
    }

    private function dados(string $quantidade): array
    {
        return ['id_produto' => $this->produto, 'id_servico_os' => $this->servico, 'quantidade' => $quantidade, 'chave_operacao' => bin2hex(random_bytes(16))];
    }

    private function saldo(): string
    {
        return $this->estoqueDb->table('produtos')->where('id_produto', $this->produto)->get()->getRowArray()['quantidade'];
    }

    private function saida(int $id): array
    {
        return $this->estoqueDb->table('saida_de_mercadorias')->where('id_saida', $id)->get()->getRowArray();
    }

    private function historico(string $evento): int
    {
        return $this->estoqueDb->table('ordens_de_servicos_historico')->where('id_ordem', $this->ordem)->where('evento', $evento)->countAllResults();
    }

    private function fixture(string $tabela, array $dados): int
    {
        foreach ($this->estoqueDb->getFieldData($tabela) as $campo) {
            if ($campo->primary_key || array_key_exists($campo->name, $dados) || $campo->nullable || $campo->default !== null) {
                continue;
            }
            $tipo = strtolower($campo->type);
            $dados[$campo->name] = match (true) {
                $tipo === 'date' => '2026-09-12',
                $tipo === 'datetime', $tipo === 'timestamp' => '2026-09-12 12:00:00',
                $tipo === 'time' => '12:00:00',
                preg_match('/int|decimal|float|double|bit/', $tipo) === 1 => 0,
                default => '',
            };
        }
        self::assertTrue($this->estoqueDb->table($tabela)->insert($dados));
        return (int) $this->estoqueDb->insertID();
    }
}
