<?php

namespace App\Tests\Libraries;

use App\Libraries\RecebimentoAtendimento;
use App\Libraries\FaturamentoNegocio;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;
use InvalidArgumentException;
use LogicException;
use RuntimeException;

/**
 * @group atendimento-database
 * Usa exclusivamente o grupo tests ja migrado; todas as fixtures ficam em rollback.
 */
final class RecebimentoAtendimentoTest extends CIUnitTestCase
{
    private ?BaseConnection $financeDb = null;
    private RecebimentoAtendimento $recebimentos;
    private bool $transacao = false;
    private int $idOrdem;
    private int $idCliente;
    private int $idLogin;
    private int $idVendedor;
    private string $pix;
    private string $cartao;

    protected function setUp(): void
    {
        parent::setUp();
        $config = config('Database');
        $nome = (string) ($config->tests['database'] ?? '');
        if (ENVIRONMENT !== 'testing' || preg_match('/\A[A-Za-z0-9_]+_test\z/', $nome) !== 1
            || $nome === ($config->default['database'] ?? '')) {
            throw new LogicException('Recebimentos: o banco exclusivo do grupo tests deve terminar em _test e diferir do principal.');
        }
        $this->financeDb = Database::connect('tests', false);
        $efetivo = $this->financeDb->query('SELECT DATABASE() AS nome')->getRowArray()['nome'] ?? '';
        if ($efetivo !== $nome || preg_match('/_test\z/', $efetivo) !== 1) {
            throw new LogicException('Recebimentos: a conexao efetiva nao corresponde ao banco de testes autorizado.');
        }
        if (! $this->financeDb->fieldExists('id_recebimento', 'lancamentos')
            || ! $this->financeDb->fieldExists('removido_at', 'parcelas_do_pagamento_os')) {
            throw new LogicException('Aplique as migrations de atendimento no clone de testes antes de executar esta suite.');
        }
        $this->transacao = $this->financeDb->transBegin();
        self::assertTrue($this->transacao);
        $sufixo = bin2hex(random_bytes(6));
        $this->idLogin = $this->fixture('login', ['usuario' => 'fin_test_' . $sufixo, 'primeiro_nome' => 'Teste Financeiro']);
        $this->idCliente = $this->fixture('clientes', ['nome' => 'Cliente de Teste Financeiro']);
        $this->idVendedor = $this->fixture('vendedores', ['nome' => 'Atendente de Teste Financeiro']);
        $this->pix = 'PIX Teste ' . $sufixo;
        $this->cartao = 'Credito Teste ' . $sufixo;
        $this->fixture('formas_de_pagamento', ['nome' => $this->pix, 'disponivel_servicos' => 1]);
        $this->fixture('formas_de_pagamento', ['nome' => $this->cartao, 'disponivel_servicos' => 1]);
        $this->idOrdem = $this->novaOrdem();
        $this->recebimentos = new RecebimentoAtendimento($this->financeDb);
    }

    protected function tearDown(): void
    {
        try {
            if ($this->financeDb !== null && $this->transacao) {
                $this->financeDb->transRollback();
            }
            $this->financeDb?->close();
        } finally {
            parent::tearDown();
        }
    }

    public function testResumoIgnoraCortesiaERemovidosEConsideraDescontoEAdicionais(): void
    {
        $this->financeDb->table('ordens_de_servicos')->where('id_ordem', $this->idOrdem)->update([
            'desconto_tipo' => 'percentual', 'desconto_informado' => '10.00', 'frete' => '50.00', 'outros' => '20.00',
        ]);
        $this->item($this->idOrdem, ['valor' => '800.00', 'valor_catalogo' => '800.00', 'cortesia' => 1]);
        $this->item($this->idOrdem, ['valor' => '900.00', 'removido_at' => date('Y-m-d H:i:s')]);
        $resumo = $this->recebimentos->resumo($this->idOrdem);
        self::assertSame('970.00', $resumo['total']);
        self::assertSame('970.00', $resumo['saldo']);
        self::assertSame('0.00', $resumo['pago']);
        self::assertNull($resumo['conta']);
    }

    public function testEntradaSemCaixaRegistraAutoriaESaldoSemGerarReceitaNova(): void
    {
        $id = $this->recebimentos->registrar($this->idOrdem, $this->dados('400,00'), $this->idLogin);
        $resumo = $this->recebimentos->resumo($this->idOrdem);
        self::assertSame('400.00', $resumo['pago']);
        self::assertSame('600.00', $resumo['saldo']);
        self::assertSame('600.00', $resumo['conta']['valor']);
        self::assertSame('400.00', $resumo['conta']['valor_pago']);
        self::assertSame('Aberta', $resumo['conta']['status']);
        self::assertSame('Servicos', $resumo['conta']['tipo_negocio']);
        self::assertSame($this->idLogin, (int) $resumo['recebimentos'][0]['created_by']);
        self::assertNull($resumo['recebimentos'][0]['id_caixa']);
        self::assertSame(0, $this->financeDb->table('lancamentos')->where('id_recebimento', $id)->countAllResults());
        $evento = $this->financeDb->table('ordens_de_servicos_historico')->where('id_ordem', $this->idOrdem)->get()->getRowArray();
        self::assertSame('recebido', $evento['evento']);
        self::assertSame($this->idLogin, (int) $evento['id_login']);
    }

    public function testPagamentoMistoQuitaUmaUnicaContaEIdentificaRecebimentoDoCaixa(): void
    {
        $idCaixa = $this->fixture('caixas', ['status' => 'Aberto']);
        $this->recebimentos->registrar($this->idOrdem, $this->dados('300.00'), $this->idLogin);
        $dados = $this->dados('700.00');
        $dados['forma_de_pagamento'] = $this->cartao;
        $dados['id_caixa'] = $idCaixa;
        $id = $this->recebimentos->registrar($this->idOrdem, $dados, $this->idLogin);
        $resumo = $this->recebimentos->resumo($this->idOrdem);
        self::assertSame('1000.00', $resumo['pago']);
        self::assertSame('0.00', $resumo['saldo']);
        self::assertSame('Paga', $resumo['conta']['status']);
        self::assertCount(2, $resumo['recebimentos']);
        self::assertSame(1, $this->financeDb->table('contas_a_receber')->where('id_ordem', $this->idOrdem)->countAllResults());
        $lancamento = $this->financeDb->table('lancamentos')->where('id_recebimento', $id)->get()->getRowArray();
        self::assertSame('recebimento_os', $lancamento['natureza']);
        self::assertSame('700.00', $lancamento['valor']);
    }

    public function testRepetirOperacaoRetornaMesmoReciboSemDuplicarValor(): void
    {
        $dados = $this->dados('250.01');
        $id = $this->recebimentos->registrar($this->idOrdem, $dados, $this->idLogin);
        self::assertSame($id, $this->recebimentos->registrar($this->idOrdem, $dados, $this->idLogin));
        self::assertSame('250.01', $this->recebimentos->resumo($this->idOrdem)['pago']);
        self::assertCount(1, $this->recebimentos->resumo($this->idOrdem)['recebimentos']);
        $dados['valor'] = '250.02';
        $this->expectException(RuntimeException::class);
        $this->recebimentos->registrar($this->idOrdem, $dados, $this->idLogin);
    }

    public function testMesmaChaveNaoPodeSerReutilizadaEmOutroAtendimento(): void
    {
        $dados = $this->dados('10.00');
        $this->recebimentos->registrar($this->idOrdem, $dados, $this->idLogin);
        $this->expectException(RuntimeException::class);
        $this->recebimentos->registrar($this->novaOrdem(), $dados, $this->idLogin);
    }

    public function testRecebimentoAcimaDoSaldoNaoAlteraContaNemCriaRecibo(): void
    {
        $this->recebimentos->registrar($this->idOrdem, $this->dados('999.99'), $this->idLogin);
        try {
            $this->recebimentos->registrar($this->idOrdem, $this->dados('0.02'), $this->idLogin);
            self::fail('Recebimento maior que o saldo foi aceito.');
        } catch (RuntimeException $exception) {
            self::assertStringContainsString('saldo', $exception->getMessage());
        }
        $resumo = $this->recebimentos->resumo($this->idOrdem);
        self::assertSame('0.01', $resumo['saldo']);
        self::assertSame('0.01', $resumo['conta']['valor']);
        self::assertCount(1, $resumo['recebimentos']);
    }

    public function testValoresInvalidosNaoGeramRecebimento(): void
    {
        foreach (['0', '-1.00', '', '1e2', '10.001', '10000000000000.00', 'abc', 0.1] as $valor) {
            try {
                $dados = $this->dados('1.00');
                $dados['valor'] = $valor;
                $this->recebimentos->registrar($this->idOrdem, $dados, $this->idLogin);
                self::fail('Valor invalido foi aceito.');
            } catch (InvalidArgumentException $exception) {
                self::assertNotSame('', $exception->getMessage());
            }
        }
        self::assertSame([], $this->recebimentos->resumo($this->idOrdem)['recebimentos']);
    }

    public function testCaixaFechadoFormaInexistenteEAutorInvalidoNaoGravam(): void
    {
        $idCaixa = $this->fixture('caixas', ['status' => 'Fechado']);
        $casos = [
            [$this->dados('1.00') + ['id_caixa' => $idCaixa], $this->idLogin],
            [array_replace($this->dados('1.00'), ['forma_de_pagamento' => 'inexistente_' . bin2hex(random_bytes(6))]), $this->idLogin],
            [$this->dados('1.00'), 0],
        ];
        foreach ($casos as [$dados, $autor]) {
            try {
                $this->recebimentos->registrar($this->idOrdem, $dados, $autor);
                self::fail('Recebimento invalido foi aceito.');
            } catch (RuntimeException | InvalidArgumentException $exception) {
                self::assertNotSame('', $exception->getMessage());
            }
        }
        self::assertSame([], $this->recebimentos->resumo($this->idOrdem)['recebimentos']);
        self::assertNull($this->recebimentos->resumo($this->idOrdem)['conta']);
    }

    public function testCanceladoOuExcluidoNaoRecebeMasPodeSerEstornado(): void
    {
        $id = $this->recebimentos->registrar($this->idOrdem, $this->dados('100.00'), $this->idLogin);
        foreach ([['situacao' => 'Cancelada', 'status_operacional' => 'cancelado'], ['deleted_at' => date('Y-m-d H:i:s')]] as $estado) {
            $this->financeDb->table('ordens_de_servicos')->where('id_ordem', $this->idOrdem)->update($estado);
            try {
                $this->recebimentos->registrar($this->idOrdem, $this->dados('1.00'), $this->idLogin);
                self::fail('Atendimento inativo recebeu pagamento.');
            } catch (RuntimeException $exception) {
                self::assertStringContainsString('cancelado ou excluido', $exception->getMessage());
            }
        }
        $this->recebimentos->estornar($id, $this->idLogin, 'Cancelamento do atendimento');
        $resumo = $this->recebimentos->resumo($this->idOrdem);
        self::assertSame('0.00', $resumo['pago']);
        self::assertSame('0.00', $resumo['conta']['valor']);
        self::assertSame('Cancelada', $resumo['conta']['status']);
    }

    public function testEstornoPreservaReciboLancamentoEMotivoOriginalAoRepetir(): void
    {
        $dados = $this->dados('125.45') + ['id_caixa' => $this->fixture('caixas', ['status' => 'Aberto'])];
        $id = $this->recebimentos->registrar($this->idOrdem, $dados, $this->idLogin);
        $this->recebimentos->estornar($id, $this->idLogin, 'Pagamento devolvido');
        $this->recebimentos->estornar($id, $this->idLogin, 'Outro motivo repetido');
        self::assertSame($id, $this->recebimentos->registrar($this->idOrdem, $dados, $this->idLogin));
        $resumo = $this->recebimentos->resumo($this->idOrdem);
        self::assertSame('0.00', $resumo['pago']);
        self::assertSame('1000.00', $resumo['saldo']);
        self::assertSame('1000.00', $resumo['conta']['valor']);
        self::assertCount(1, $resumo['recebimentos']);
        self::assertSame('Pagamento devolvido', $resumo['recebimentos'][0]['estorno_motivo']);
        self::assertSame($this->idLogin, (int) $resumo['recebimentos'][0]['estornado_by']);
        self::assertSame(1, $this->financeDb->table('lancamentos')->where('id_recebimento', $id)->countAllResults());
        self::assertSame(1, $this->financeDb->table('ordens_de_servicos_historico')->where('id_ordem', $this->idOrdem)->where('evento', 'estornado')->countAllResults());
    }

    public function testFormaDesabilitadaParaServicosNaoAceitaRecebimento(): void
    {
        $this->financeDb->table('formas_de_pagamento')->where('nome', $this->pix)->update(['disponivel_servicos' => 0]);
        $this->expectException(InvalidArgumentException::class);
        $this->recebimentos->registrar($this->idOrdem, $this->dados('100.00'), $this->idLogin);
    }

    public function testAdiantamentoNaoDuplicaReceitaELancamentoEstornadoSaiDoCaixa(): void
    {
        $faturamento = new FaturamentoNegocio($this->financeDb);
        $hoje = date('Y-m-d');
        $receitaAnterior = $faturamento->totalLancamentos($hoje, $hoje, 'Servicos');
        $idCaixa = $this->fixture('caixas', ['status' => 'Aberto']);
        $this->fixture('lancamentos', [
            'id_caixa' => $idCaixa, 'tipo_negocio' => 'Servicos', 'natureza' => 'receita',
            'valor' => '17.05', 'data' => $hoje, 'deleted_at' => null,
        ]);
        $id = $this->recebimentos->registrar($this->idOrdem, $this->dados('250.00') + ['id_caixa' => $idCaixa], $this->idLogin);
        self::assertEqualsWithDelta($receitaAnterior + 17.05, $faturamento->totalLancamentos($hoje, $hoje, 'Servicos'), 0.00001);
        $soma = $faturamento->consultaLancamentos()->select('SUM(l.valor) AS valor', false)->where('l.id_caixa', $idCaixa)->get()->getRowArray();
        self::assertSame('267.05', $soma['valor']);
        $this->recebimentos->estornar($id, $this->idLogin, 'Devolucao testada');
        $soma = $faturamento->consultaLancamentos()->select('SUM(l.valor) AS valor', false)->where('l.id_caixa', $idCaixa)->get()->getRowArray();
        self::assertSame('17.05', $soma['valor']);
        self::assertEqualsWithDelta($receitaAnterior + 17.05, $faturamento->totalLancamentos($hoje, $hoje, 'Servicos'), 0.00001);
    }

    public function testFaturamentoDaConclusaoUsaTotalAtualESomeComExclusaoLogica(): void
    {
        $data = '2048-01-02';
        $this->financeDb->table('ordens_de_servicos')->where('id_ordem', $this->idOrdem)->update([
            'situacao' => 'Concretizada', 'status_operacional' => 'concluido', 'data_de_saida' => $data,
            'desconto_tipo' => 'percentual', 'desconto_informado' => '10.00', 'frete' => '30.00',
        ]);
        $this->item($this->idOrdem, ['valor' => '100.00', 'cortesia' => 1]);
        $this->item($this->idOrdem, ['valor' => '999.00', 'removido_at' => date('Y-m-d H:i:s')]);
        $faturamento = new FaturamentoNegocio($this->financeDb);
        $ordens = $faturamento->ordensServicos($data, $data, ['id_cliente' => $this->idCliente]);
        self::assertCount(1, $ordens);
        self::assertSame('930.00', $ordens[0]['valor_total']);
        $this->financeDb->table('ordens_de_servicos')->where('id_ordem', $this->idOrdem)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        self::assertSame([], $faturamento->ordensServicos($data, $data, ['id_cliente' => $this->idCliente]));
    }

    public function testSincronizarNaoDuplicaContaEImpedeTotalMenorQueRecebido(): void
    {
        $this->recebimentos->registrar($this->idOrdem, $this->dados('200.00'), $this->idLogin);
        $idConta = $this->recebimentos->resumo($this->idOrdem)['conta']['id_conta'];
        $this->recebimentos->sincronizarConta($this->idOrdem, '1000.00', '2026-10-10', $this->idCliente, false);
        self::assertSame('Cancelada', $this->recebimentos->resumo($this->idOrdem)['conta']['status']);
        $this->recebimentos->sincronizarConta($this->idOrdem, '1000.00', null, $this->idCliente, true);
        $conta = $this->recebimentos->resumo($this->idOrdem)['conta'];
        self::assertSame($idConta, $conta['id_conta']);
        self::assertSame('800.00', $conta['valor']);
        self::assertSame('2026-10-10', $conta['data_de_vencimento']);
        $this->expectException(RuntimeException::class);
        $this->recebimentos->sincronizarConta($this->idOrdem, '199.99', null, $this->idCliente, true);
    }

    public function testParcelaRemovidaNaoAceitaRecebimento(): void
    {
        $idPlano = $this->fixture('pagamentos_os', ['id_ordem' => $this->idOrdem, 'tipo' => 'Parcelado']);
        $idParcela = $this->fixture('parcelas_do_pagamento_os', [
            'id_pagamento' => $idPlano, 'valor_da_parcela' => '1000.00', 'forma_de_pagamento' => $this->pix,
            'removido_at' => date('Y-m-d H:i:s'),
        ]);
        $this->expectException(InvalidArgumentException::class);
        $this->recebimentos->registrar($this->idOrdem, $this->dados('100.00') + ['id_parcela' => $idParcela], $this->idLogin);
    }

    private function dados(string $valor): array
    {
        return ['valor' => $valor, 'forma_de_pagamento' => $this->pix, 'chave_operacao' => bin2hex(random_bytes(16))];
    }

    private function novaOrdem(): int
    {
        $id = $this->fixture('ordens_de_servicos', [
            'numero' => 'TEST-' . bin2hex(random_bytes(8)), 'situacao' => 'Em andamento', 'status_operacional' => 'aprovado',
            'id_cliente' => $this->idCliente, 'id_vendedor' => $this->idVendedor, 'id_tecnico' => null,
            'desconto_tipo' => 'nenhum', 'desconto_informado' => '0.00', 'frete' => '0.00', 'outros' => '0.00', 'deleted_at' => null,
        ]);
        $this->item($id, ['valor' => '1000.00']);
        return $id;
    }

    private function item(int $idOrdem, array $dados): int
    {
        return $this->fixture('servicos_mao_de_obra_da_os', $dados + [
            'id_ordem' => $idOrdem, 'nome' => 'Servico de teste', 'quantidade' => '1.0000', 'tipo_preco' => 'unidade', 'cortesia' => 0,
        ]);
    }

    /** Preenche apenas campos obrigatorios legados; nenhuma fixture depende de dados de clientes reais. */
    private function fixture(string $tabela, array $dados): int
    {
        foreach ($this->financeDb->getFieldData($tabela) as $campo) {
            if ($campo->primary_key || array_key_exists($campo->name, $dados) || $campo->nullable || $campo->default !== null) {
                continue;
            }
            $tipo = strtolower($campo->type);
            $dados[$campo->name] = match (true) {
                $tipo === 'date' => '2026-09-07',
                $tipo === 'datetime', $tipo === 'timestamp' => '2026-09-07 12:00:00',
                $tipo === 'time' => '12:00:00',
                preg_match('/int|decimal|float|double|bit/', $tipo) === 1 => 0,
                default => '',
            };
        }
        self::assertTrue($this->financeDb->table($tabela)->insert($dados));
        return (int) $this->financeDb->insertID();
    }
}
