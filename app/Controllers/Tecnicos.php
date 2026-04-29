<?php

namespace App\Controllers;

use App\Models\TecnicoModel;
use CodeIgniter\Controller;

class Tecnicos extends Controller
{
    private $links;
    private $tecnico_model;

    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.5'
        ];

        $this->tecnico_model = new TecnicoModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Técnicos',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Técnicos", 'rota'   => "", 'active' => true]
        ];

        $data['tecnicos'] = $this->tecnico_model->findAll();

        echo view('templates/header');
        echo view('tecnicos/index', $data);
        echo view('templates/footer');
    }

    public function show($id_tecnico)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Técnico',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Técnicos", 'rota' => "/tecnicos", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['tecnico'] = $this->tecnico_model->where('id_tecnico', $id_tecnico)->first();

        echo view('templates/header');
        echo view('tecnicos/show', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Técnico',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Técnicos", 'rota'   => "/tecnicos", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('tecnicos/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_tecnico)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Técnico',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Técnicos", 'rota'   => "/tecnicos", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['tecnico'] = $this->tecnico_model->where('id_tecnico', $id_tecnico)->first();

        echo view('templates/header');
        echo view('tecnicos/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->tecnico_model->save($dados);

        // Caso a ação é editar
        if(isset($dados['id_tecnico']))
        {
            $session = session();
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/tecnicos');
        }

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/tecnicos');
    }

    public function delete($id_tecnico)
    {
        $this->tecnico_model->where('id_tecnico', $id_tecnico)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/tecnicos');
    }
}
