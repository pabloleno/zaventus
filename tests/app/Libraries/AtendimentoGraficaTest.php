<?php

namespace App\Tests\Libraries;

use App\Libraries\AtendimentoGrafica;
use App\Libraries\RecebimentoAtendimento;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;
use InvalidArgumentException;
use LogicException;
use RuntimeException;

/** @group atendimento-database */
final class AtendimentoGraficaTest extends CIUnitTestCase
{
    private ?BaseConnection $atendimentoDb = null;
    private AtendimentoGrafica $atendimento;
    private bool $transacao = false;
    private int $usuario;
    private int $cliente;
    private int $vendedor;
    private int $catalogo;
    private string $pix;

    protected function setUp(): void
    {
        parent::setUp();
        $config = config('Database');
        $nome = (string) ($config->tests['database'] ?? '');
        if (ENVIRONMENT !== 'testing' || preg_match('/\A[A-Za-z0-9_]+_test\z/', $nome) !== 1
            || $nome === ($config->default['database'] ?? '')) {
            throw new LogicException('Atendimento: configure um banco exclusivo do grupo tests com sufixo _test, diferente do principal.');
        }
        $this->atendimentoDb = Database::connect('tests', false);
        $efetivo = $this->atendimentoDb->query('SELECT DATABASE() AS nome')->getRowArray()['nome'] ?? '';
        if ($efetivo !== $nome || preg_match('/_test\z/', $efetivo) !== 1) {
            throw new LogicException('Atendimento: a conexão efetiva não corresponde ao clone de testes autorizado.');
        }
        if (! $this->atendimentoDb->fieldExists('hash_criacao', 'ordens_de_servicos')
            || ! $this->atendimentoDb->fieldExists('removido_at', 'parcelas_do_pagamento_os')) {
            throw new LogicException('Aplique as migrations atuais no clone antes de executar os testes de atendimento.');
        }
        $this->transacao = $this->atendimentoDb->transBegin();
        self::assertTrue($this->transacao);
        $sufixo = bin2hex(random_bytes(6));
        $this->usuario = $this->fixture('login', ['usuario' => 'atend_test_' . $sufixo, 'primeiro_nome' => 'Atendente Teste']);
        $this->cliente = $this->fixture('clientes', ['nome' => 'Cliente Atendimento ' . $sufixo]);
        $this->vendedor = $this->fixture('vendedores', ['nome' => 'Vendedor Atendimento ' . $sufixo, 'status' => 'Ativo']);
        $this->catalogo = $this->fixture('servicos_mao_de_obra', [
            'nome' => 'Banner 90x120', 'descricao' => 'Banner com impressão', 'valor' => '70.00', 'ativo' => 1,
            'tipo_preco' => 'fixo', 'largura_padrao' => '0.9000', 'altura_padrao' => '1.2000',
            'tipo_execucao' => 'interna', 'arte_padrao' => 'cliente',
        ]);
        $this->pix = 'PIX Atendimento ' . $sufixo;
        $this->fixture('formas_de_pagamento', ['nome' => $this->pix, 'disponivel_servicos' => 1]);
        $this->atendimento = new AtendimentoGrafica($this->atendimentoDb);
    }

    protected function tearDown(): void
    {
        try {
            if ($this->atendimentoDb !== null && $this->transacao) {
                $this->atendimentoDb->transRollback();
            }
            $this->atendimentoDb?->close();
        } finally {
            parent::tearDown();
        }
    }

    public function testBannerSimplesTemTotal140TecnicoOpcionalEAutoriaDoUsuario(): void
    {
        $dados = $this->dados();
        $dados += ['created_by' => 99999, 'id_atendente' => 99999, 'data_de_entrada' => '2001-01-01', 'execucao_endereco' => 'Campo interno indevido'];
        $antes = date('Y-m-d H:i:s');
        $id = $this->atendimento->salvar($dados, $this->usuario);
        $ordem = $this->atendimento->obter($id);

        self::assertMatchesRegularExpression('/\AORC-' . date('Y') . '-\d{6,}\z/', $ordem['numero']);
        self::assertSame('140.00', $ordem['totais']['total']);
        self::assertSame('140.00', $ordem['itens'][0]['total']);
        self::assertSame('1.0800', $ordem['itens'][0]['area']);
        self::assertSame('cliente', $ordem['itens'][0]['arte']);
        self::assertSame('aguardando_aprovacao', $ordem['status_operacional']);
        self::assertSame('Em aberto', $ordem['situacao']);
        self::assertNull($ordem['id_tecnico']);
        self::assertNull($ordem['tecnico']);
        self::assertNull($ordem['execucao_endereco']);
        self::assertNull($ordem['data_de_saida']);
        self::assertNull($ordem['hora_de_saida']);
        self::assertNull($ordem['financeiro']['conta']);
        self::assertSame('0.00', $ordem['financeiro']['pago']);
        self::assertSame($this->usuario, (int) $ordem['created_by']);
        self::assertSame($this->usuario, (int) $ordem['id_atendente']);
        self::assertGreaterThanOrEqual($antes, $ordem['created_at']);
        self::assertSame(date('Y-m-d'), $ordem['data_de_entrada']);
        self::assertSame('Atendente Teste', $ordem['historico'][0]['usuario_nome']);
        self::assertSame('criado', $ordem['historico'][0]['evento']);
        self::assertArrayNotHasKey('senha', $ordem['atendente']);
        foreach (['cliente', 'atendente', 'vendedor', 'tecnico', 'anexos', 'equipamentos', 'historico', 'parcelas', 'financeiro', 'totais', 'itens'] as $campo) {
            self::assertArrayHasKey($campo, $ordem);
        }
    }

    public function testFachadaCortesiaEntradaEFluxoCompletoPreservamHistorico(): void
    {
        $fachada = $this->fixture('servicos_mao_de_obra', ['nome' => 'Fachada ACM', 'valor' => '300', 'tipo_preco' => 'metro_quadrado', 'tipo_execucao' => 'mista', 'arte_padrao' => 'grafica', 'ativo' => 1]);
        $instalacao = $this->fixture('servicos_mao_de_obra', ['nome' => 'Instalação', 'valor' => '300', 'tipo_preco' => 'fixo', 'tipo_execucao' => 'externa', 'necessita_instalacao' => 1, 'ativo' => 1]);
        $arte = $this->fixture('servicos_mao_de_obra', ['nome' => 'Criação de arte', 'valor' => '80', 'ativo' => 1]);
        $dados = array_replace($this->dados(), [
            'itens' => [
                ['id_servico_catalogo' => $fachada, 'largura' => '5,00', 'altura' => '1,20'],
                ['id_servico_catalogo' => $instalacao],
                ['id_servico_catalogo' => $arte, 'cortesia' => 1, 'valor' => '800'],
            ],
            'desconto_tipo' => 'valor', 'desconto_informado' => '100', 'entrada_necessaria' => 1, 'valor_entrada' => '1000',
            'execucao_endereco' => 'Endereço de instalação', 'execucao_prevista' => '2026-10-01T10:00',
            'parcelas' => [$this->parcela('1000'), $this->parcela('1000', '2026-10-01')],
        ]);
        $id = $this->atendimento->salvar($dados, $this->usuario);
        $ordem = $this->atendimento->obter($id);
        self::assertSame('2100.00', $ordem['totais']['subtotal']);
        self::assertSame('2000.00', $ordem['totais']['total']);
        self::assertSame('6.0000', $ordem['itens'][0]['area']);
        self::assertSame('0.00', $ordem['itens'][2]['valor']);
        self::assertSame('80.00', $ordem['itens'][2]['valor_catalogo']);

        (new RecebimentoAtendimento($this->atendimentoDb))->registrar($id, $this->receber('1000'), $this->usuario);
        foreach (['aprovado', 'falta_arte', 'arte_aprovacao', 'aguardando_producao', 'em_producao', 'pronto', 'instalacao_agendada', 'concluido'] as $status) {
            $this->atendimento->mudarStatus($id, $status, $this->usuario);
        }
        $ordem = $this->atendimento->obter($id);
        self::assertSame('1000.00', $ordem['financeiro']['pago']);
        self::assertSame('1000.00', $ordem['financeiro']['saldo']);
        self::assertSame('Concretizada', $ordem['situacao']);
        self::assertNotEmpty($ordem['data_de_saida']);
        self::assertCount(10, $ordem['historico']);
        self::assertCount(8, array_filter($ordem['historico'], static fn (array $evento): bool => $evento['evento'] === 'status_alterado'));
        self::assertCount(1, array_filter($ordem['historico'], static fn (array $evento): bool => $evento['evento'] === 'recebido'));
        self::assertSame('concluido', $ordem['historico'][0]['status_novo']);
    }

    public function testChaveDeCriacaoEhIdempotenteEVinculadaAoAutorEAoConteudo(): void
    {
        $dados = $this->dados();
        $id = $this->atendimento->salvar($dados, $this->usuario);
        self::assertSame($id, $this->atendimento->salvar($dados, $this->usuario));
        self::assertCount(1, $this->atendimento->obter($id)['historico']);
        $this->falha(fn () => $this->atendimento->salvar(array_replace($dados, ['observacoes' => 'Outro pedido']), $this->usuario), 'outros dados');
        $outroUsuario = $this->fixture('login', ['usuario' => 'outro_' . bin2hex(random_bytes(6))]);
        $this->falha(fn () => $this->atendimento->salvar($dados, $outroUsuario), 'outros dados');
        self::assertSame(1, $this->atendimentoDb->table('ordens_de_servicos')->where('chave_criacao', $dados['chave_criacao'])->countAllResults());
    }

    public function testVersaoImpedeSobrescritaEAlterarStatusInvalidaTelaAntiga(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $antiga = $this->edicao($id);
        $this->atendimento->salvar($antiga + ['observacoes' => 'Primeira atualização'], $this->usuario);
        $this->falha(fn () => $this->atendimento->salvar($antiga, $this->usuario), 'outra tela');
        $segunda = $this->edicao($id);
        $this->atendimento->mudarStatus($id, 'aprovado', $this->usuario);
        $this->falha(fn () => $this->atendimento->salvar($segunda, $this->usuario), 'outra tela');
        self::assertSame('Primeira atualização', $this->atendimento->obter($id)['observacoes']);
    }

    public function testParcelaInvalidaDesfazCabecalhoItensEHistoricoMesmoEmTransacaoExterna(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $antes = $this->atendimento->obter($id);
        $dados = $this->edicao($id);
        $dados['itens'][0]['quantidade'] = 3;
        $dados['observacoes'] = 'Não pode persistir';
        $this->falha(fn () => $this->atendimento->salvar($dados, $this->usuario), 'soma das parcelas');
        $depois = $this->atendimento->obter($id);
        self::assertSame($antes['versao'], $depois['versao']);
        self::assertSame($antes['observacoes'], $depois['observacoes']);
        self::assertSame('2.0000', $depois['itens'][0]['quantidade']);
        self::assertSame($antes['historico'], $depois['historico']);
        self::assertSame('140.00', $depois['totais']['total']);
    }

    public function testCatalogoInativoPreservaSnapshotExistenteMasNaoPermiteNovoItem(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $this->atendimentoDb->table('servicos_mao_de_obra')->where('id_servico', $this->catalogo)->update(['ativo' => 0, 'nome' => 'Outro nome', 'valor' => '900']);
        $this->atendimento->salvar($this->edicao($id), $this->usuario);
        $ordem = $this->atendimento->obter($id);
        self::assertSame('Banner 90x120', $ordem['itens'][0]['nome']);
        self::assertSame('70.00', $ordem['itens'][0]['valor']);
        $this->falha(fn () => $this->atendimento->salvar($this->dados(), $this->usuario), 'serviço ativo do catálogo');
    }

    public function testNovoServicoLivreEItemDeOutroAtendimentoSaoRecusados(): void
    {
        $dados = $this->dados();
        $dados['itens'] = [['nome' => 'Digitado', 'valor' => '140']];
        $this->falha(fn () => $this->atendimento->salvar($dados, $this->usuario), 'serviço ativo do catálogo');
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $outro = $this->atendimento->salvar($this->dados(), $this->usuario);
        $dados = $this->edicao($outro);
        $dados['itens'] = $this->edicao($id)['itens'];
        $this->falha(fn () => $this->atendimento->salvar($dados, $this->usuario), 'não pertence');
    }

    public function testRemoverItemMantemLinhaAnexoEIdentidadeDaParcela(): void
    {
        $dados = $this->dados();
        $dados['itens'] = [['id_servico_catalogo' => $this->catalogo], ['id_servico_catalogo' => $this->catalogo]];
        $id = $this->atendimento->salvar($dados, $this->usuario);
        $edicao = $this->edicao($id);
        $removido = (int) $edicao['itens'][0]['id_servico'];
        $idParcela = $edicao['parcelas'][0]['id_parcela'];
        $anexo = $this->fixture('anexos_os', ['id_ordem' => $id, 'id_servico_os' => $removido, 'arquivo' => 'uploads/atendimentos/fixture.pdf']);
        array_shift($edicao['itens']);
        $edicao['parcelas'][0]['valor_da_parcela'] = '70';
        $this->atendimento->salvar($edicao, $this->usuario);
        $ordem = $this->atendimento->obter($id);
        self::assertCount(1, $ordem['itens']);
        self::assertSame('70.00', $ordem['totais']['total']);
        self::assertSame($idParcela, $ordem['parcelas'][0]['id_parcela']);
        self::assertNotNull($this->atendimentoDb->table('servicos_mao_de_obra_da_os')->where('id_servico', $removido)->get()->getRowArray()['removido_at']);
        self::assertSame($anexo, (int) $ordem['anexos'][0]['id_anexo']);
    }

    public function testRecebimentoProtegeTotalClienteEParcelaMesmoDepoisDoEstorno(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $ordem = $this->atendimento->obter($id);
        $financeiro = new RecebimentoAtendimento($this->atendimentoDb);
        $recibo = $financeiro->registrar($id, $this->receber('100') + ['id_parcela' => $ordem['parcelas'][0]['id_parcela']], $this->usuario);
        $dados = $this->edicao($id);
        $dados['itens'][0]['quantidade'] = 1;
        $dados['parcelas'][0]['valor_da_parcela'] = '70';
        $this->falha(fn () => $this->atendimento->salvar($dados, $this->usuario), 'abaixo do valor já recebido');
        $dados = $this->edicao($id);
        $dados['id_cliente'] = $this->fixture('clientes', ['nome' => 'Cliente trocado']);
        $this->falha(fn () => $this->atendimento->salvar($dados, $this->usuario), 'trocar o cliente');
        $financeiro->estornar($recibo, $this->usuario, 'Devolução para teste');
        $dados = $this->edicao($id);
        unset($dados['parcelas'][0]['id_parcela']);
        $this->falha(fn () => $this->atendimento->salvar($dados, $this->usuario), 'Preserve a parcela');
        self::assertCount(1, $this->atendimento->obter($id)['parcelas']);
    }

    public function testConsumoProtegeQuantidadeMedidasECatalogoDoItem(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $item = $this->atendimento->obter($id)['itens'][0];
        $this->consumo($id, (int) $item['id_servico']);
        $dados = $this->edicao($id);
        $dados['itens'][0]['quantidade'] = 3;
        $dados['parcelas'][0]['valor_da_parcela'] = '210';
        $this->falha(fn () => $this->atendimento->salvar($dados, $this->usuario), 'consumo registrado');
        $dados = $this->edicao($id);
        $dados['itens'] = [['id_servico_catalogo' => $this->catalogo, 'quantidade' => 2]];
        $this->falha(fn () => $this->atendimento->salvar($dados, $this->usuario), 'Não remova');
        self::assertSame('2.0000', $this->atendimento->obter($id)['itens'][0]['quantidade']);
    }

    public function testCancelarExigeMotivoEReabrirMantemHistoricoESaidaAnterior(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $this->falha(fn () => $this->atendimento->mudarStatus($id, 'cancelado', $this->usuario), 'motivo');
        $this->atendimento->mudarStatus($id, 'concluido', $this->usuario);
        $saida = $this->atendimento->obter($id)['data_de_saida'];
        $this->atendimento->mudarStatus($id, 'cancelado', $this->usuario, 'Cliente desistiu');
        $cancelado = $this->edicao($id);
        $cancelado['status_operacional'] = 'aprovado';
        $this->falha(fn () => $this->atendimento->salvar($cancelado, $this->usuario), 'Reabra');
        $this->falha(fn () => $this->atendimento->mudarStatus($id, 'aprovado', $this->usuario), 'Reabrir');
        $this->atendimento->reabrir($id, $this->usuario, 'Cliente retomou o pedido');
        $ordem = $this->atendimento->obter($id);
        self::assertSame('aguardando_aprovacao', $ordem['status_operacional']);
        self::assertSame($saida, $ordem['data_de_saida']);
        self::assertSame('reaberto', $ordem['historico'][0]['evento']);
        self::assertSame('Cliente desistiu', $ordem['historico'][1]['motivo']);
    }

    public function testInstalacaoSoPodeSerAgendadaComEnderecoEData(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $this->falha(fn () => $this->atendimento->mudarStatus($id, 'instalacao_agendada', $this->usuario), 'endereço e a data');
        self::assertSame('aguardando_aprovacao', $this->atendimento->obter($id)['status_operacional']);
    }

    public function testLixeiraRestauraEExpurgaSemApagarHistorico(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $this->atendimento->lixeira($id, $this->usuario);
        $ordem = $this->atendimento->obter($id, true);
        self::assertSame($this->usuario, (int) $ordem['deleted_by']);
        self::assertSame(30 * 86400, strtotime($ordem['purge_at']) - strtotime($ordem['deleted_at']));
        $this->falha(fn () => $this->atendimento->obter($id), 'lixeira');
        $this->atendimento->restaurar($id, $this->usuario);
        self::assertNull($this->atendimento->obter($id)['deleted_at']);
        $this->fixture('anexos_os', ['id_ordem' => $id, 'arquivo' => 'uploads/atendimentos/nao_existe.pdf']);
        $this->atendimento->lixeira($id, $this->usuario);
        $this->falha(fn () => $this->atendimento->excluirDefinitivamente($id, $this->usuario, 'incorreto'), 'Digite exatamente');
        $arquivos = $this->atendimento->excluirDefinitivamente($id, $this->usuario, $ordem['numero']);
        self::assertSame('uploads/atendimentos/nao_existe.pdf', $arquivos[0]['arquivo']);
        self::assertSame(0, $this->atendimentoDb->table('ordens_de_servicos')->where('id_ordem', $id)->countAllResults());
        self::assertSame(0, $this->atendimentoDb->table('servicos_mao_de_obra_da_os')->where('id_ordem', $id)->countAllResults());
        self::assertSame(5, $this->atendimentoDb->table('ordens_de_servicos_historico')->where('id_ordem', $id)->countAllResults());
    }

    public function testContaOuConsumoImpedemExpurgoERecebimentoPreAprovacaoPreservaSaldo(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        (new RecebimentoAtendimento($this->atendimentoDb))->registrar($id, $this->receber('40'), $this->usuario);
        $this->atendimento->salvar($this->edicao($id), $this->usuario);
        self::assertSame('100.00', $this->atendimento->obter($id)['financeiro']['conta']['valor']);
        $this->atendimento->lixeira($id, $this->usuario);
        $ordem = $this->atendimento->obter($id, true);
        self::assertSame('Cancelada', $ordem['financeiro']['conta']['status']);
        $this->falha(fn () => $this->atendimento->excluirDefinitivamente($id, $this->usuario, $ordem['numero']), 'registros financeiros');
        $this->atendimento->restaurar($id, $this->usuario);
        self::assertSame('100.00', $this->atendimento->obter($id)['financeiro']['conta']['valor']);

        $outro = $this->atendimento->salvar($this->dados(), $this->usuario);
        $this->consumo($outro, (int) $this->atendimento->obter($outro)['itens'][0]['id_servico']);
        $this->atendimento->lixeira($outro, $this->usuario);
        $numero = $this->atendimento->obter($outro, true)['numero'];
        $this->falha(fn () => $this->atendimento->excluirDefinitivamente($outro, $this->usuario, $numero), 'consumo de material');
    }

    public function testLeituraLegadaNaoMutaBancoENaoPerdeDescontoNaEdicao(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $this->atendimentoDb->table('ordens_de_servicos')->where('id_ordem', $id)->update(['status_operacional' => null, 'desconto' => '10', 'desconto_informado' => 0]);
        $this->atendimentoDb->table('parcelas_do_pagamento_os')->where('id_parcela', $this->atendimento->obter($id)['parcelas'][0]['id_parcela'])->update(['valor_da_parcela' => '130']);
        $ordem = $this->atendimento->obter($id);
        self::assertSame('130.00', $ordem['totais']['total']);
        self::assertSame('10.00', $ordem['desconto_informado']);
        self::assertSame('aguardando_aprovacao', $ordem['status_operacional']);
        self::assertNull($this->atendimentoDb->table('ordens_de_servicos')->where('id_ordem', $id)->get()->getRowArray()['status_operacional']);
        $this->atendimento->salvar($this->edicao($id) + ['desconto_tipo' => $ordem['desconto_tipo'], 'desconto_informado' => $ordem['desconto_informado']], $this->usuario);
        self::assertSame('130.00', $this->atendimento->obter($id)['totais']['total']);
    }

    public function testListagemFiltraClientePeriodoStatusETermoSemIncluirLixeira(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $ordem = $this->atendimento->obter($id);
        $filtros = ['id_cliente' => $this->cliente, 'data_inicio' => date('Y-m-d'), 'data_final' => date('Y-m-d'), 'status' => 'aguardando_aprovacao', 'term' => $ordem['numero']];
        $lista = $this->atendimento->listar($filtros);
        self::assertCount(1, $lista);
        self::assertSame('140.00', $lista[0]['total']);
        self::assertSame('0.00', $lista[0]['total_pago']);
        $this->atendimento->lixeira($id, $this->usuario);
        self::assertSame([], $this->atendimento->listar($filtros));
        self::assertCount(1, $this->atendimento->listar($filtros, true));
    }

    public function testEquipamentoPreservaCamposLegadosVersaoEHistoricoAposRemocao(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $antiga = $this->edicao($id);
        $idEquipamento = $this->atendimento->salvarEquipamento($id, [
            'equipamento' => 'Peça do cliente', 'marca' => 'Marca', 'modelo' => 'Modelo', 'serie' => '123',
            'condicoes' => 'Bom estado', 'defeitos' => 'Risco superficial', 'acessorios' => 'Suporte',
            'solucao' => 'Aplicação de adesivo', 'laudo_tecnico' => 'Conferido', 'termos_de_garantia' => 'Conforme combinado',
        ], $this->usuario);
        $this->falha(fn () => $this->atendimento->salvar($antiga, $this->usuario), 'outra tela');
        $this->atendimento->salvarEquipamento($id, ['id_equipamento' => $idEquipamento, 'solucao' => 'Adesivo aplicado'], $this->usuario);
        $equipamento = $this->atendimento->obter($id)['equipamentos'][0];
        self::assertSame('Bom estado', $equipamento['condicoes']);
        self::assertSame('Conforme combinado', $equipamento['termos_de_garantia']);
        self::assertSame('Adesivo aplicado', $equipamento['solucao']);
        $this->atendimento->removerEquipamento($id, $idEquipamento, $this->usuario);
        $this->atendimento->removerEquipamento($id, $idEquipamento, $this->usuario);
        $ordem = $this->atendimento->obter($id);
        self::assertSame([], $ordem['equipamentos']);
        self::assertCount(4, $ordem['historico']);
        self::assertSame('equipamento_removido', $ordem['historico'][0]['evento']);
        self::assertNotNull($this->atendimentoDb->table('equipamentos_os')->where('id_equipamento', $idEquipamento)->get()->getRowArray()['deleted_at']);
    }

    public function testEquipamentoDeOutroAtendimentoENomeVazioSaoRecusados(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $outro = $this->atendimento->salvar($this->dados(), $this->usuario);
        $idEquipamento = $this->atendimento->salvarEquipamento($id, ['equipamento' => 'Equipamento'], $this->usuario);
        $this->falha(fn () => $this->atendimento->salvarEquipamento($outro, ['id_equipamento' => $idEquipamento, 'equipamento' => 'Troca'], $this->usuario), 'não pertence');
        $this->falha(fn () => $this->atendimento->removerEquipamento($outro, $idEquipamento, $this->usuario), 'não pertence');
        $this->falha(fn () => $this->atendimento->salvarEquipamento($id, ['equipamento' => ''], $this->usuario), 'nome do equipamento');
        $this->atendimento->mudarStatus($id, 'cancelado', $this->usuario, 'Cancelado para teste');
        $this->falha(fn () => $this->atendimento->removerEquipamento($id, $idEquipamento, $this->usuario), 'Reabra');
    }

    public function testFormaInativaPreservaParcelaAnteriorMasImpedeNovoPlano(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $this->atendimentoDb->table('formas_de_pagamento')->where('nome', $this->pix)->update(['disponivel_servicos' => 0]);
        $edicao = $this->edicao($id);
        $idParcela = $edicao['parcelas'][0]['id_parcela'];
        $this->atendimento->salvar($edicao, $this->usuario);
        self::assertSame($idParcela, $this->atendimento->obter($id)['parcelas'][0]['id_parcela']);
        self::assertSame($this->pix, $this->atendimento->obter($id)['parcelas'][0]['forma_de_pagamento']);
        $this->falha(fn () => $this->atendimento->salvar($this->dados(), $this->usuario), 'habilitada para serviços');
        $novaParcela = $this->edicao($id);
        unset($novaParcela['parcelas'][0]['id_parcela']);
        $this->falha(fn () => $this->atendimento->salvar($novaParcela, $this->usuario), 'habilitada para serviços');
    }

    public function testResponsaveisInativosPodemSerMantidosMasNaoAtribuidosANovoAtendimento(): void
    {
        $tecnico = $this->fixture('tecnicos', ['nome' => 'Técnico histórico', 'status' => 'Ativo']);
        $id = $this->atendimento->salvar($this->dados() + ['id_tecnico' => $tecnico], $this->usuario);
        $this->atendimentoDb->table('tecnicos')->where('id_tecnico', $tecnico)->update(['status' => 'Inativo']);
        $this->atendimentoDb->table('vendedores')->where('id_vendedor', $this->vendedor)->update(['status' => 'Removido']);
        $this->atendimento->salvar($this->edicao($id) + ['id_tecnico' => $tecnico, 'id_vendedor' => $this->vendedor], $this->usuario);
        $ordem = $this->atendimento->obter($id);
        self::assertSame($tecnico, (int) $ordem['id_tecnico']);
        self::assertSame($this->vendedor, (int) $ordem['id_vendedor']);
        $this->falha(fn () => $this->atendimento->salvar($this->dados(), $this->usuario), 'vendedor cadastrado');
        $this->atendimentoDb->table('vendedores')->where('id_vendedor', $this->vendedor)->update(['status' => 'Ativo']);
        $this->falha(fn () => $this->atendimento->salvar($this->dados() + ['id_tecnico' => $tecnico], $this->usuario), 'técnico cadastrado');
    }

    public function testGrupoComercialFiltraAntesDoLimiteEDistingueStatusLegado(): void
    {
        $id = $this->atendimento->salvar($this->dados(), $this->usuario);
        $this->atendimentoDb->table('ordens_de_servicos')->where('id_ordem', $id)->update(['status_operacional' => null]);
        for ($n = 0; $n < 201; $n++) {
            $this->fixture('ordens_de_servicos', [
                'id_cliente' => $this->cliente, 'id_vendedor' => $this->vendedor,
                'status_operacional' => 'em_producao', 'situacao' => 'Em andamento', 'deleted_at' => null,
            ]);
        }
        $filtros = ['id_cliente' => $this->cliente, 'grupo' => 'orcamentos'];
        $comerciais = $this->atendimento->listar($filtros);
        self::assertCount(1, $comerciais);
        self::assertSame($id, (int) $comerciais[0]['id_ordem']);
        self::assertCount(200, $this->atendimento->listar($filtros + ['todos' => 1]));
        self::assertCount(200, $this->atendimento->listar(['id_cliente' => $this->cliente, 'grupo' => 'servicos']));
    }

    public function testPercentualPersistidoUsaDuasCasasETelefoneSemDigitosFicaVazio(): void
    {
        $dados = $this->dados();
        $dados['itens'][0]['tipo_execucao'] = 'externa';
        $dados['desconto_tipo'] = 'percentual';
        $dados['desconto_informado'] = '1,2345';
        $dados['execucao_telefone'] = '(__) _____-____';
        $dados['parcelas'][0]['valor_da_parcela'] = '138.28';
        $id = $this->atendimento->salvar($dados, $this->usuario);
        $ordem = $this->atendimento->obter($id);
        self::assertSame('1.23', $ordem['desconto_informado']);
        self::assertSame('138.28', $ordem['totais']['total']);
        self::assertSame('', $ordem['execucao_telefone']);
    }

    private function dados(): array
    {
        return [
            'chave_criacao' => bin2hex(random_bytes(24)), 'id_cliente' => $this->cliente, 'id_vendedor' => $this->vendedor,
            'itens' => [['id_servico_catalogo' => $this->catalogo, 'quantidade' => 2]],
            'parcelas' => [$this->parcela('140')],
        ];
    }

    private function edicao(int $id): array
    {
        $ordem = $this->atendimento->obter($id);
        return ['id_ordem' => $id, 'versao' => $ordem['versao'], 'itens' => $ordem['itens'], 'parcelas' => $ordem['parcelas']];
    }

    private function parcela(string $valor, string $vencimento = '2026-10-01'): array
    {
        return ['valor_da_parcela' => $valor, 'forma_de_pagamento' => $this->pix, 'data_de_vencimento' => $vencimento];
    }

    private function receber(string $valor): array
    {
        return ['valor' => $valor, 'forma_de_pagamento' => $this->pix, 'chave_operacao' => bin2hex(random_bytes(24))];
    }

    private function consumo(int $idOrdem, int $idServico): void
    {
        $produto = $this->fixture('produtos', [
            'nome' => 'Material Teste', 'quantidade' => '10',
            'id_categoria' => $this->fixture('categorias_dos_produtos', ['nome' => 'Categoria Teste']),
            'id_fornecedor' => $this->fixture('fornecedores', ['nome_da_empresa' => 'Fornecedor Teste']),
        ]);
        $this->fixture('saida_de_mercadorias', ['id_produto' => $produto, 'id_ordem' => $idOrdem, 'id_servico_os' => $idServico, 'quantidade' => '1']);
    }

    private function falha(callable $operacao, string $trecho): void
    {
        try {
            $operacao();
            self::fail('A operação deveria ter sido recusada: ' . $trecho);
        } catch (RuntimeException | InvalidArgumentException $exception) {
            self::assertStringContainsString($trecho, $exception->getMessage());
        }
    }

    /** Fixtures sintéticas, sem depender de cadastros reais e sem commits. */
    private function fixture(string $tabela, array $dados): int
    {
        foreach ($this->atendimentoDb->getFieldData($tabela) as $campo) {
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
        $inserido = $this->atendimentoDb->table($tabela)->insert($dados);
        self::assertTrue($inserido, 'Fixture ' . $tabela . ': ' . ($this->atendimentoDb->error()['message'] ?? ''));
        return (int) $this->atendimentoDb->insertID();
    }
}
