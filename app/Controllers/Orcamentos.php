<?php

namespace App\Controllers;

use App\Models\CaixaModel;
use App\Models\ClienteModel;
use App\Models\OrcamentoModel;
use App\Models\ProdutoDoOrcamentoModel;
use App\Models\VendaModel;
use App\Models\ProdutoDaVendaModel;
use CodeIgniter\Controller;

class Orcamentos extends Controller
{
    private $links;
    private $orcamento_model;
    private $produto_do_orcamento_model;
    private $venda_model;
    private $produto_da_venda_model;

    private $cliente_model;
    private $caixa_model;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '2.m',
            'item' => '2.0',
            'subItem' => '2.7'
        ];

        $this->orcamento_model = new OrcamentoModel();
        $this->produto_do_orcamento_model = new ProdutoDoOrcamentoModel();
        $this->venda_model = new VendaModel();
        $this->produto_da_venda_model = new ProdutoDaVendaModel();
        $this->cliente_model = new ClienteModel();
        $this->caixa_model = new CaixaModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Orçamentos',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Orçamentos", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $id_orcamento = $dados['id_orcamento'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            $id_cliente = $dados['id_cliente'];

            if($dados['id_orcamento'] != "") // Filtra somente pelo Cód do caixa
            {
                $orcamentos = $this->orcamento_model->where('id_orcamento', $id_orcamento)->join('clientes', 'orcamentos.id_cliente = clientes.id_cliente')->findAll();

                $data['id_orcamento'] = $id_orcamento;
            }
            else if($data_inicio != "" && $data_final != "" && $id_cliente != "")
            {
                if($id_cliente == "Todos")
                {
                    $orcamentos = $this->orcamento_model->where('data >=', $data_inicio)->where('data <=', $data_final)->join('clientes', 'orcamentos.id_cliente = clientes.id_cliente')->findAll();
                }
                else
                {
                    $orcamentos = $this->orcamento_model->where('data >=', $data_inicio)->where('data <=', $data_final)->where('id_cliente', $id_cliente)->join('clientes', 'orcamentos.id_cliente = clientes.id_cliente')->findAll();
                }

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;

                $data['id_cliente'] = $id_cliente;
            }
            else if($dados['data_inicio'] != "" && $dados['data_final'] != "") // Filtra somente pela data inicial e data final, quando não tem status definido
            {
                $orcamentos = $this->orcamento_model->where('data >=', $data_inicio)->where('data <=', $data_final)->join('clientes', 'orcamentos.id_cliente = clientes.id_cliente')->find();
                
                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else if($id_cliente != "")
            {
                if($id_cliente == "Todos")
                {
                    $orcamentos = $this->orcamento_model->join('clientes', 'orcamentos.id_cliente = clientes.id_cliente')->findAll();
                }
                else
                {
                    $orcamentos = $this->orcamento_model->where('id_cliente', $id_cliente)->join('clientes', 'orcamentos.id_cliente = clientes.id_cliente')->findAll();
                }

                $data['id_cliente'] = $id_cliente;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimos caixas cadastrados
            {
                $orcamentos = $this->orcamento_model->orderBy('id_orcamento', 'DESC')->join('clientes', 'orcamentos.id_cliente = clientes.id_cliente')->findAll();
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $orcamentos = $this->orcamento_model->orderBy('id_orcamento', 'DESC')->join('clientes', 'orcamentos.id_cliente = clientes.id_cliente')->findAll();
        }
        // ------------------------------------------------------------------------------------------ //

        $data['orcamentos'] = $orcamentos; // Passa os dados para o metodo para poder filtrar
        $data['clientes'] = $this->cliente_model->findAll();
        $data['caixas'] = $this->caixa_model->findAll();

        echo view('templates/header');
        echo view('orcamentos/index', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega e exibe os detalhes do registro solicitado.
     */
    public function show($id_orcamento)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Orçamento',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Orçamentos", 'rota' => "/orcamentos", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['orcamento'] = $this->orcamento_model->where('id_orcamento', $id_orcamento)->first();

        $data['produtos_do_orcamento'] = $this->produto_do_orcamento_model->where('id_orcamento', $id_orcamento)->findAll();

        echo view('templates/header');
        echo view('orcamentos/show', $data);
        echo view('templates/footer');
    }

    /**
     * Remove o registro solicitado e retorna para a listagem.
     */
    public function delete($id_orcamento)
    {
        $this->orcamento_model->where('id_orcamento', $id_orcamento)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/orcamentos');
    }

    /**
     * Finaliza venda.
     */
    public function finalizarVenda($id_orcamento)
    {
        $dados = $this->orcamento_model->where('id_orcamento', $id_orcamento)->first();
        $produtos = $this->produto_do_orcamento_model->where('id_orcamento', $id_orcamento)->findAll();

        $id_venda = $this->venda_model->insert($dados);

        foreach($produtos as $produto)
        {
            $produto['id_venda'] = $id_venda;
            $this->produto_da_venda_model->insert($produto);
        }

        $this->orcamento_model->where('id_orcamento', $id_orcamento)->set('status', "Finalizado")->update(); // Altera o status informando que o orçamento foi finalizado e registrado como venda.

        $session = session();
        $session->setFlashdata('alert', 'success_finaliza_venda');

        return redirect()->to("/orcamentos/show/$id_orcamento");
    }
}
