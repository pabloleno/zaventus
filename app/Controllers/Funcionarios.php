<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FuncionarioModel;

class Funcionarios extends Controller
{
    private $links;
    private $funcionario_model;

    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.3'
        ];

        $this->funcionario_model = new FuncionarioModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Funcionários',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Funcionários", 'rota'   => "", 'active' => true]
        ];

        $data['funcionarios'] = $this->funcionario_model->findAll();

        echo view('templates/header');
        echo view('funcionarios/index', $data);
        echo view('templates/footer');
    }

    public function show($id_funcionario)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Funcionário',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Funcionários", 'rota' => "/funcionarios", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['funcionario'] = $this->funcionario_model->where('id_funcionario', $id_funcionario)->first();

        echo view('templates/header');
        echo view('funcionarios/show', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Funcionário',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Funcionários", 'rota'   => "/funcionarios", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('funcionarios/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_funcionario)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Funcionário',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Funcionários", 'rota'   => "/funcionarios", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['funcionario'] = $this->funcionario_model->where('id_funcionario', $id_funcionario)->first();

        echo view('templates/header');
        echo view('funcionarios/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->funcionario_model->save($dados);

        $session = session();

        // Caso a ação é editar
        if(isset($dados['id_funcionario']))
        {
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/funcionarios');
        }

        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/funcionarios');
    }

    public function delete($id_funcionario)
    {
        $this->funcionario_model->where('id_funcionario', $id_funcionario)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/funcionarios');
    }
}