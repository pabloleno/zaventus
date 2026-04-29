<?php

namespace App\Controllers;
// namespace App\Models;

use App\Models\PagamentoOsProvisorioModel;
use App\Models\ParcelasDoPagamentoOsProvisorioModel;
use App\Models\OrdemDeServicoProvisorioModel;
use App\Models\EquipamentoOsProvisorioModel;
use App\Models\ProdutoPecaOsProvisorioModel;
use App\Models\ProdutoModel;
use App\Models\ServicoMaoDeObraModel;
use App\Models\ServicoMaoDeObraProvisorioModel;
use App\Models\FormaDePagamentoModel;
use App\Models\ClienteModel;
use App\Models\VendedorModel;
use App\Models\TecnicoModel;

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

    function __construct()
    {
        $this->links = [
            'menu' => '2.m',
            'item' => '2.0',
            'subItem' => '2.5'
        ];

        $this->pagamento_os_provisorio_model             = new PagamentoOsProvisorioModel();
        $this->parcelas_do_pagamento_os_provisorio_model = new ParcelasDoPagamentoOsProvisorioModel();
        $this->ordem_de_servico_provisorio_model         = new OrdemDeServicoProvisorioModel();
        $this->equipamento_os_provisorio_model           = new EquipamentoOsProvisorioModel();
        $this->produto_peca_os_provisorio_model          = new ProdutoPecaOsProvisorioModel();
        $this->produto_model                             = new ProdutoModel();
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

        $data['ordens_de_servicos'] = $this->ordem_de_servico_model->orderBy('id_ordem', 'DESC')->limit(15)->join('clientes', 'ordens_de_servicos.id_cliente = clientes.id_cliente')->findAll();

        echo view('templates/header');
        echo view('ordem_de_servico/index', $data);
        echo view('templates/footer');
    }

    public function alteraSituacaoDaOrdemDeServicos()
    {
        $dados = $this->request->getvar();

        $this->ordem_de_servico_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_altera_situacao_ordem_de_servivo');

        return redirect()->to('/ordensDeServicos');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Ordem de Serviço',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Ordens De Serviços", 'rota' => "/ordensDeServicos", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];


        if(empty($this->ordem_de_servico_provisorio_model->findAll())) // Verifica se está vazia e cria uma ordem de serviço provisoria
        {
            $this->ordem_de_servico_provisorio_model->insert([
                'id_ordem'             => 1,
                'situacao'             => "Aberto",
                'data_de_entrada'      => date('Y-m-d'),
                'hora_de_entrada'      => date('H:i:s'),
                'canal_de_venda'       => "Presencial",
                'centro_de_custo'      => "",
                'frete'                => 0,
                'outros'               => 0,
                'desconto'             => 0,
                'id_cliente'           => 1,
                'id_vendedor'          => 1,
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
        $data['vendedores']          = $this->vendedor_model->findAll();
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
        $data['vendedores']          = $this->vendedor_model->findAll();
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
        $data['vendedores']          = $this->vendedor_model->findAll();
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
            'nome' => $produto['nome'],
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
            'nome' => $produto['nome'],
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
        $dados = $this->request->getvar();
        $dados['id_ordem'] = 1;

        $this->ordem_de_servico_provisorio_model->save($dados);
        
        $session = session();
        $session->setFlashdata('alert', 'success_total_salvo');

        return redirect()->to('/ordensDeServicos/create/#dados-do-total'); // Redireciona e foca na div 'dados-do-total'
    }

    // ----- EDIT ----- //
    public function alteraTotalEdit()
    {
        $dados = $this->request->getvar();

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
        $dados_da_ordem_de_servicos = $this->request->getvar();

        // Insere os dados da ordem no banco e retorna seu id_ordem para inserir nos demais registros
        $id_ordem = $this->ordem_de_servico_model->insert($dados_da_ordem_de_servicos);

        // ------------------------------- PAGAMENTO -------------------------- //
        // Pega o pagamento provisório
        $pagamento = $this->pagamento_os_provisorio_model->where('id_pagamento', 1)->first();
        unset($pagamento['id_pagamento']); // Remove o id_pagamento para inserir o definitivo
        $pagamento['id_ordem'] = $id_ordem;

        // Insere o pagamento na tabela definitiva
        $id_pagamento = $this->pagamento_os_model->insert($pagamento);

        // Pega as parcelas do pagamento da tabela provisória
        $parcelas = $this->parcelas_do_pagamento_os_provisorio_model->findAll();

        // Insere as parcelas na tabela 'parcelas_do_pagamento' definitiva
        foreach($parcelas as $parcela)
        {
            unset($parcela['id_parcela']); // Remove o id_parcela para ser inserido o novo definitivo
            unset($parcela['created_at']); // Remove o created_at para ser inserido o novo definitivo
            unset($parcela['updated_at']); // Remove o updated_at para ser inserido o novo definitivo
            unset($parcela['deleted_at']); // Remove o deleted_at para ser inserido o novo definitivo
            
            $parcela['id_pagamento'] = $id_pagamento; // Altera o id_pagamento para o novo da tabela definitiva
            
            $this->parcelas_do_pagamento_os_model->insert($parcela);
        }
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
        

        // ------------------------------------ PRODUTOS/PEÇAS ------------------------------- //
        $produtos_e_pecas_provisorio = $this->produto_peca_os_provisorio_model->findAll();

        foreach($produtos_e_pecas_provisorio as $produto_peca)
        {
            unset($produto_peca['id_produto']); // Remove o id_produto para ser inserido o novo definitivo
            unset($produto_peca['created_at']); // Remove o created_at para ser inserido o novo definitivo
            unset($produto_peca['updated_at']); // Remove o updated_at para ser inserido o novo definitivo
            unset($produto_peca['deleted_at']); // Remove o deleted_at para ser inserido o novo definitivo

            $produto_peca['id_ordem'] = $id_ordem; // Altera o id_ordem para o definitivo
         
            $this->produto_peca_os_model->insert($produto_peca);
        }
        // ------------------------------------------------------------------------------- //


        // Remove todos os registros da tabela ordens_de_servicos_provisorio assim, limpa todos os outros dados para poder criar outra ordem de serviço
        $this->ordem_de_servico_provisorio_model->emptyTable('ordens_de_servicos_provisorio');


        $session = session();
        $session->setFlashdata('alert', 'success_finaliza_ordem_de_servico');

        return redirect()->to('/ordensDeServicos');
    }


    // -------------------------------------------------------- EDITAR DADOS DOS RESPONSÁVEIS E DADOS FINAIS DA ORDEM DE SERVIÇO ---------------------------------------
    public function editDadosResponsaveis_e_DadosFinaisOrdemDeServico()
    {
        $dados = $this->request->getvar();

        $this->ordem_de_servico_model->save($dados); // Altera os dados

        $session = session();
        $session->setFlashdata('alert', 'success_edit_ordem_de_servico');

        return redirect()->to("/ordensDeServicos/edit/{$dados['id_ordem']}");
    }

    public function delete($id_ordem) // Deleta a ordem de serviço
    {
        $this->ordem_de_servico_model->where('id_ordem', $id_ordem)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete_ordem_de_servico');

        return redirect()->to('/ordensDeServicos');
    }
}