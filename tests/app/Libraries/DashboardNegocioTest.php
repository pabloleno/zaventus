<?php

namespace App\Tests\Libraries;

use App\Libraries\AtendimentoGrafica;
use App\Libraries\DashboardNegocio;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;
use LogicException;

/**
 * @group atendimento-database
 * Usa somente o clone tests migrado, com fixtures revertidas em cada teste.
 */
final class DashboardNegocioTest extends CIUnitTestCase
{
    private ?BaseConnection $dashboardDb = null;
    private DashboardNegocio $dashboard;
    private bool $transacao = false;
    private int $cliente;
    private int $vendedor;

    protected function setUp(): void
    {
        parent::setUp();
        $config = config('Database');
        $nome = (string) ($config->tests['database'] ?? '');
        if (ENVIRONMENT !== 'testing' || preg_match('/\A[A-Za-z0-9_]+_test\z/', $nome) !== 1
            || $nome === ($config->default['database'] ?? '')) {
            throw new LogicException('Dashboard: configure um banco exclusivo tests com sufixo _test, diferente do principal.');
        }
        $this->dashboardDb = Database::connect('tests', false);
        $efetivo = $this->dashboardDb->query('SELECT DATABASE() AS nome')->getRowArray()['nome'] ?? '';
        if ($efetivo !== $nome || preg_match('/_test\z/', $efetivo) !== 1) {
            throw new LogicException('Dashboard: a conexao efetiva nao corresponde ao clone de testes autorizado.');
        }
        if (! $this->dashboardDb->fieldExists('status_operacional', 'ordens_de_servicos')
            || ! $this->dashboardDb->fieldExists('removido_at', 'servicos_mao_de_obra_da_os')) {
            throw new LogicException('Aplique as migrations atuais no clone antes de executar os testes da dashboard.');
        }
        $this->transacao = $this->dashboardDb->transBegin();
        self::assertTrue($this->transacao);
        $sufixo = bin2hex(random_bytes(6));
        $this->cliente = $this->fixture('clientes', ['nome' => 'Cliente Dashboard ' . $sufixo]);
        $this->vendedor = $this->fixture('vendedores', ['nome' => 'Vendedor Dashboard ' . $sufixo]);
        $this->dashboard = new DashboardNegocio($this->dashboardDb);
    }

    protected function tearDown(): void
    {
        try {
            if ($this->dashboardDb !== null && $this->transacao) {
                $this->dashboardDb->transRollback();
            }
            $this->dashboardDb?->close();
        } finally {
            parent::tearDown();
        }
    }

    public function testOrcamentosEmEsperaNaoEntramComoServicosEmAndamento(): void
    {
        $antes = $this->dashboard->montar(2048, 1)['operacao'];
        foreach (array_keys(AtendimentoGrafica::STATUS) as $status) {
            $this->ordem(['status_operacional' => $status]);
        }
        $this->ordem(['status_operacional' => null, 'situacao' => 'Em aberto']);
        $this->ordem(['status_operacional' => '', 'situacao' => 'Aberto']);
        $this->ordem(['status_operacional' => null, 'situacao' => 'Em andamento']);
        $this->ordem(['status_operacional' => 'aguardando_aprovacao', 'deleted_at' => date('Y-m-d H:i:s')]);
        $this->ordem(['status_operacional' => 'em_producao', 'deleted_at' => date('Y-m-d H:i:s')]);

        $depois = $this->dashboard->montar(2048, 1)['operacao'];

        self::assertSame($antes['orcamentos_em_espera'] + 4, $depois['orcamentos_em_espera']);
        self::assertSame($antes['os_abertas'] + 9, $depois['os_abertas']);
        self::assertSame(array_keys(AtendimentoGrafica::STATUS), array_keys($depois['atendimento']['status']));
        self::assertSame(array_keys(AtendimentoGrafica::STATUS), array_keys($depois['atendimento']['valores']));
        foreach (array_keys(AtendimentoGrafica::STATUS) as $status) {
            $acrescimo = match ($status) {
                'aguardando_aprovacao' => 3,
                'em_producao' => 2,
                default => 1,
            };
            self::assertSame(
                $antes['atendimento']['status'][$status] + $acrescimo,
                $depois['atendimento']['status'][$status],
                $status
            );
        }
    }

    public function testValoresUsamMedidasDescontosCortesiaEItensAtivosDoAtendimento(): void
    {
        $antes = $this->dashboard->montar(2048, 1)['operacao'];
        $orcamento = $this->ordem([
            'status_operacional' => 'aguardando_aprovacao',
            'desconto_tipo' => 'percentual', 'desconto_informado' => '10.00',
            'frete' => '20.00', 'outros' => '5.00',
        ]);
        $this->item($orcamento, [
            'tipo_preco' => 'metro_quadrado', 'largura' => '2.0000', 'altura' => '1.5000',
            'quantidade' => '2.0000', 'valor' => '100.00',
        ]);
        $this->item($orcamento, ['valor' => '800.00', 'cortesia' => 1]);
        $this->item($orcamento, ['valor' => '900.00', 'removido_at' => date('Y-m-d H:i:s')]);
        $rascunho = $this->ordem(['status_operacional' => 'em_elaboracao']);
        $this->item($rascunho, ['valor' => '10.01']);
        $legado = $this->ordem([
            'status_operacional' => null, 'situacao' => 'Em aberto', 'desconto' => '5.00',
            'desconto_tipo' => 'percentual', 'desconto_informado' => '99.00',
        ]);
        $this->item($legado, ['valor' => '50.00']);
        $producao = $this->ordem(['status_operacional' => 'em_producao']);
        $this->item($producao, ['valor' => '123.45']);
        $excluido = $this->ordem([
            'status_operacional' => 'aguardando_aprovacao', 'deleted_at' => date('Y-m-d H:i:s'),
        ]);
        $this->item($excluido, ['valor' => '999.99']);

        $depois = $this->dashboard->montar(2048, 1)['operacao'];
        $ordem = $this->dashboardDb->table('ordens_de_servicos')->where('id_ordem', $orcamento)->get()->getRowArray();
        $itens = $this->dashboardDb->table('servicos_mao_de_obra_da_os')->where('id_ordem', $orcamento)->get()->getResultArray();
        self::assertSame('565.00', AtendimentoGrafica::totais($ordem, $itens)['total']);
        self::assertSame(
            bcadd($antes['atendimento']['valores']['aguardando_aprovacao'], '610.00', 2),
            $depois['atendimento']['valores']['aguardando_aprovacao']
        );
        self::assertSame(
            bcadd($antes['atendimento']['valores']['em_elaboracao'], '10.01', 2),
            $depois['atendimento']['valores']['em_elaboracao']
        );
        self::assertSame(
            bcadd($antes['valor_orcamentos_em_espera'], '620.01', 2),
            $depois['valor_orcamentos_em_espera']
        );
        self::assertSame(
            bcadd($antes['atendimento']['valores']['em_producao'], '123.45', 2),
            $depois['atendimento']['valores']['em_producao']
        );
    }

    public function testPeriodoFiltraConclusoesEFaturamentoMasMantemTodaAFilaAtiva(): void
    {
        $antesJaneiro = $this->dashboard->montar(2048, 1);
        $antesFevereiro = $this->dashboard->montar(2048, 2);
        $orcamento = $this->ordem(['status_operacional' => 'aguardando_aprovacao', 'data_de_entrada' => '2020-01-01']);
        $this->item($orcamento, ['valor' => '111.11']);
        $this->ordem(['status_operacional' => 'pronto', 'data_de_entrada' => '2020-01-01']);
        $janeiro = $this->ordem([
            'status_operacional' => 'concluido', 'situacao' => 'Concretizada', 'data_de_saida' => '2048-01-02',
            'desconto_tipo' => 'percentual', 'desconto_informado' => '10.00', 'frete' => '7.00',
        ]);
        $this->item($janeiro, ['valor' => '100.00']);
        $this->item($janeiro, ['valor' => '500.00', 'cortesia' => 1]);
        $this->item($janeiro, ['valor' => '999.00', 'removido_at' => date('Y-m-d H:i:s')]);
        $fevereiro = $this->ordem([
            'status_operacional' => 'concluido', 'situacao' => 'Concretizada', 'data_de_saida' => '2048-02-02',
        ]);
        $this->item($fevereiro, ['valor' => '200.00']);
        $excluida = $this->ordem([
            'status_operacional' => 'concluido', 'situacao' => 'Concretizada', 'data_de_saida' => '2048-01-02',
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);
        $this->item($excluida, ['valor' => '999.00']);

        $depoisJaneiro = $this->dashboard->montar(2048, 1);
        $depoisFevereiro = $this->dashboard->montar(2048, 2);

        self::assertSame($antesJaneiro['operacao']['os_concretizadas'] + 1, $depoisJaneiro['operacao']['os_concretizadas']);
        self::assertSame($antesFevereiro['operacao']['os_concretizadas'] + 1, $depoisFevereiro['operacao']['os_concretizadas']);
        self::assertEqualsWithDelta($antesJaneiro['faturamento']['servicos'] + 97.00, $depoisJaneiro['faturamento']['servicos'], 0.00001);
        self::assertEqualsWithDelta($antesFevereiro['faturamento']['servicos'] + 200.00, $depoisFevereiro['faturamento']['servicos'], 0.00001);
        self::assertSame($depoisJaneiro['operacao']['atendimento'], $depoisFevereiro['operacao']['atendimento']);
        self::assertSame($antesJaneiro['operacao']['orcamentos_em_espera'] + 1, $depoisJaneiro['operacao']['orcamentos_em_espera']);
        self::assertSame($antesJaneiro['operacao']['os_abertas'] + 1, $depoisJaneiro['operacao']['os_abertas']);
        self::assertSame($depoisJaneiro['operacao']['os_abertas'], $depoisFevereiro['operacao']['os_abertas']);
        self::assertSame(
            round($depoisJaneiro['faturamento']['servicos'] / $depoisJaneiro['operacao']['os_concretizadas'], 2),
            $depoisJaneiro['operacao']['ticket_servicos']
        );
        self::assertEqualsWithDelta($antesJaneiro['mensal'][0]['servicos'] + 97.00, $depoisJaneiro['mensal'][0]['servicos'], 0.00001);
        self::assertEqualsWithDelta($antesJaneiro['mensal'][1]['servicos'] + 200.00, $depoisJaneiro['mensal'][1]['servicos'], 0.00001);
    }

    public function testAlertasRespeitamPrazoDataDaInstalacaoEExclusao(): void
    {
        $antes = $this->dashboard->montar(2048, 1)['operacao']['atendimento'];
        $hoje = date('Y-m-d');
        $ontem = date('Y-m-d', strtotime('-1 day'));
        $amanha = date('Y-m-d', strtotime('+1 day'));
        $this->ordem(['status_operacional' => 'em_producao', 'previsao_conclusao' => $ontem]);
        $this->ordem(['status_operacional' => 'em_producao', 'previsao_conclusao' => $hoje]);
        $this->ordem(['status_operacional' => 'concluido', 'previsao_conclusao' => $ontem]);
        $this->ordem(['status_operacional' => 'cancelado', 'previsao_conclusao' => $ontem]);
        $this->ordem([
            'status_operacional' => 'em_producao', 'previsao_conclusao' => $ontem,
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);
        $this->ordem(['status_operacional' => 'instalacao_agendada', 'execucao_prevista' => $hoje . ' 14:00:00']);
        $this->ordem(['status_operacional' => 'instalacao_agendada', 'execucao_prevista' => $amanha . ' 14:00:00']);
        $this->ordem(['status_operacional' => 'cancelado', 'execucao_prevista' => $hoje . ' 14:00:00']);
        $this->ordem([
            'status_operacional' => 'instalacao_agendada', 'execucao_prevista' => $hoje . ' 14:00:00',
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);

        $depois = $this->dashboard->montar(2048, 1)['operacao']['atendimento'];

        self::assertSame($antes['atrasados'] + 1, $depois['atrasados']);
        self::assertSame($antes['instalacoes_hoje'] + 1, $depois['instalacoes_hoje']);
    }

    private function ordem(array $dados): int
    {
        return $this->fixture('ordens_de_servicos', $dados + [
            'numero' => 'DASH-TEST-' . bin2hex(random_bytes(8)),
            'situacao' => 'Em andamento', 'status_operacional' => 'aprovado',
            'id_cliente' => $this->cliente, 'id_vendedor' => $this->vendedor, 'id_tecnico' => null,
            'data_de_entrada' => '2048-01-01', 'data_de_saida' => null,
            'previsao_conclusao' => null, 'execucao_prevista' => null,
            'desconto' => '0.00', 'desconto_tipo' => 'nenhum', 'desconto_informado' => '0.00',
            'frete' => '0.00', 'outros' => '0.00', 'deleted_at' => null,
        ]);
    }

    private function item(int $idOrdem, array $dados): int
    {
        return $this->fixture('servicos_mao_de_obra_da_os', $dados + [
            'id_ordem' => $idOrdem, 'nome' => 'Servico Dashboard', 'quantidade' => '1.0000',
            'tipo_preco' => 'unidade', 'unidade_dimensao' => 'm', 'cortesia' => 0, 'removido_at' => null,
        ]);
    }

    /** Preenche apenas campos obrigatorios legados, sem depender de cadastros reais. */
    private function fixture(string $tabela, array $dados): int
    {
        foreach ($this->dashboardDb->getFieldData($tabela) as $campo) {
            if ($campo->primary_key || array_key_exists($campo->name, $dados) || $campo->nullable || $campo->default !== null) {
                continue;
            }
            $tipo = strtolower($campo->type);
            $dados[$campo->name] = match (true) {
                $tipo === 'date' => '2048-01-01',
                $tipo === 'datetime', $tipo === 'timestamp' => '2048-01-01 12:00:00',
                $tipo === 'time' => '12:00:00',
                preg_match('/int|decimal|float|double|bit/', $tipo) === 1 => 0,
                default => '',
            };
        }
        self::assertTrue($this->dashboardDb->table($tabela)->insert($dados));
        return (int) $this->dashboardDb->insertID();
    }
}
