<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FornecedorModel;

class Fornecedores extends Controller
{
    private $links;
    private $fornecedor_model;

    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.2'
        ];

        $this->fornecedor_model = new FornecedorModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'fornecedores',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "fornecedores", 'rota'   => "", 'active' => true]
        ];

        $data['fornecedores'] = $this->fornecedor_model->findAll();

        echo view('templates/header');
        echo view('fornecedores/index', $data);
        echo view('templates/footer');
    }

    public function show($id_fornecedor)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Fornecedor',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Fornecedor", 'rota' => "/fornecedores", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['fornecedor'] = $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->first();

        echo view('templates/header');
        echo view('fornecedores/show', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Fornecedor',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Fornecedores", 'rota'   => "/fornecedores", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('fornecedores/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_fornecedor)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Funcionário',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Fornecedores", 'rota'   => "/fornecedores", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['fornecedor'] = $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->first();

        echo view('templates/header');
        echo view('fornecedores/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->fornecedor_model->save($dados);

        $session = session();
        
        // Caso a ação é editar
        if(isset($dados['id_fornecedor']))
        {
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/fornecedores');
        }

        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/fornecedores');
    }

    public function delete($id_fornecedor)
    {
        $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/fornecedores');
    }
}