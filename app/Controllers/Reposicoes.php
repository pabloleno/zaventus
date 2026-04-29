<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\ReposicaoModel;
use CodeIgniter\Controller;

class Reposicoes extends Controller
{
    private $links;
    private $reposicao_model;
    private $produto_model;

    function __construct()
    {
        $this->links = [
            'menu' => '4.m',
            'item' => '4.0',
            'subItem' => '4.3'
        ];

        $this->reposicao_model = new ReposicaoModel();
        $this->produto_model = new ProdutoModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Reposições de Produtos',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Reposições", 'rota'   => "", 'active' => true]
        ];

        $data['reposicoes'] = $this->reposicao_model->select('id_reposicao, nome, reposicoes.quantidade as qtd_da_reposicao, data, hora, observacoes')->join('produtos', 'produtos.id_produto = reposicoes.id_produto')->findAll();

        echo view('templates/header');
        echo view('reposicoes/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Reposição',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Reposições", 'rota' => "/reposicoes", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        $data['produtos'] = $this->produto_model->findAll();

        echo view('templates/header');
        echo view('reposicoes/create', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->reposicao_model->save($dados);

        // --------------------------- ATUALIZA A QUANTIDADE DO PRODUTO ------------------------------- //
        $qtd_reposicao = $dados['quantidade'];
        $id_produto = $dados['id_produto'];

        // Pega o produto pelo seu ID
        $produto = $this->produto_model->select('quantidade')->where('id_produto', $id_produto)->first();

        // Atribui a quantidade da reposição a quantidade do produto
        $qtd = $qtd_reposicao + $produto['quantidade'];

        // Atualiza a quantidade do produto com a nova quantidade
        $this->produto_model->set('quantidade', $qtd)->where('id_produto', $id_produto)->update();
        // ------------------------------------------------------------------------------------------ //

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/reposicoes');
    }

    public function delete($id_reposicao)
    {
        // ---------- RETIRA A QUANTIDADE DA REPOSIÇÃO DA QUANTIDADE DO PRODUTO ------------- // 
        // Pega a reposição e sua quantidade
        $reposicao = $this->reposicao_model->where('id_reposicao', $id_reposicao)->first();
        $qtd_da_reposicao = $reposicao['quantidade'];

        // Pega o produto e sua quantidade e seu ID
        $produto = $this->produto_model->where('id_produto', $reposicao['id_produto'])->first();
        $id_produto = $produto['id_produto'];
        $qtd_do_produto = $produto['quantidade'];

        // Tira da quantidade do produto a quantidade da reposição
        $qtd = $qtd_do_produto - $qtd_da_reposicao;
        
        // Atualiza o produto com a nova quantidae
        $this->produto_model->set('quantidade', $qtd)->where('id_produto', $id_produto)->update();
        // --------------------------------------------------------------------------------- //

        // Remove a reposição
        $this->reposicao_model->where('id_reposicao', $id_reposicao)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/reposicoes');
    }
}
