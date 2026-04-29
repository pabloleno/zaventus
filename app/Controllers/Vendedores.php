<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\VendedorModel;

class Vendedores extends Controller
{
    private $links;
    private $funcionario_model;

    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.4'
        ];

        $this->vendedor_model = new VendedorModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Vendedores',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Vendedores", 'rota'   => "", 'active' => true]
        ];

        $data['vendedores'] = $this->vendedor_model->findAll();

        echo view('templates/header');
        echo view('vendedores/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Vendedor',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Vendedores", 'rota'   => "/vendedores", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('vendedores/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_vendedor)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Vendedor',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Vendedores", 'rota'   => "/funcionarios", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['vendedor'] = $this->vendedor_model->where('id_vendedor', $id_vendedor)->first();

        echo view('templates/header');
        echo view('vendedores/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->vendedor_model->save($dados);

        $session = session();

        // Caso a ação é editar
        if(isset($dados['id_vendedor']))
        {
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/vendedores');
        }

        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/vendedores');
    }

    public function delete($id_vendedor)
    {
        $this->vendedor_model->where('id_vendedor', $id_vendedor)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/vendedores');
    }
}