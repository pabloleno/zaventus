<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\SaidaDeMercadoriaModel;
use CodeIgniter\Controller;

class SaidaDeMercadorias extends Controller
{
    private $links;
    private $saida_de_mercadoria_model;
    private $produto_model;

    function __construct()
    {
        $this->links = [
            'menu' => '4.m',
            'item' => '4.0',
            'subItem' => '4.4'
        ];

        $this->produto_model = new ProdutoModel();
        $this->saida_de_mercadoria_model = new SaidaDeMercadoriaModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Saída de Mercadorias',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Saída de Mercadorias", 'rota'   => "", 'active' => true]
        ];

        $data['saida_de_mercadorias'] = $this->saida_de_mercadoria_model->select('id_saida, nome, saida_de_mercadorias.quantidade as qtd_da_saida, data, hora, observacoes')->join('produtos', 'produtos.id_produto = saida_de_mercadorias.id_produto')->findAll();

        echo view('templates/header');
        echo view('saida_de_mercadorias/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Saída',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Reposições", 'rota' => "/reposicoes", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        $data['produtos'] = $this->produto_model->findAll();

        echo view('templates/header');
        echo view('saida_de_mercadorias/create', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->saida_de_mercadoria_model->save($dados);

        // --------------------------- ATUALIZA A QUANTIDADE DO PRODUTO ------------------------------- //
        $qtd_saida = $dados['quantidade'];
        $id_produto = $dados['id_produto'];

        // Pega o produto pelo seu ID
        $produto = $this->produto_model->select('quantidade')->where('id_produto', $id_produto)->first();

        // Tira da quantidade do produto a quantidade da saída
        $qtd = $produto['quantidade'] - $qtd_saida;

        // Atualiza a quantidade do produto com a nova quantidade
        $this->produto_model->set('quantidade', $qtd)->where('id_produto', $id_produto)->update();
        // ------------------------------------------------------------------------------------------ //

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/saidaDeMercadorias');
    }

    public function delete($id_saida)
    {
        // ----------------- DEVOLTE A QUANTIDADE DA SAÍDA PARA O PRODUTO -------------------- // 
        // Pega a reposição e sua quantidade
        $saida = $this->saida_de_mercadoria_model->where('id_saida', $id_saida)->first();
        $qtd_da_saida = $saida['quantidade'];

        // Pega o produto e sua quantidade e seu ID
        $produto = $this->produto_model->where('id_produto', $saida['id_produto'])->first();
        $id_produto = $produto['id_produto'];
        $qtd_do_produto = $produto['quantidade'];

        // Adiciona a quantidade da saída a quantidade do produto
        $qtd = $qtd_do_produto + $qtd_da_saida;

        // Atualiza o produto com a nova quantidae
        $this->produto_model->set('quantidade', $qtd)->where('id_produto', $id_produto)->update();
        // --------------------------------------------------------------------------------- //

        // Remove a saída
        $this->saida_de_mercadoria_model->where('id_saida', $id_saida)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/saidaDeMercadorias');
    }
}
