<?php

namespace App\Controllers;

use App\Models\CaixaModel;
use App\Models\ClienteModel;
use App\Models\FormaDePagamentoModel;
use App\Models\PedidoModel;
use App\Models\ProdutoDaVendaModel;
use App\Models\ProdutoDaVendaRapidaModel;
use App\Models\ProdutoDoPedidoModel;
use App\Models\ProdutoModel;
use App\Models\VendaModel;
use App\Models\VendedorModel;
use CodeIgniter\Controller;

class VendaRapida extends Controller
{
    private $links;
    private $produto_model;
    private $caixa_model;
    private $produto_da_venda_rapida_model;
    private $venda_model;
    private $cliente_model;
    private $produto_da_venda_model;
    private $pedido_model;
    private $produto_do_pedido_model;
    private $forma_de_pagamento_model;
    private $vendedor_model;

    function __construct()
    {
        $this->links = [
            'menu' => '2.m',
            'item' => '2.0',
            'subItem' => '2.2'
        ];

        $this->produto_model                 = new ProdutoModel();
        $this->caixa_model                   = new CaixaModel();
        $this->produto_da_venda_rapida_model = new ProdutoDaVendaRapidaModel();
        $this->venda_model                   = new VendaModel();
        $this->cliente_model                 = new ClienteModel();
        $this->produto_da_venda_model        = new ProdutoDaVendaModel();
        $this->pedido_model                  = new PedidoModel();
        $this->produto_do_pedido_model       = new ProdutoDoPedidoModel();
        $this->forma_de_pagamento_model      = new FormaDePagamentoModel();
        $this->vendedor_model                = new VendedorModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Venda Rápida',
            'icone'  => 'fa fa-money-bill-alt'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Venda Rápida", 'rota'   => "", 'active' => true]
        ];

        $data['caixas']                   = $this->caixa_model->where('status', "Aberto")->findAll();
        $data['produtos_do_estoque']      = $this->produto_model->findAll();
        $data['clientes']                 = $this->cliente_model->findAll();
        $data['produtos_da_venda_rapida'] = $this->produto_da_venda_rapida_model->findAll();
        $data['valor_da_venda']           = $this->produto_da_venda_rapida_model->selectSum('valor_final')->first();
        $data['formas_de_pagamento']      = $this->forma_de_pagamento_model->findAll();
        $data['vendedores']               = $this->vendedor_model->paraVenda();

        echo view('templates/header');
        echo view('venda_rapida/index', $data);
        echo view('templates/footer');
    }

    public function addProdutoDaVenda()
    {
        $dados = $this->request->getvar();
        
        $produto = $this->produto_model->where('id_produto', $dados['id_produto'])->first();

        $this->produto_da_venda_rapida_model->insert([
            'nome'             => $produto['nome'],
            'unidade'          => $produto['unidade'],
            'codigo_de_barras' => $produto['codigo_de_barras'],
            'quantidade'       => $dados['quantidade'],
            'valor_unitario'   => $produto['valor_de_venda'],
            'subtotal'         => $dados['quantidade'] * $produto['valor_de_venda'],
            'desconto'         => 0,
            'valor_final'      => $dados['quantidade'] * $produto['valor_de_venda'],
            'NCM'              => $produto['NCM'],
            'CSOSN'            => $produto['CSOSN'],
            'CFOP'             => $produto['CFOP'],
            'id_produto'       => $produto['id_produto']
        ]);

        $session = session();
        $session->setFlashdata('alert', 'success_add_produto');

        return redirect()->to('/vendaRapida');
    }

    public function tipoVenda($dados)
    {
        // $dados['valor_a_pagar'] = $dados['valor_a_pagar'] - $dados['desconto'];

        // ----------------------------------------------------------------------------------------
        $id_venda = $this->venda_model->insert($dados);

        $produtos_da_venda_rapida = $this->produto_da_venda_rapida_model->findAll();

        foreach ($produtos_da_venda_rapida as $produto) {
            $produto['id_venda'] = $id_venda;

            $this->produto_da_venda_model->insert($produto);

            // Decrementa da quantidade do estoque a quantidade do produto vendido
            $produto_do_estoque = $this->produto_model->where('id_produto', $produto['id_produto'])->first();
            $nova_qtd = $produto_do_estoque['quantidade'] - $produto['quantidade'];

            $this->produto_model->set('quantidade', $nova_qtd)->where('id_produto', $produto['id_produto'])->update();
        }

        // Remove todos os registros da tabela produtos_da_venda_rapida.
        $this->produto_da_venda_rapida_model->emptyTable('produtos_da_venda_rapida');

        $session = session();
        $session->setFlashdata('alert', 'success_venda');

        return TRUE;
    }

    public function tipoPedido($dados)
    {
        $dados['situacao']         = "Não Pago - Andamento"; // Situação do Pedido
        $dados['prazo_de_entrega'] = $dados['data']; // Para de entrega é o mesmo da data, pode ser alterado na sessão pedidos

        $id_pedido = $this->pedido_model->insert($dados);

        $produtos_da_venda_rapida = $this->produto_da_venda_rapida_model->findAll();

        foreach ($produtos_da_venda_rapida as $produto) {
            $produto['id_pedido'] = $id_pedido;
            $this->produto_do_pedido_model->insert($produto);
        }

        // Remove todos os registros da tabela produtos_da_venda_rapida.
        $this->produto_da_venda_rapida_model->emptyTable('produtos_da_venda_rapida');

        $session = session();
        $session->setFlashdata('alert', 'success_pedido');

        return TRUE;
    }

    public function store()
    {
        $dados = $this->request->getvar();

        // $dados['data'] = date('Y-m-d');
        // $dados['hora'] = date('H:i:s');
        
        if($dados['tipo'] == "Venda")
        {
            $this->tipoVenda($dados);
        }
        else if($dados['tipo'] == "Pedido")
        {
            $this->tipoPedido($dados);
        }

        return redirect()->to('/vendaRapida');
    }

    public function deleteProduto($id_produto_da_venda_rapida)
    {
        $this->produto_da_venda_rapida_model->where('id_produto_da_venda_rapida', $id_produto_da_venda_rapida)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete_produto');

        return redirect()->to('/vendaRapida');
    }

    public function alteraQuantidade()
    {
        $dados = $this->request->getvar();

        $produto_da_venda_rapida = $this->produto_da_venda_rapida_model->where('id_produto_da_venda_rapida', $dados['id_produto_da_venda_rapida'])->first();

        // dd($produto_da_venda_rapida);

        $subtotal = $dados['quantidade'] * $produto_da_venda_rapida['valor_unitario'];

        $this->produto_da_venda_rapida_model->save([
            'id_produto_da_venda_rapida' => $dados['id_produto_da_venda_rapida'],
            'quantidade'                 => $dados['quantidade'],
            'subtotal'                   => $subtotal,
            'valor_final'                => $subtotal - $produto_da_venda_rapida['desconto']
        ]);

        $session = session();
        $session->setFlashdata('alert', 'success_update_qtd_produto');

        return redirect()->to('/vendaRapida');
    }


    public function alteraDesconto()
    {
        $dados = $this->request->getvar();

        $produto_da_venda_rapida = $this->produto_da_venda_rapida_model->where('id_produto_da_venda_rapida', $dados['id_produto_da_venda_rapida'])->first();

        // dd($produto_da_venda_rapida);

        $subtotal = $produto_da_venda_rapida['quantidade'] * $produto_da_venda_rapida['valor_unitario'];

        $this->produto_da_venda_rapida_model->save([
            'id_produto_da_venda_rapida' => $dados['id_produto_da_venda_rapida'],
            'desconto'                   => $dados['desconto'],
            'subtotal'                   => $subtotal,
            'valor_final'                => $subtotal - $dados['desconto']
        ]);

        $session = session();
        $session->setFlashdata('alert', 'success_update_desconto_produto');

        return redirect()->to('/vendaRapida');
    }

    public function alteraValorUnitario()
    {
        $dados = $this->request->getvar();

        $produto_da_venda_rapida = $this->produto_da_venda_rapida_model->where('id_produto_da_venda_rapida', $dados['id_produto_da_venda_rapida'])->first();

        // dd($produto_da_venda_rapida);

        $subtotal = $produto_da_venda_rapida['quantidade'] * $dados['valor_unitario'];

        $this->produto_da_venda_rapida_model->save([
            'id_produto_da_venda_rapida' => $dados['id_produto_da_venda_rapida'],
            'valor_unitario'             => $dados['valor_unitario'],
            'desconto'                   => $produto_da_venda_rapida['desconto'],
            'subtotal'                   => $subtotal,
            'valor_final'                => $subtotal - $produto_da_venda_rapida['desconto']
        ]);

        $session = session();
        $session->setFlashdata('alert', 'success_update_valor_unitario_produto');

        return redirect()->to('/vendaRapida');
    }
}
