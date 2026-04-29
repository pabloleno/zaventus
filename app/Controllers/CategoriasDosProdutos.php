<?php

namespace App\Controllers;

use App\Models\CategoriasDosProdutosModel;
use CodeIgniter\Controller;

class CategoriasDosProdutos extends Controller
{
    private $links;
    private $categoria_model;

    function __construct()
    {
        $this->links = [
            'menu' => '4.m',
            'item' => '4.0',
            'subItem' => '4.2'
        ];

        $this->categoria_model = new CategoriasDosProdutosModel();
    }

    public function index()
    {
        $data['links'] = $this->links;
        
        $data['titulo'] = [
            'modulo' => 'Categorias dos Produtos',
            'icone'  => 'fa fa-database'
        ];
        
        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Categorias", 'rota'   => "", 'active' => true]
        ];

        $data['categorias'] = $this->categoria_model->findAll();

        echo view('templates/header');
        echo view('categorias_dos_produtos/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Categoria',
            'icone'  => 'fa fa-plus-circle'
        ];
        
        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Categorias dos Produtos", 'rota' => "/CategoriasDosProdutos", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('categorias_dos_produtos/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_categoria)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Categoria',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Categorias dos Produtos", 'rota' => "/categoriasDosProdutos", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['categoria'] = $this->categoria_model->where('id_categoria', $id_categoria)->first();

        echo view('templates/header');
        echo view('categorias_dos_produtos/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->categoria_model->save($dados);

        $session = session();
        
        // Caso a ação seja editar
        if(isset($dados['id_categoria']))
        {
            $session->setFlashdata('alert', 'success_edit');
            return redirect()->to('/categoriasDosProdutos');
        }

        $session->setFlashdata('alert', 'success_create');
        return redirect()->to('/categoriasDosProdutos');
    }

    public function delete($id_categoria)
    {
        $this->categoria_model->where('id_categoria', $id_categoria)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/CategoriasDosProdutos');
    }
}
