<?php

namespace App\Controllers;

use App\Libraries\FaturamentoNegocio;
use App\Libraries\TipoNegocio;
use App\Models\ConfigEmpresaModel;
use App\Models\ClienteModel;
use App\Models\FornecedorModel;
use App\Models\FuncionarioModel;
use App\Models\LancamentoModel;
use App\Models\ProdutoModel;
use App\Models\RetiradaModel;
use App\Models\DespesaModel;
use App\Models\ContaPagarModel;
use App\Models\ContaReceberModel;
use App\Models\VendedorModel;
use CodeIgniter\Controller;

class Relatorios extends Controller
{
    private $links;
    private $config_empresa_model;
    private $cliente_model;
    private $fornecedor_model;
    private $funcionario_model;
    private $lancamento_model;
    private $produto_model;
    private $retirada_model;
    private $despesa_model;
    private $conta_pagar_model;
    private $conta_receber_model;
    private $vendedor_model;
    private $faturamento_negocio;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '6.m',
            'item' => '6.0',
            'subItem' => '6.5'
        ];

        $this->config_empresa_model = new ConfigEmpresaModel();
        $this->cliente_model        = new ClienteModel();
        $this->fornecedor_model     = new FornecedorModel();
        $this->funcionario_model    = new FuncionarioModel();
        $this->lancamento_model     = new LancamentoModel();
        $this->produto_model        = new ProdutoModel();
        $this->retirada_model       = new RetiradaModel();
        $this->despesa_model        = new DespesaModel();
        $this->conta_pagar_model    = new ContaPagarModel;
        $this->conta_receber_model  = new ContaReceberModel();
        $this->vendedor_model       = new VendedorModel();
        $this->faturamento_negocio  = new FaturamentoNegocio();
    }

    /**
     * Monta e exibe o relatorio de clientes.
     */
    public function clientes()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '8.5'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DE CLIENTES',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório de Clientes", 'rota'   => "", 'active' => true]
        ];

        $data['empresa']  = $this->config_empresa_model->where('id_config', 1)->first();
        $data['clientes'] = $this->cliente_model->findAll();

        echo view('templates/header');
        echo view('relatorios/clientes', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de fornecedores.
     */
    public function fornecedores()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '8.6'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DE FORNECEDORES',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório de Fornecedores", 'rota'   => "", 'active' => true]
        ];

        $data['empresa']  = $this->config_empresa_model->where('id_config', 1)->first();
        $data['fornecedores'] = $this->fornecedor_model->findAll();

        echo view('templates/header');
        echo view('relatorios/fornecedores', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de funcionarios.
     */
    public function funcionarios()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '8.7'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DE FUNCIONÁRIOS',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório de Funcionários", 'rota'   => "", 'active' => true]
        ];

        $data['empresa']  = $this->config_empresa_model->where('id_config', 1)->first();
        $data['funcionarios'] = $this->funcionario_model->findAll();

        echo view('templates/header');
        echo view('relatorios/funcionarios', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de historico completo.
     */
    public function historicoCompleto()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.1'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DE VENDAS - HISTÓRICO COMPLETO',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório de Vendas", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!isset($dados['data_inicio']))
        {
            $dados['data_inicio'] = date('Y-m-01');
            $dados['data_final']  = date('Y-m-31');
        }

        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final']  = $dados['data_final'];
        $data['tipo_negocio'] = TipoNegocio::normalizarVenda($dados['tipo_negocio'] ?? null);
        $data['tipos_negocio'] = TipoNegocio::opcoesVendas();

        $data['empresa']  = $this->config_empresa_model->where('id_config', 1)->first();
        $data['vendas'] = in_array($data['tipo_negocio'], [TipoNegocio::TODOS, TipoNegocio::PRODUTOS], true)
            ? $this->faturamento_negocio->vendasProdutos($dados['data_inicio'], $dados['data_final'])
            : [];
        $data['ordens_servicos'] = in_array($data['tipo_negocio'], [TipoNegocio::TODOS, TipoNegocio::SERVICOS], true)
            ? $this->faturamento_negocio->ordensServicos($dados['data_inicio'], $dados['data_final'])
            : [];

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/vendas/historico_completo', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de por cliente.
     */
    public function porCliente()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.2'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DE VENDAS - POR CLIENTE',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório de Vendas", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!isset($dados['id_cliente']))
        {
            $dados['id_cliente'] = 1;
        }

        $data['id_cliente'] = $dados['id_cliente'];
        $data['tipo_negocio'] = TipoNegocio::normalizarVenda($dados['tipo_negocio'] ?? null);
        $data['tipos_negocio'] = TipoNegocio::opcoesVendas();

        $data['empresa']  = $this->config_empresa_model->where('id_config', 1)->first();
        $data['clientes'] = $this->cliente_model->findAll();
        $data['vendas'] = in_array($data['tipo_negocio'], [TipoNegocio::TODOS, TipoNegocio::PRODUTOS], true)
            ? $this->faturamento_negocio->vendasProdutos('1900-01-01', '2999-12-31', ['id_cliente' => $dados['id_cliente']])
            : [];
        $data['ordens_servicos'] = in_array($data['tipo_negocio'], [TipoNegocio::TODOS, TipoNegocio::SERVICOS], true)
            ? $this->faturamento_negocio->ordensServicos('1900-01-01', '2999-12-31', ['id_cliente' => $dados['id_cliente']])
            : [];

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/vendas/por_cliente', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de por vendedor.
     */
    public function porVendedor()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.3'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DE VENDAS - POR VENDEDOR',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório de Vendas", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!isset($dados['id_vendedor']))
        {
            $dados['id_vendedor'] = $this->vendedor_model->idGeral();
        }

        $data['id_vendedor'] = $dados['id_vendedor'];
        $data['tipo_negocio'] = TipoNegocio::normalizarVenda($dados['tipo_negocio'] ?? null);
        $data['tipos_negocio'] = TipoNegocio::opcoesVendas();

        $data['empresa']  = $this->config_empresa_model->where('id_config', 1)->first();
        $data['vendedores'] = $this->vendedor_model->visiveis();
        $data['vendas'] = in_array($data['tipo_negocio'], [TipoNegocio::TODOS, TipoNegocio::PRODUTOS], true)
            ? $this->faturamento_negocio->vendasProdutos('1900-01-01', '2999-12-31', ['id_vendedor' => $dados['id_vendedor']])
            : [];
        $data['ordens_servicos'] = in_array($data['tipo_negocio'], [TipoNegocio::TODOS, TipoNegocio::SERVICOS], true)
            ? $this->faturamento_negocio->ordensServicos('1900-01-01', '2999-12-31', ['id_vendedor' => $dados['id_vendedor']])
            : [];

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/vendas/por_vendedor', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de produtos.
     */
    public function produtos()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.4'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DE PRODUTOS',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório de Produtos", 'rota'   => "", 'active' => true]
        ];

        $data['empresa']  = $this->config_empresa_model->where('id_config', 1)->first();
        $data['produtos'] = $this->produto_model->findAll();

        echo view('templates/header');
        echo view('relatorios/estoque/produtos', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de estoque minimo.
     */
    public function estoqueMinimo()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.5'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DE PRODUTOS - ESTOQUE MÍNIMO',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório de Produtos", 'rota'   => "", 'active' => true]
        ];

        $data['empresa']  = $this->config_empresa_model->where('id_config', 1)->first();
        $data['produtos'] = $this->produto_model->where('quantidade <= quantidade_minima')->findAll();

        echo view('templates/header');
        echo view('relatorios/estoque/quantidade_minima', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de validade dos produtos.
     */
    public function validadeDosProdutos()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.7'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO VALIDADE DOS PRODUTOS',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Validade dos Produtos", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!isset($dados['criterio'])) // Caso não exista mostra os produtos que vencem hoje
        {
            $dados['criterio'] = 1;
        }

        if($dados['criterio'] != 0) // Zero siginifica que o produto está vencido 
        {
            if($dados['criterio'] == 1) // Vence hoje
            {
                $aux = date('Y-m-d');
            }
            else if($dados['criterio'] == 2) // Vence nos próximos 3 dias
            {
                $hoje = date('Y-m-d');
                $aux  = date('Y-m-d', strtotime('+3 days', strtotime(date('Y-m-d'))));
                
                $data['produtos'] = $this->produto_model->where("validade >=", $hoje)->where("validade <=", $aux)->find();
            }
            else if($dados['criterio'] == 3) // Vence nos próximos 7 dias
            {
                $hoje = date('Y-m-d');
                $aux  = date('Y-m-d', strtotime('+7 days', strtotime(date('Y-m-d'))));
                
                $data['produtos'] = $this->produto_model->where("validade >=", $hoje)->where("validade <=", $aux)->find();
            }
            else if($dados['criterio'] == 4) // Vence nos próximos 15 dias
            {
                $hoje = date('Y-m-d');
                $aux  = date('Y-m-d', strtotime('+15 days', strtotime(date('Y-m-d'))));
                
                $data['produtos'] = $this->produto_model->where("validade >=", $hoje)->where("validade <=", $aux)->find();
            }
            else if($dados['criterio'] == 5) // Vence nos próximos 31 dias
            {
                $hoje = date('Y-m-d');
                $aux  = date('Y-m-d', strtotime('+31 days', strtotime(date('Y-m-d'))));
                
                $data['produtos'] = $this->produto_model->where("validade >=", $hoje)->where("validade <=", $aux)->find();
            }
        }
        else // Caso o produto já esteja vencido
        {
            $aux = date('Y-m-d');
            $data['produtos'] = $this->produto_model->where("validade <", $aux)->find();
        }

        $data['criterio'] = $dados['criterio'];
        $data['empresa']   = $this->config_empresa_model->where('id_config', 1)->first();

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/estoque/validade_dos_produtos', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de faturamento diario.
     */
    public function faturamentoDiario()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.8.1'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO FATURAMENTO',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Faturamento", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!empty($dados['mes']) && !empty($dados['ano'])) // Caso o usuário escolha o mes e ano
        {
            $mes = $dados['mes'];
            $ano = $dados['ano'];
        }
        else // Caso o usuário ainda não tenha escolhido o mes e ano mostra o atual
        {
            $mes = date('m');
            $ano = date('Y');
        }

        $data['mes'] = $mes;
        $data['ano'] = $ano;
        $data['tipo_negocio'] = TipoNegocio::normalizarVenda($dados['tipo_negocio'] ?? null);
        $data['tipos_negocio'] = TipoNegocio::opcoesVendas();

        $data['dados_fat'] = $this->faturamento_negocio->resumoDiarioMes((int) $ano, (int) $mes, $data['tipo_negocio']);
        $data['faturamentos'] = array_column($data['dados_fat'], 'total');

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/financeiro/faturamento_diario', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de faturamento detalhado.
     */
    public function faturamentoDetalhado()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.8'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO FATURAMENTO',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Faturamento", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!isset($dados['data_inicio']))
        {
            $dados['data_inicio'] = date('Y-m-01');
            $dados['data_final']  = date('Y-m-31');
        }

        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final']  = $dados['data_final'];
        $data['tipo_negocio'] = TipoNegocio::normalizarVenda($dados['tipo_negocio'] ?? null);
        $data['tipos_negocio'] = TipoNegocio::opcoesVendas();

        $data['empresa']     = $this->config_empresa_model->where('id_config', 1)->first();
        $data['vendas'] = in_array($data['tipo_negocio'], [TipoNegocio::TODOS, TipoNegocio::PRODUTOS], true)
            ? $this->faturamento_negocio->vendasProdutos($dados['data_inicio'], $dados['data_final'])
            : [];
        $data['ordens_servicos'] = in_array($data['tipo_negocio'], [TipoNegocio::TODOS, TipoNegocio::SERVICOS], true)
            ? $this->faturamento_negocio->ordensServicos($dados['data_inicio'], $dados['data_final'])
            : [];
        $lancamentos = $this->lancamento_model
            ->where('data >=', $dados['data_inicio'])
            ->where('data <=', $dados['data_final']);
        if ($data['tipo_negocio'] !== TipoNegocio::TODOS) {
            $lancamentos->where('tipo_negocio', $data['tipo_negocio']);
        }
        $data['lancamentos'] = $lancamentos->find();
        $data['resumo_faturamento'] = $this->faturamento_negocio->resumo(
            $dados['data_inicio'],
            $dados['data_final'],
            $data['tipo_negocio']
        );

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/financeiro/faturamento_detalhado', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de lancamentos.
     */
    public function lancamentos()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '7.9'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO LANÇAMENTOS',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Lançamentos", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!isset($dados['data_inicio']))
        {
            $dados['data_inicio'] = date('Y-m-01');
            $dados['data_final']  = date('Y-m-31');
        }

        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final']  = $dados['data_final'];
        $data['tipo_negocio'] = TipoNegocio::normalizar($dados['tipo_negocio'] ?? TipoNegocio::TODOS, TipoNegocio::TODOS, true);
        $data['tipos_negocio'] = TipoNegocio::opcoes(true);

        $data['empresa']     = $this->config_empresa_model->where('id_config', 1)->first();
        $lancamentos = $this->lancamento_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final']);
        if ($data['tipo_negocio'] !== TipoNegocio::TODOS) {
            $lancamentos->where('tipo_negocio', $data['tipo_negocio']);
        }
        $data['lancamentos'] = $lancamentos->find();

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/financeiro/lancamentos', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de retiradas do caixa.
     */
    public function retiradasDoCaixa()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '8.0'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO RETIRADAS DO CAIXA',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Retiradas do Caixa", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!isset($dados['data_inicio']))
        {
            $dados['data_inicio'] = date('Y-m-01');
            $dados['data_final']  = date('Y-m-31');
        }

        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final']  = $dados['data_final'];

        $data['empresa']     = $this->config_empresa_model->where('id_config', 1)->first();
        $data['retiradas'] = $this->retirada_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final'])->find();

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/financeiro/retiradas_do_caixa', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de despesas.
     */
    public function despesas()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '8.1'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO DESPESAS',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Despesas", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();

        if(!isset($dados['data_inicio']))
        {
            $dados['data_inicio'] = date('Y-m-01');
            $dados['data_final']  = date('Y-m-31');
        }

        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final']  = $dados['data_final'];
        $data['tipo_negocio'] = TipoNegocio::normalizar($dados['tipo_negocio'] ?? TipoNegocio::TODOS, TipoNegocio::TODOS, true);
        $data['tipos_negocio'] = TipoNegocio::opcoes(true);

        $data['empresa']     = $this->config_empresa_model->where('id_config', 1)->first();
        $despesas = $this->despesa_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final']);
        if ($data['tipo_negocio'] !== TipoNegocio::TODOS) {
            $despesas->where('tipo_negocio', $data['tipo_negocio']);
        }
        $data['despesas'] = $despesas->find();

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/financeiro/despesas', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de contas pagar.
     */
    public function contasPagar()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '8.2'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO CONTAS À PAGAR',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Contas à Pagar", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();
        $dados['status'] = $dados['status'] ?? TipoNegocio::TODOS;
        $dados['tipo_negocio'] = $dados['tipo_negocio'] ?? TipoNegocio::TODOS;

        if(!isset($dados['data_inicio']))
        {
            $dados['status']      = "Todos";
            $dados['tipo_negocio'] = TipoNegocio::TODOS;
            $dados['data_inicio'] = date('Y-m-01');
            $dados['data_final']  = date('Y-m-31');
        }

        $data['status']      = $dados['status'];
        $data['tipo_negocio'] = TipoNegocio::normalizar($dados['tipo_negocio'] ?? TipoNegocio::TODOS, TipoNegocio::TODOS, true);
        $data['tipos_negocio'] = TipoNegocio::opcoes(true);
        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final']  = $dados['data_final'];

        $data['empresa'] = $this->config_empresa_model->where('id_config', 1)->first();
        $contas = $this->conta_pagar_model
            ->where('data_de_vencimento >=', $dados['data_inicio'])
            ->where('data_de_vencimento <=', $dados['data_final']);
        if ($dados['status'] !== TipoNegocio::TODOS) {
            $contas->where('status', $dados['status']);
        }
        if ($data['tipo_negocio'] !== TipoNegocio::TODOS) {
            $contas->where('tipo_negocio', $data['tipo_negocio']);
        }
        $data['contas'] = $contas->find();

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/administrativo/contas_a_pagar', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de contas receber.
     */
    public function contasReceber()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '8.3'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO CONTAS À RECEBER',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Contas à Receber", 'rota'   => "", 'active' => true]
        ];

        $dados = $this->request->getvar();
        $dados['status'] = $dados['status'] ?? TipoNegocio::TODOS;
        $dados['tipo_negocio'] = $dados['tipo_negocio'] ?? TipoNegocio::TODOS;

        if(!isset($dados['data_inicio']))
        {
            $dados['status']      = "Todos";
            $dados['tipo_negocio'] = TipoNegocio::TODOS;
            $dados['data_inicio'] = date('Y-m-01');
            $dados['data_final']  = date('Y-m-31');
        }

        $data['status']      = $dados['status'];
        $data['tipo_negocio'] = TipoNegocio::normalizar($dados['tipo_negocio'] ?? TipoNegocio::TODOS, TipoNegocio::TODOS, true);
        $data['tipos_negocio'] = TipoNegocio::opcoes(true);
        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final']  = $dados['data_final'];

        $data['empresa'] = $this->config_empresa_model->where('id_config', 1)->first();
        $contas = $this->conta_receber_model
            ->where('data_de_vencimento >=', $dados['data_inicio'])
            ->where('data_de_vencimento <=', $dados['data_final']);
        if ($dados['status'] !== TipoNegocio::TODOS) {
            $contas->where('status', $dados['status']);
        }
        if ($data['tipo_negocio'] !== TipoNegocio::TODOS) {
            $contas->where('tipo_negocio', $data['tipo_negocio']);
        }
        $data['contas'] = $contas->find();

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio');

        echo view('templates/header');
        echo view('relatorios/administrativo/contas_a_receber', $data);
        echo view('templates/footer');
    }

    /**
     * Monta e exibe o relatorio de vendedores.
     */
    public function vendedores()
    {
        $data['links'] = [
            'menu' => '7.m',
            'item' => '7.0',
            'subItem' => '8.8'
        ];

        $data['titulo'] = [
            'modulo' => 'RELATÓRIO VENDEDORES',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório Vendedores", 'rota'   => "", 'active' => true]
        ];

        $data['empresa'] = $this->config_empresa_model->where('id_config', 1)->first();
        $data['vendedores']  = $this->vendedor_model->visiveis();

        echo view('templates/header');
        echo view('relatorios/vendedores', $data);
        echo view('templates/footer');
    }
}
