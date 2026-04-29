<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PedidoModel;
use App\Models\ProdutoDoPedidoModel;
use App\Models\VendaModel;
use App\Models\ProdutoDaVendaModel;
use CodeIgniter\Controller;

class Pedidos extends Controller
{
    private $links;
    private $pedido_model;
    private $produto_do_pedido_model;
    private $venda_model;
    private $produto_da_venda_model;
    private $cliente_model;

    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.9'
        ];

        $this->pedido_model = new PedidoModel();
        $this->produto_do_pedido_model = new ProdutoDoPedidoModel();
        $this->venda_model = new VendaModel();
        $this->produto_da_venda_model = new ProdutoDaVendaModel();
        $this->cliente_model = new ClienteModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Pedidos',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Pedidos", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $id_pedido = $dados['id_pedido'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            $id_cliente = $dados['id_cliente'];

            if($dados['id_pedido'] != "") // Filtra somente pelo Cód do caixa
            {
                $pedidos = $this->pedido_model->where('id_pedido', $id_pedido)->join('clientes', 'pedidos.id_cliente = clientes.id_cliente')->findAll();

                $data['id_pedido'] = $id_pedido;
            }
            else if($data_inicio != "" && $data_final != "" && $id_cliente != "")
            {
                if($id_cliente == "Todos")
                {
                    $pedidos = $this->pedido_model->where('data >=', $data_inicio)->where('data <=', $data_final)->join('clientes', 'pedidos.id_cliente = clientes.id_cliente')->findAll();
                }
                else
                {
                    $pedidos = $this->pedido_model->where('data >=', $data_inicio)->where('data <=', $data_final)->where('id_cliente', $id_cliente)->join('clientes', 'pedidos.id_cliente = clientes.id_cliente')->findAll();
                }

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;

                $data['id_cliente'] = $id_cliente;
            }
            else if($dados['data_inicio'] != "" && $dados['data_final'] != "") // Filtra somente pela data inicial e data final, quando não tem status definido
            {
                $pedidos = $this->pedido_model->where('data >=', $data_inicio)->where('data <=', $data_final)->join('clientes', 'pedidos.id_cliente = clientes.id_cliente')->find();
                
                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else if($id_cliente != "")
            {
                if($id_cliente == "Todos")
                {
                    $pedidos = $this->pedido_model->join('clientes', 'pedidos.id_cliente = clientes.id_cliente')->findAll();
                }
                else
                {
                    $pedidos = $this->pedido_model->where('id_cliente', $id_cliente)->join('clientes', 'pedidos.id_cliente = clientes.id_cliente')->findAll();
                }

                $data['id_cliente'] = $id_cliente;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimos caixas cadastrados
            {
                $pedidos = $this->pedido_model->orderBy('id_pedido', 'DESC')->limit(5)->join('clientes', 'pedidos.id_cliente = clientes.id_cliente')->find();
                $data['ultimos_cinco'] = TRUE;
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $pedidos = $this->pedido_model->orderBy('id_pedido', 'DESC')->limit(5)->join('clientes', 'pedidos.id_cliente = clientes.id_cliente')->find();
            $data['ultimos_cinco'] = TRUE;
        }
        // ------------------------------------------------------------------------------------------ //

        $data['pedidos'] = $pedidos;

        $data['clientes'] = $this->cliente_model->findAll();
        
        echo view('templates/header');
        echo view('pedidos/index', $data);
        echo view('templates/footer');
    }

    public function show($id_pedido)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Pedido',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Pedidos", 'rota' => "/pedidos", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['pedido'] = $this->pedido_model->where('id_pedido', $id_pedido)->first();

        $data['produtos_do_pedido'] = $this->produto_do_pedido_model->where('id_pedido', $id_pedido)->findAll();

        echo view('templates/header');
        echo view('pedidos/show', $data);
        echo view('templates/footer');
    }

    public function delete($id_pedido)
    {
        $this->pedido_model->where('id_pedido', $id_pedido)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/pedidos');
    }

    public function finalizarPedido($id_pedido)
    {
        $dados = $this->pedido_model->where('id_pedido', $id_pedido)->first();
        $produtos = $this->produto_do_pedido_model->where('id_pedido', $id_pedido)->findAll();

        $id_venda = $this->venda_model->insert($dados);

        foreach($produtos as $produto)
        {
            $produto['id_venda'] = $id_venda;
            $this->produto_da_venda_model->insert($produto);
        }

        $this->pedido_model->where('id_pedido', $id_pedido)->set('situacao', "Pago - Finalizado")->update(); // Altera o status informando que o orçamento foi finalizado e registrado como venda.

        $session = session();
        $session->setFlashdata('alert', 'success_finaliza_pedido');

        return redirect()->to("/pedidos/show/$id_pedido");
    }
}
