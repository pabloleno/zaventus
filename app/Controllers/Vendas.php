<?php

namespace App\Controllers;

use App\Models\ConfigEmpresaModel;
use App\Models\VendedorModel;
use App\Models\ClienteModel;
use App\Models\ConfigNFeNFCeModel;
use App\Models\NFeModel;
use App\Models\NFCeModel;
use App\Models\ProdutoDaVendaModel;
use App\Models\VendaModel;
use CodeIgniter\Controller;

class Vendas extends Controller
{
    private $links;

    private $config_empresa_model;
    private $vendedor_model;
    private $venda_model;
    private $produtos_da_venda;
    private $nfe_model;
    private $config_nfe_nfce_model;
    private $cliente_model;

    function __construct()
    {
        $this->links = [
            'menu' => '2.m',
            'item' => '2.0',
            'subItem' => '2.4'
        ];

        $this->config_empresa_model  = new ConfigEmpresaModel();
        $this->vendedor_model        = new VendedorModel();
        $this->venda_model           = new VendaModel();
        $this->produtos_da_venda     = new ProdutoDaVendaModel();
        $this->nfe_model             = new NFeModel();
        $this->nfce_model            = new NFCeModel();
        $this->config_nfe_nfce_model = new ConfigNFeNFCeModel();
        $this->cliente_model         = new ClienteModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Vendas',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Vendas", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $id_venda = $dados['id_venda'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            $id_cliente = $dados['id_cliente'];

            if($dados['id_venda'] != "") // Filtra somente pelo Cód do caixa
            {
                $vendas = $this->venda_model->join('clientes', 'vendas.id_cliente = clientes.id_cliente')->where('id_venda', $id_venda)->findAll();

                $data['id_venda'] = $id_venda;
            }
            else if($data_inicio != "" && $data_final != "" && $id_cliente != "")
            {
                if($id_cliente == "Todos")
                {
                    $vendas = $this->venda_model->join('clientes', 'vendas.id_cliente = clientes.id_cliente')->where('data >=', $data_inicio)->where('data <=', $data_final)->findAll();
                }
                else
                {
                    $vendas = $this->venda_model->join('clientes', 'vendas.id_cliente = clientes.id_cliente')->where('data >=', $data_inicio)->where('data <=', $data_final)->where('id_cliente', $id_cliente)->findAll();
                }

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;

                $data['id_cliente'] = $id_cliente;
            }
            else if($dados['data_inicio'] != "" && $dados['data_final'] != "") // Filtra somente pela data inicial e data final, quando não tem status definido
            {
                $vendas = $this->venda_model->join('clientes', 'vendas.id_cliente = clientes.id_cliente')->where('data >=', $data_inicio)->where('data <=', $data_final)->find();
                
                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else if($id_cliente != "")
            {
                if($id_cliente == "Todos")
                {
                    $vendas = $this->venda_model->join('clientes', 'vendas.id_cliente = clientes.id_cliente')->findAll();
                }
                else
                {
                    $vendas = $this->venda_model->join('clientes', 'vendas.id_cliente = clientes.id_cliente')->where('id_cliente', $id_cliente)->findAll();
                }

                $data['id_cliente'] = $id_cliente;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimos caixas cadastrados
            {
                $vendas = $this->venda_model->join('clientes', 'vendas.id_cliente = clientes.id_cliente')->orderBy('id_venda', 'DESC')->limit(5)->find();
                $data['ultimos_cinco'] = TRUE;
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $vendas = $this->venda_model->join('clientes', 'vendas.id_cliente = clientes.id_cliente')->orderBy('id_venda', 'DESC')->limit(5)->find();
            $data['ultimos_cinco'] = TRUE;
        }
        // ------------------------------------------------------------------------------------------ //

        $data['vendas'] = $vendas;

        $data['clientes'] = $this->cliente_model->findAll();

        echo view('templates/header');
        echo view('vendas/index', $data);
        echo view('templates/footer');
    }

    public function show($id_venda)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados da Venda',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['empresa'] = $this->config_empresa_model->where('id_config', 1)->first();

        $data['venda'] = $this->venda_model->where('id_venda', $id_venda)->first();

        // Adiciona um elemento 'nome_do_cliente' e 'nome_do_vendedor' ao array associativo vendas
        $data['venda']['nome_do_cliente'] = $this->cliente_model->where('id_cliente', $data['venda']['id_cliente'])->first()['nome'];
        $data['venda']['nome_do_vendedor'] = $this->vendedor_model->where('id_vendedor', $data['venda']['id_vendedor'])->first()['nome'];
        
        $data['produtos_da_venda'] = $this->produtos_da_venda->where('id_venda', $id_venda)->findAll();

        $data['config_nfe_nfce'] = $this->config_nfe_nfce_model->where('id_config', 1)->first();

        $data['nfe_da_venda'] = $this->nfe_model->where('id_venda', $id_venda)->first();
        $data['nfce_da_venda'] = $this->nfce_model->where('id_venda', $id_venda)->first();

        echo view('templates/header');
        echo view('vendas/show', $data);
        echo view('templates/footer');
    }

    public function delete($id_venda)
    {
        $this->venda_model->where('id_venda', $id_venda)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/vendas');
    }
}