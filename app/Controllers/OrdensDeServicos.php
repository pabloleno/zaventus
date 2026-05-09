<?php

namespace App\Controllers;
// namespace App\Models;

use App\Models\PagamentoOsProvisorioModel;
use App\Models\ParcelasDoPagamentoOsProvisorioModel;
use App\Models\OrdemDeServicoProvisorioModel;
use App\Models\EquipamentoOsProvisorioModel;
use App\Models\ProdutoPecaOsProvisorioModel;
use App\Models\ProdutoModel;
use App\Models\CaixaModel;
use App\Models\ServicoMaoDeObraModel;
use App\Models\ServicoMaoDeObraProvisorioModel;
use App\Models\FormaDePagamentoModel;
use App\Models\ClienteModel;
use App\Models\VendedorModel;
use App\Models\TecnicoModel;
use App\Models\VendaModel;
use App\Models\ProdutoDaVendaModel;

use App\Models\OrdemDeServicoModel;
use App\Models\PagamentoOsModel;
use App\Models\ParcelasDoPagamentoOsModel;
use App\Models\ServicoMaoDeObraOsModel;
use App\Models\EquipamentoOsModel;
use App\Models\ProdutoPecaOsModel;

use CodeIgniter\Controller;

class OrdensDeServicos extends Controller
{
    private $links;
    private $pagamento_os_provisorio_model;
    private $parcelas_do_pagamento_os_provisorio_model;
    private $ordem_de_servico_provisorio_model;
    private $equipamento_os_provisorio_model;
    private $produto_peca_os_provisorio_model;
    private $produto_model;
    private $caixa_model;
    private $servico_mao_de_obra_model;
    private $servico_mao_de_obra_provisorio_model;
    private $forma_de_pagamento_model;
    private $cliente_model;
    private $vendedor_model;
    private $tecnico_model;

    private $ordem_de_servico_model;
    private $pagamento_os_model;
    private $parcelas_do_pagamento_os_model;
    private $servico_mao_de_obra_os_model;
    private $equipamento_os_model;
    private $produto_peca_os_model;
    private $venda_model;
    private $produto_da_venda_model;

    function __construct()
    {
        $this->links = [
            'menu' => '2.m',
            'item' => '2.0',
            'subItem' => '2.6'
        ];

        $this->pagamento_os_provisorio_model             = new PagamentoOsProvisorioModel();
        $this->parcelas_do_pagamento_os_provisorio_model = new ParcelasDoPagamentoOsProvisorioModel();
        $this->ordem_de_servico_provisorio_model         = new OrdemDeServicoProvisorioModel();
        $this->equipamento_os_provisorio_model           = new EquipamentoOsProvisorioModel();
        $this->produto_peca_os_provisorio_model          = new ProdutoPecaOsProvisorioModel();
        $this->produto_model                             = new ProdutoModel();
        $this->caixa_model                               = new CaixaModel();
        $this->servico_mao_de_obra_model                 = new ServicoMaoDeObraModel();
        $this->servico_mao_de_obra_provisorio_model      = new ServicoMaoDeObraProvisorioModel();
        $this->forma_de_pagamento_model                  = new FormaDePagamentoModel();
        $this->cliente_model                             = new ClienteModel();
        $this->vendedor_model                            = new VendedorModel();
        $this->tecnico_model                             = new TecnicoModel();

        $this->ordem_de_servico_model                    = new OrdemDeServicoModel();
        $this->pagamento_os_model                        = new PagamentoOsModel();
        $this->parcelas_do_pagamento_os_model            = new ParcelasDoPagamentoOsModel();
        $this->servico_mao_de_obra_os_model              = new ServicoMaoDeObraOsModel();
        $this->equipamento_os_model                      = new EquipamentoOsModel();
        $this->produto_peca_os_model                     = new ProdutoPecaOsModel();
        $this->venda_model                               = new VendaModel();
        $this->produto_da_venda_model                    = new ProdutoDaVendaModel();
    }

    public function index()
    {
        $this->links['subItem'] = "2.6";

        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Ordens de Serviço',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Ordens de Serviço", 'rota'   => "", 'active' => true]
        ];

        $data['ordens_de_servicos'] = $this->ordem_de_servico_model
            ->whereIn('situacao', ['Concretizada', 'Cancelada'])
            ->orderBy('id_ordem', 'DESC')
            ->limit(15)
            ->join('clientes', 'ordens_de_servicos.id_cliente = clientes.id_cliente')
            ->findAll();

        $data['situacoes_alteracao'] = ['Em aberto', 'Em andamento'];
        $data['titulo_lista'] = '15 últimas ordens de serviços cadastradas';
        $data['exibe_botao_novo_orcamento'] = false;

        echo view('templates/header');
        echo view('ordem_de_servico/index', $data);
        echo view('templates/footer');
    }

    public function orcamentos()
    {
        $this->links['subItem'] = "2.7";

        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Orçamentos',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Orçamentos", 'rota'   => "", 'active' => true]
        ];

        $data['ordens_de_servicos'] = $this->ordem_de_servico_model
            ->whereIn('situacao', ['Em aberto', 'Em andamento', 'Aberto'])
            ->orderBy('id_ordem', 'DESC')
            ->limit(15)
            ->join('clientes', 'ordens_de_servicos.id_cliente = clientes.id_cliente')
            ->findAll();

        $data['situacoes_alteracao'] = ['Concretizada', 'Cancelada'];
        $data['titulo_lista'] = '15 últimos orçamentos cadastrados';
        $data['exibe_botao_novo_orcamento'] = true;

        echo view('templates/header');
        echo view('ordem_de_servico/index', $data);
        echo view('templates/footer');
    }

    public function alteraSituacaoDaOrdemDeServicos()
    {
        $dados = $this->request->getvar();
        $situacao = $dados['situacao'] ?? '';

        if(!in_array($situacao, ['Em aberto', 'Em andamento', 'Concretizada', 'Cancelada']))
        {
            return redirect()->to('/ordensDeServicos');
        }

        $this->ordem_de_servico_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_altera_situacao_ordem_de_servivo');

        return redirect()->to($this->rotaListagemDaSituacao($situacao));
    }

    private function rotaListagemDaSituacao($situacao)
    {
        if(in_array($situacao, ['Concretizada', 'Cancelada']))
        {
            return '/ordensDeServicos';
        }

        return '/ordensDeServicos/orcamentos';
    }

    public function create()
    {
        $this->links['subItem'] = "2.7";

        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Orçamento',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Orçamentos", 'rota' => "/ordensDeServicos/orcamentos", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];


        if(empty($this->ordem_de_servico_provisorio_model->findAll())) // Verifica se está vazia e cria uma ordem de serviço provisoria
        {
            $this->ordem_de_servico_provisorio_model->insert([
                'id_ordem'             => 1,
                'situacao'             => "Em aberto",
                'data_de_entrada'      => date('Y-m-d'),
                'hora_de_entrada'      => date('H:i:s'),
                'canal_de_venda'       => "Presencial",
                'centro_de_custo'      => "",
                'frete'                => 0,
                'outros'               => 0,
                'desconto'             => 0,
                'id_cliente'           => 1,
                'id_vendedor'          => $this->vendedor_model->idGeral(),
                'id_tecnico'           => 1,
                'observacoes'          => "",
                'observacoes_internas' => ""
            ]);

            $this->pagamento_os_provisorio_model->insert([
                'id_pagamento' => 1,
                'tipo'         => "À Vista",
                'id_ordem'     => 1
            ]);

            $this->parcelas_do_pagamento_os_provisorio_model->insert([
                'id_parcela'         => 1,
                'data_de_vencimento' => date('Y-m-d'),
                'valor_da_parcela'   => 0,
                'forma_de_pagamento' => "Dinheiro",
                'observacoes'        => "",
                'id_pagamento'       => 1
            ]);
        }

        $this->produto_peca_os_provisorio_model->emptyTable('produtos_pecas_os_provisorio');

        $os = $this->ordem_de_servico_provisorio_model->where('id_ordem', 1)->first();

        $data['id_ordem'] = 1; // Como é a ordem de serviço provisória será 1.
        
        $data['dados_ordem_de_servico']             = $os;
        $data['equipamentos_os_provisorio']         = $this->equipamento_os_provisorio_model->findAll();
        $data['produtos_os_provisorio']             = $this->produto_peca_os_provisorio_model->findAll();
        $data['servicos_mao_de_obra_os_provisorio'] = $this->servico_mao_de_obra_provisorio_model->findAll();
        $data['pagamento_os']                       = $this->pagamento_os_provisorio_model->first();
        $data['parcelas_pagamento_os']              = $this->parcelas_do_pagamento_os_provisorio_model->findAll();

        $data['produtos']             = $this->produto_model->findAll();
        $data['servicos_mao_de_obra'] = $this->servico_mao_de_obra_model->findAll();

        $data['formas_de_pagamento'] = $this->forma_de_pagamento_model->findAll();
        $data['clientes']            = $this->cliente_model->findAll();
        $data['vendedores']          = $this->vendedor_model->paraVenda();
        $data['tecnicos']            = $this->tecnico_model->findAll();

        echo view('templates/header');
        echo view('ordem_de_servico/form', $data);
        echo view('templates/footer');
    }

    public function show($id_ordem)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados da Ordem de Serviço',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Ordens De Serviços", 'rota' => "/ordensDeServicos", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        $os = $this->ordem_de_servico_model->select('clientes.nome AS nome_do_cliente, situacao, data_de_entrada, hora_de_entrada, data_de_saida, hora_de_saida, canal_de_venda, centro_de_custo, frete, outros, desconto, ordens_de_servicos.observacoes AS observacoes, observacoes_internas, vendedores.nome AS nome_do_vendedor, tecnicos.nome AS nome_do_tecnico')->where('id_ordem', $id_ordem)->join('clientes', 'ordens_de_servicos.id_cliente = clientes.id_cliente')->join('vendedores', 'ordens_de_servicos.id_vendedor = vendedores.id_vendedor')->join('tecnicos', 'ordens_de_servicos.id_tecnico = tecnicos.id_tecnico')->first();

        $data['id_ordem'] = $id_ordem; // Como é a ordem de serviço provisória será 1.
        
        $data['dados_ordem_de_servico']             = $os;
        $data['equipamentos_os_provisorio']         = $this->equipamento_os_model->where('id_ordem', $id_ordem)->findAll();
        $data['produtos_os_provisorio']             = $this->produto_peca_os_model->where('id_ordem', $id_ordem)->findAll();
        $data['servicos_mao_de_obra_os_provisorio'] = $this->servico_mao_de_obra_os_model->where('id_ordem', $id_ordem)->findAll();
        $data['pagamento_os']                       = $this->pagamento_os_model->where('id_ordem', $id_ordem)->first();
        $data['parcelas_pagamento_os']              = $this->parcelas_do_pagamento_os_model->where('id_pagamento', $data['pagamento_os']['id_pagamento'])->findAll();

        $data['produtos']             = $this->produto_model->findAll();
        $data['servicos_mao_de_obra'] = $this->servico_mao_de_obra_model->findAll();

        $data['formas_de_pagamento'] = $this->forma_de_pagamento_model->findAll();
        $data['clientes']            = $this->cliente_model->findAll();
        $data['vendedores']          = $this->vendedor_model->visiveis();
        $data['tecnicos']            = $this->tecnico_model->findAll();

        echo view('templates/header');
        echo view('ordem_de_servico/show', $data);
        echo view('templates/footer');
    }

    public function edit($id_ordem)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados da Ordem de Serviço',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Ordens De Serviços", 'rota' => "/ordensDeServicos", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        $data['acao_user'] = "edit"; // Informando que a ação do usuario é editar

        $os = $this->ordem_de_servico_model->where('id_ordem', $id_ordem)->first();

        $data['id_ordem'] = $id_ordem; // Como é a ordem de serviço provisória será 1.
        
        $data['dados_ordem_de_servico']             = $os;
        $data['equipamentos_os_provisorio']         = $this->equipamento_os_model->where('id_ordem', $id_ordem)->findAll();
        $data['produtos_os_provisorio']             = $this->produto_peca_os_model->where('id_ordem', $id_ordem)->findAll();
        $data['servicos_mao_de_obra_os_provisorio'] = $this->servico_mao_de_obra_os_model->where('id_ordem', $id_ordem)->findAll();
        $data['pagamento_os']                       = $this->pagamento_os_model->where('id_ordem', $id_ordem)->first();
        $data['parcelas_pagamento_os']              = $this->parcelas_do_pagamento_os_model->where('id_pagamento', $data['pagamento_os']['id_pagamento'])->findAll();

        $data['produtos']             = $this->produto_model->findAll();
        $data['servicos_mao_de_obra'] = $this->servico_mao_de_obra_model->findAll();

        $data['formas_de_pagamento'] = $this->forma_de_pagamento_model->findAll();
        $data['clientes']            = $this->cliente_model->findAll();
        $data['vendedores']          = $this->vendedor_model->visiveis();
        $data['tecnicos']            = $this->tecnico_model->findAll();

        echo view('templates/header');
        echo view('ordem_de_servico/form', $data);
        echo view('templates/footer');
    }

    // ------------------------------------------------- EQUIPAMENTOS ----------------------------------------------------- //
    // ------ AÇÃO CREATE ------ //
    public function addEquipamento()
    {
        $dados = $this->request->getvar();
        $dados['id_ordem'] = 1; // Para OS Provisório o id é 1

        $this->equipamento_os_provisorio_model->insert($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_add_equipamento');

        return redirect()->to('/ordensDeServicos/create/#table-equipamentos'); // Redireciona e foca na div 'table-equipamentos'
    }

    public function deleteEquipamento($id_equipamento)
    {
        $this->equipamento_os_provisorio_model->where('id_equipamento', $id_equipamento)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete_equipamento');

        return redirect()->to('/ordensDeServicos/create/#table-equipamentos'); // Redireciona e foca na div 'table-equipamentos'
    }

    // ------ AÇÃO EDIT ------ //
    public function addEquipamentoEdit()
    {
        $dados = $this->request->getvar();

        $this->equipamento_os_model->insert($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_add_equipamento');

        return redirect()->to("/ordensDeServicos/edit/{$dados['id_ordem']}/#table-equipamentos"); // Redireciona e foca na div 'table-equipamentos'
    }

    public function deleteEquipamentoEdit($id_equipamento, $id_ordem)
    {
        $this->equipamento_os_model->where('id_equipamento', $id_equipamento)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete_equipamento');

        return redirect()->to("/ordensDeServicos/edit/$id_ordem/#table-equipamentos"); // Redireciona e foca na div 'table-equipamentos'
    }

    // ------------------------------------------------- PRODUTO/PEÇAS ----------------------------------------------------- //
    // ------ CREATE ------- //
    public function addProduto()
    {
        $id_produto = $this->request->getvar('id_produto');

        $produto = $this->produto_model->where('id_produto', $id_produto)->first();

        $dados = [
            'id_produto_estoque' => $produto['id_produto'],
            'nome'               => $produto['nome'],
            'quantidade'     => 1,
            'valor_unitario' => $produto['valor_de_venda'],
            'desconto'       => 0,
            'id_ordem'       => 1 // Para OS Provisório o id é 1
        ];

        $this->produto_peca_os_provisorio_model->insert($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_add_produto_peca');

        return redirect()->to('/ordensDeServicos/create/#table-produto-peca'); // Redireciona e foca na div 'table-equipamentos'
    }

    public function deleteProduto($id_produto)
    {
        $this->produto_peca_os_provisorio_model->where('id_produto', $id_produto)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete_produto');

        return redirect()->to('/ordensDeServicos/create/#table-produto-peca'); // Redireciona e foca na div 'table-equipamentos'
    }

    public function alteraDadosProdutoPeca()
    {
        $dados = $this->request->getvar();

        $this->produto_peca_os_provisorio_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_altera_dados_produto_peca');

        return redirect()->to('/ordensDeServicos/create/#table-produto-peca'); // Redireciona e foca na div 'table-equipamentos'
    }

    // ------ EDIT ------ //
    public function addProdutoEdit()
    {
        $id_produto = $this->request->getvar('id_produto');
        $id_ordem = $this->request->getvar('id_ordem');

        $produto = $this->produto_model->where('id_produto', $id_produto)->first();

        $dados = [
            'id_produto_estoque' => $produto['id_produto'],
            'nome'               => $produto['nome'],
            'quantidade'     => 1,
            'valor_unitario' => $produto['valor_de_venda'],
            'desconto'       => 0,
            'id_ordem'       => $id_ordem // Para OS Provisório o id é 1
        ];

        $this->produto_peca_os_model->insert($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_add_produto_peca');

        return redirect()->to("/ordensDeServicos/edit/$id_ordem/#table-produto-peca"); // Redireciona e foca na div 'table-equipamentos'
    }

    public function deleteProdutoEdit($id_produto, $id_ordem)
    {
        $this->produto_peca_os_model->where('id_produto', $id_produto)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete_produto');

        return redirect()->to("/ordensDeServicos/edit/$id_ordem/#table-produto-peca"); // Redireciona e foca na div 'table-equipamentos'
    }

    public function alteraDadosProdutoPecaEdit()
    {
        $dados = $this->request->getvar();

        $this->produto_peca_os_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_altera_dados_produto_peca');

        return redirect()->to("/ordensDeServicos/edit/{$dados['id_ordem']}/#table-produto-peca"); // Redireciona e foca na div 'table-equipamentos'
    }

    // ------------------------------------------------- SERVIÇO MÃO DE OBRA ----------------------------------------------------- //
    // ----- CREATE ----- //
    public function addServicoMaoDeObra()
    {
        $id_servico = $this->request->getvar('id_servico');

        $servico = $this->servico_mao_de_obra_model->where('id_servico', $id_servico)->first();

        $dados = [
            'nome'           => $servico['nome'],
            'descricao'      => $servico['descricao'],
            'quantidade'     => 1,
            'valor'          => $servico['valor'],
            'desconto'       => 0,
            'id_ordem'       => 1 // Para OS Provisório o id é 1
        ];

        $this->servico_mao_de_obra_provisorio_model->insert($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_add_servico_mao_de_obra');

        return redirect()->to('/ordensDeServicos/create/#table-servico-mao-de-obra'); // Redireciona e foca na div 'table-servico-mao-de-bra'
    }

    public function deleteServicoMaoDeObra($id_servico)
    {
        $this->servico_mao_de_obra_provisorio_model->where('id_servico', $id_servico)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete_servico_mao_de_obra');

        return redirect()->to('/ordensDeServicos/create/#table-servico-mao-de-obra'); // Redireciona e foca na div 'table-servico-mao-de-bra'
    }

    public function alteraDadosServicoMaoDeObra()
    {
        $dados = $this->request->getvar();

        $this->servico_mao_de_obra_provisorio_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_atualiza_dados_servico_mao_de_obra');

        return redirect()->to("/ordensDeServicos/create/#table-servico-mao-de-obra"); // Redireciona e foca na div 'table-servico-mao-de-bra'
    }

    // ----- EDIT ----- //
    public function addServicoMaoDeObraEdit()
    {
        $id_servico = $this->request->getvar('id_servico');
        $id_ordem = $this->request->getvar('id_ordem');

        $servico = $this->servico_mao_de_obra_model->where('id_servico', $id_servico)->first();

        $dados = [
            'nome'           => $servico['nome'],
            'descricao'      => $servico['descricao'],
            'quantidade'     => 1,
            'valor'          => $servico['valor'],
            'desconto'       => 0,
            'id_ordem'       => $id_ordem
        ];

        $this->servico_mao_de_obra_os_model->insert($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_add_servico_mao_de_obra');

        return redirect()->to("/ordensDeServicos/edit/$id_ordem/#table-servico-mao-de-obra"); // Redireciona e foca na div 'table-servico-mao-de-bra'
    }

    public function deleteServicoMaoDeObraEdit($id_servico, $id_ordem)
    {
        $this->servico_mao_de_obra_os_model->where('id_servico', $id_servico)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete_servico_mao_de_obra');

        return redirect()->to("/ordensDeServicos/edit/$id_ordem/#table-servico-mao-de-obra"); // Redireciona e foca na div 'table-servico-mao-de-bra'
    }

    public function alteraDadosServicoMaoDeObraEdit()
    {
        $dados = $this->request->getvar();

        $this->servico_mao_de_obra_os_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_atualiza_dados_servico_mao_de_obra');

        return redirect()->to("/ordensDeServicos/edit/{$dados['id_ordem']}/#table-servico-mao-de-obra"); // Redireciona e foca na div 'table-servico-mao-de-bra'
    }

    // ------------------------------------------------- TOTAL ----------------------------------------------------- //
    // ----- CREATE ----- //
    public function alteraTotal()
    {
        $dados = $this->normalizaTotaisDaOrdem($this->request->getvar());
        $dados['id_ordem'] = 1;

        $this->ordem_de_servico_provisorio_model->save($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_total_salvo');

        return redirect()->to('/ordensDeServicos/create/#dados-do-total'); // Redireciona e foca na div 'dados-do-total'
    }

    // ----- EDIT ----- //
    public function alteraTotalEdit()
    {
        $dados = $this->normalizaTotaisDaOrdem($this->request->getvar());

        $this->ordem_de_servico_model->save($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_total_salvo');

        return redirect()->to("/ordensDeServicos/edit/{$dados['id_ordem']}/#dados-do-total"); // Redireciona e foca na div 'dados-do-total'
    }


    // ------------------------------------------------- PAGAMENTO ----------------------------------------------------- //
    // ----- CREATE ----- //
    public function calculaPagamentoAVista()
    {
        $valor_total_do_pagamento = $this->request->getvar('valor_total_do_pagamento');

        // Remove todos os registros da tabela parcelas_do_pagamento_os_provisorio
        $this->parcelas_do_pagamento_os_provisorio_model->emptyTable('parcelas_do_pagamento_os_provisorio');

        $this->parcelas_do_pagamento_os_provisorio_model->insert([
            'data_de_vencimento' => date('Y-m-d'),
            'valor_da_parcela'   => $valor_total_do_pagamento,
            'forma_de_pagamento' => "Dinheiro",
            'observacoes'        => "",
            'id_pagamento'       => 1
        ]);

        // Altera o tipo para à vista
        $this->pagamento_os_provisorio_model->save([
            'id_pagamento' => 1,
            'tipo'         => "À Vista"
        ]);

        // Prepara retorno
        $session = session();
        $session->setFlashdata('alert', 'success_pagamento_a_vista_os');

        return redirect()->to('/ordensDeServicos/create/#pagamento_os');
    }

    public function calculaParcelasOs()
    {
        $dados = $this->request->getvar();

        // Remove todos os registros da tabela parcelas_do_pagamento_os_provisorio
        $this->parcelas_do_pagamento_os_provisorio_model->emptyTable('parcelas_do_pagamento_os_provisorio');

        $forma_de_pagamento       = $dados['forma_de_pagamento'];
        $intervalo_parcelas       = $dados['intervalo_parcelas'];
        $quantidade_de_parcelas   = $dados['quantidade_de_parcelas'];
        $data_primeira_parcela    = $dados['data_primeira_parcela'];
        $valor_total_dos_servicos = $dados['valor_total_dos_servicos'];

        // Verifica se o usuário escolheu a data da primeira parcela. Se não, coloca a data atual
        if($data_primeira_parcela == "")
        {
            $data_primeira_parcela = date('Y-m-d');
        }

        $valor_da_parcela = ($valor_total_dos_servicos / $quantidade_de_parcelas);

        $guarda_data = $data_primeira_parcela;

        for($i=0; $i<$quantidade_de_parcelas; $i++)
        {
            $this->parcelas_do_pagamento_os_provisorio_model->insert([
                'data_de_vencimento' => $data_primeira_parcela,
                'valor_da_parcela'   => $valor_da_parcela,
                'forma_de_pagamento' => $forma_de_pagamento,
                'observacoes'        => "",
                'id_pagamento'       => 1
            ]);

            $data_primeira_parcela = date('Y-m-d', strtotime("+$intervalo_parcelas days",strtotime($guarda_data)));
            $guarda_data = $data_primeira_parcela;
        }

        // Altera o tipo para parcelado
        $this->pagamento_os_provisorio_model->save([
            'id_pagamento' => 1,
            'tipo'         => "Parcelado"
        ]);
        
        // Prepara retorno
        $session = session();
        $session->setFlashdata('alert', 'success_parcelamento_os');

        return redirect()->to('/ordensDeServicos/create/#pagamento_os');
    }

    public function alteraDadosDaParcela()
    {
        $dados = $this->request->getvar();

        $this->parcelas_do_pagamento_os_provisorio_model->save($dados);

        // Prepara retorno
        $session = session();
        $session->setFlashdata('alert', 'success_atualiza_dados_da_parcela_do_pagamento_os');

        return redirect()->to('/ordensDeServicos/create/#pagamento_os');
    }

    // ----- EDIT ----- //
    public function calculaPagamentoAVistaEdit()
    {
        $valor_total_do_pagamento = $this->request->getvar('valor_total_do_pagamento');
        $id_ordem = $this->request->getvar('id_ordem');

        // Remove todos os registros da tabela parcelas_do_pagamento_os_provisorio
        $this->parcelas_do_pagamento_os_model->emptyTable('parcelas_do_pagamento_os');

        // Pega o pagamento da ordem de servico
        $pagamento = $this->pagamento_os_model->where('id_ordem', $id_ordem)->first();

        $this->parcelas_do_pagamento_os_model->insert([
            'data_de_vencimento' => date('Y-m-d'),
            'valor_da_parcela'   => $valor_total_do_pagamento,
            'forma_de_pagamento' => "Dinheiro",
            'observacoes'        => "",
            'id_pagamento'       => $pagamento['id_pagamento']
        ]);

        // Altera o tipo para à vista
        $this->pagamento_os_model->save([
            'id_pagamento' => $pagamento['id_pagamento'],
            'tipo'         => "À Vista"
        ]);

        // Prepara retorno
        $session = session();
        $session->setFlashdata('alert', 'success_pagamento_a_vista_os');

        return redirect()->to("/ordensDeServicos/edit/$id_ordem/#pagamento_os");
    }

    public function calculaParcelasOsEdit()
    {
        $dados = $this->request->getvar();

        // Remove todos os registros da tabela parcelas_do_pagamento_os_provisorio
        $this->parcelas_do_pagamento_os_model->emptyTable('parcelas_do_pagamento_os_provisorio');

        $forma_de_pagamento       = $dados['forma_de_pagamento'];
        $intervalo_parcelas       = $dados['intervalo_parcelas'];
        $quantidade_de_parcelas   = $dados['quantidade_de_parcelas'];
        $data_primeira_parcela    = $dados['data_primeira_parcela'];
        $valor_total_dos_servicos = $dados['valor_total_dos_servicos'];

        // Verifica se o usuário escolheu a data da primeira parcela. Se não, coloca a data atual
        if($data_primeira_parcela == "")
        {
            $data_primeira_parcela = date('Y-m-d');
        }

        $valor_da_parcela = ($valor_total_dos_servicos / $quantidade_de_parcelas);

        // Guarda a data da primeira parcela para poder adicionar a quantidade de dias que o usuário escolher
        $guarda_data = $data_primeira_parcela;

        // Pega os dados do Pagamento
        $pagamento = $this->pagamento_os_model->where('id_ordem', $dados['id_ordem'])->first();

        for($i=0; $i<$quantidade_de_parcelas; $i++)
        {
            $this->parcelas_do_pagamento_os_model->insert([
                'data_de_vencimento' => $data_primeira_parcela,
                'valor_da_parcela'   => $valor_da_parcela,
                'forma_de_pagamento' => $forma_de_pagamento,
                'observacoes'        => "",
                'id_pagamento'       => $pagamento['id_pagamento']
            ]);

            $data_primeira_parcela = date('Y-m-d', strtotime("+$intervalo_parcelas days",strtotime($guarda_data)));
            $guarda_data = $data_primeira_parcela;
        }

        // Altera o tipo para parcelado
        $this->pagamento_os_model->save([
            'id_pagamento' => $pagamento['id_pagamento'],
            'tipo'         => "Parcelado"
        ]);
        
        // Prepara retorno
        $session = session();
        $session->setFlashdata('alert', 'success_parcelamento_os');

        return redirect()->to("/ordensDeServicos/edit/{$dados['id_ordem']}/#pagamento_os");
    }


    // ------------------------------------------------- FINALIZA VENDA ----------------------------------------------------- //
    public function finalizaOrdemDeServico()
    {
        $dados_da_ordem_de_servicos = $this->normalizaTotaisDaOrdem($this->request->getvar());
        $this->produto_peca_os_provisorio_model->emptyTable('produtos_pecas_os_provisorio');

        $db = db_connect();
        $db->transBegin();

        try
        {

        // Insere os dados da ordem no banco e retorna seu id_ordem para inserir nos demais registros
        $id_ordem = $this->ordem_de_servico_model->insert($dados_da_ordem_de_servicos);

        // ------------------------------- PAGAMENTO -------------------------- //
        // Pega o pagamento provisório
        $pagamento = $this->pagamento_os_provisorio_model->where('id_pagamento', 1)->first();
        unset($pagamento['id_pagamento']); // Remove o id_pagamento para inserir o definitivo
        $pagamento['id_ordem'] = $id_ordem;
        $pagamento['tipo'] = "À Vista";

        // Insere o pagamento na tabela definitiva
        $id_pagamento = $this->pagamento_os_model->insert($pagamento);

        $this->parcelas_do_pagamento_os_model->insert([
            'data_de_vencimento' => date('Y-m-d'),
            'valor_da_parcela'   => $this->valorTotalDaOrdem($dados_da_ordem_de_servicos),
            'forma_de_pagamento' => "Dinheiro",
            'observacoes'        => "",
            'id_pagamento'       => $id_pagamento
        ]);
        // ------------------------------------------------------------------- //


        // ------------------------------- SERVIÇOS MÃO DE OBRA -------------------------- //
        $servicos_mao_de_bra_peovisorio = $this->servico_mao_de_obra_provisorio_model->findAll();

        foreach($servicos_mao_de_bra_peovisorio as $servico)
        {
            unset($servico['id_servico']); // Remove o id_servico para ser inserido o novo definitivo
            unset($servico['created_at']); // Remove o created_at para ser inserido o novo definitivo
            unset($servico['updated_at']); // Remove o updated_at para ser inserido o novo definitivo
            unset($servico['deleted_at']); // Remove o deleted_at para ser inserido o novo definitivo

            $servico['id_ordem'] = $id_ordem; // Altera o id_ordem para o definitivo
         
            $this->servico_mao_de_obra_os_model->insert($servico);
        }
        // ----------------------------------------------------------------------------- //


        // ---------------------------------- EQUIPAMENTOS ----------------------------- //
        $equipamentos_provisorio = $this->equipamento_os_provisorio_model->findAll();

        foreach($equipamentos_provisorio as $equipamento)
        {
            unset($equipamento['id_equipamento']); // Remove o id_equipamento para ser inserido o novo definitivo
            unset($equipamento['created_at']); // Remove o created_at para ser inserido o novo definitivo
            unset($equipamento['updated_at']); // Remove o updated_at para ser inserido o novo definitivo
            unset($equipamento['deleted_at']); // Remove o deleted_at para ser inserido o novo definitivo

            $equipamento['id_ordem'] = $id_ordem; // Altera o id_ordem para o definitivo
         
            $this->equipamento_os_model->insert($equipamento);
        }
        // ------------------------------------------------------------------------------- //
        

        $this->ordem_de_servico_provisorio_model->emptyTable('ordens_de_servicos_provisorio');

        if(!$db->transStatus())
        {
            throw new \RuntimeException('Falha ao salvar a ordem de servico.');
        }

        $db->transCommit();

        $session = session();
        $session->setFlashdata('alert', 'success_finaliza_ordem_de_servico');

        return redirect()->to($this->rotaListagemDaSituacao($dados_da_ordem_de_servicos['situacao']));
        }
        catch(\Throwable $exception)
        {
            $db->transRollback();

            log_message('error', 'Erro ao finalizar OS: ' . $exception->getMessage());

            $session = session();
            $session->setFlashdata('alert', 'error_finaliza_os');

            return redirect()->to('/ordensDeServicos/create');
        }
    }

    private function valorTotalDaOrdem(array $ordem): float
    {
        $servicos = $this->servico_mao_de_obra_provisorio_model->findAll();
        $total_servicos = 0;

        foreach($servicos as $servico)
        {
            $total_servicos += ($this->valorNumerico($servico['quantidade'] ?? 0) * $this->valorNumerico($servico['valor'] ?? 0)) - $this->valorNumerico($servico['desconto'] ?? 0);
        }

        $total = $total_servicos
            + $this->valorNumerico($ordem['frete'] ?? 0)
            + $this->valorNumerico($ordem['outros'] ?? 0)
            - $this->valorNumerico($ordem['desconto'] ?? 0);

        return round($total, 2);
    }


    // -------------------------------------------------------- EDITAR DADOS DOS RESPONSÁVEIS E DADOS FINAIS DA ORDEM DE SERVIÇO ---------------------------------------
    private function registrarVendaDosProdutosDaOs(array $ordem, array $produtos_e_pecas, int $id_caixa): void
    {
        $produtos_da_venda = [];
        $valor_a_pagar = 0;
        $desconto_total = 0;

        foreach($produtos_e_pecas as $produto_peca)
        {
            $produto_estoque = $this->produtoEstoqueDaPecaOs($produto_peca);
            $quantidade = (int) $this->valorNumerico($produto_peca['quantidade'] ?? 0);
            $valor_unitario = $this->valorNumerico($produto_peca['valor_unitario'] ?? 0);
            $desconto = $this->valorNumerico($produto_peca['desconto'] ?? 0);
            $subtotal = $quantidade * $valor_unitario;
            $valor_final = $subtotal - $desconto;

            if($quantidade <= 0 || $valor_final <= 0)
            {
                continue;
            }

            $valor_a_pagar += $valor_final;
            $desconto_total += $desconto;

            $produtos_da_venda[] = [
                'produto_estoque' => $produto_estoque,
                'quantidade'      => $quantidade,
                'dados'           => [
                    'nome'             => $produto_peca['nome'] ?? $produto_estoque['nome'],
                    'unidade'          => $produto_estoque['unidade'] ?? '',
                    'codigo_de_barras' => $produto_estoque['codigo_de_barras'] ?? '',
                    'quantidade'       => $quantidade,
                    'valor_unitario'   => $valor_unitario,
                    'subtotal'         => $subtotal,
                    'desconto'         => $desconto,
                    'valor_final'      => $valor_final,
                    'NCM'              => $produto_estoque['NCM'] ?? '',
                    'CSOSN'            => $produto_estoque['CSOSN'] ?? '',
                    'CFOP'             => $produto_estoque['CFOP'] ?? '',
                    'id_produto'       => $produto_estoque['id_produto']
                ]
            ];
        }

        if(empty($produtos_da_venda))
        {
            return;
        }

        $id_venda = $this->venda_model->insert([
            'valor_a_pagar'      => $valor_a_pagar,
            'desconto'           => $desconto_total,
            'valor_recebido'     => $valor_a_pagar,
            'troco'              => 0,
            'forma_de_pagamento' => $this->formaDePagamentoVendaOs(),
            'data'               => $this->dataVendaOs($ordem),
            'hora'               => $this->horaVendaOs($ordem),
            'id_cliente'         => $ordem['id_cliente'] ?? 1,
            'id_vendedor'        => $ordem['id_vendedor'] ?? $this->vendedor_model->idGeral(),
            'id_caixa'           => $id_caixa
        ]);

        if(!$id_venda)
        {
            throw new \RuntimeException('Nao foi possivel registrar a venda dos produtos da OS.');
        }

        foreach($produtos_da_venda as $produto_da_venda)
        {
            $dados_produto = $produto_da_venda['dados'];
            $dados_produto['id_venda'] = $id_venda;

            if(!$this->produto_da_venda_model->insert($dados_produto))
            {
                throw new \RuntimeException('Nao foi possivel registrar um produto da venda da OS.');
            }

            $produto_estoque = $produto_da_venda['produto_estoque'];
            $nova_quantidade = $this->valorNumerico($produto_estoque['quantidade'] ?? 0) - $produto_da_venda['quantidade'];

            $this->produto_model
                ->set('quantidade', $nova_quantidade)
                ->where('id_produto', $produto_estoque['id_produto'])
                ->update();
        }
    }

    private function produtoEstoqueDaPecaOs(array $produto_peca): array
    {
        $id_produto_estoque = (int) ($produto_peca['id_produto_estoque'] ?? 0);

        if($id_produto_estoque > 0)
        {
            $produto = $this->produto_model->where('id_produto', $id_produto_estoque)->first();

            if(!empty($produto))
            {
                return $produto;
            }
        }

        $nome = trim((string) ($produto_peca['nome'] ?? ''));

        if($nome !== '')
        {
            $produto = $this->produto_model->where('nome', $nome)->orderBy('id_produto', 'ASC')->first();

            if(!empty($produto))
            {
                return $produto;
            }
        }

        throw new \RuntimeException('Produto/peca da OS sem vinculo com estoque: ' . $nome);
    }

    private function formaDePagamentoVendaOs(): string
    {
        $parcela = $this->parcelas_do_pagamento_os_provisorio_model->orderBy('id_parcela', 'ASC')->first();
        $forma_de_pagamento = $parcela['forma_de_pagamento'] ?? '';

        return $forma_de_pagamento !== '' ? $forma_de_pagamento : 'Dinheiro';
    }

    private function dataVendaOs(array $ordem): string
    {
        foreach(['data_de_saida', 'data_de_entrada'] as $campo)
        {
            $data = $ordem[$campo] ?? '';

            if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) && $data !== '0000-00-00')
            {
                return $data;
            }
        }

        return date('Y-m-d');
    }

    private function horaVendaOs(array $ordem): string
    {
        foreach(['hora_de_saida', 'hora_de_entrada'] as $campo)
        {
            $hora = $ordem[$campo] ?? '';

            if(preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $hora) && $hora !== '00:00:00')
            {
                return strlen($hora) === 5 ? $hora . ':00' : $hora;
            }
        }

        return date('H:i:s');
    }

    private function valorNumerico($valor): float
    {
        if(is_numeric($valor))
        {
            return (float) $valor;
        }

        $valor = trim((string) $valor);

        if($valor === '')
        {
            return 0;
        }

        if(strpos($valor, ',') !== false)
        {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }

        return (float) $valor;
    }

    private function normalizaTotaisDaOrdem(array $dados): array
    {
        foreach(['frete', 'outros', 'desconto'] as $campo)
        {
            if(isset($dados[$campo]))
            {
                $dados[$campo] = round($this->valorNumerico($dados[$campo]), 2);
            }
        }

        return $dados;
    }

    public function editDadosResponsaveis_e_DadosFinaisOrdemDeServico()
    {
        $dados = $this->normalizaTotaisDaOrdem($this->request->getvar());

        $this->ordem_de_servico_model->save($dados); // Altera os dados

        $session = session();
        $session->setFlashdata('alert', 'success_edit_ordem_de_servico');

        return redirect()->to("/ordensDeServicos/edit/{$dados['id_ordem']}");
    }

    public function delete($id_ordem) // Deleta a ordem de serviço
    {
        $ordem = $this->ordem_de_servico_model->where('id_ordem', $id_ordem)->first();
        $rota = !empty($ordem) ? $this->rotaListagemDaSituacao($ordem['situacao']) : '/ordensDeServicos';

        $this->ordem_de_servico_model->where('id_ordem', $id_ordem)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete_ordem_de_servico');

        return redirect()->to($rota);
    }
}
