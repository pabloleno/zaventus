<?php

namespace App\Controllers;

use App\Libraries\ImagemCadastro;
use App\Models\FuncionarioModel;
use App\Models\VendedorModel;
use CodeIgniter\Controller;
use InvalidArgumentException;

class Vendedores extends Controller
{
    private $links;
    private $vendedor_model;
    private $funcionario_model;

    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.4'
        ];

        $this->vendedor_model = new VendedorModel();
        $this->funcionario_model = new FuncionarioModel();
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

        $data['vendedores'] = $this->vendedor_model->visiveis();

        echo view('templates/header');
        echo view('vendedores/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        return redirect()->to('/funcionarios/create?tipo=Vendedor');
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
            ['titulo' => "Vendedores", 'rota'   => "/vendedores", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['vendedor'] = $this->vendedor_model->where('id_vendedor', $id_vendedor)->first();

        if (empty($data['vendedor'])) {
            return redirect()->to('/vendedores');
        }

        if (! empty($data['vendedor']['id_funcionario'])) {
            return redirect()->to('/funcionarios/edit/' . $data['vendedor']['id_funcionario']);
        }

        echo view('templates/header');
        echo view('vendedores/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];
        $vendedorAnterior = ! empty($dados['id_vendedor'])
            ? $this->vendedor_model->where('id_vendedor', $dados['id_vendedor'])->first()
            : [];

        try {
            $fotoNova = ImagemCadastro::salvar($this->request->getFile('foto'), 'vendedores');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('erro_foto', $e->getMessage());
        }

        if ($fotoNova !== null) {
            $dados['foto'] = $fotoNova;
        }

        $this->vendedor_model->save($dados);

        if ($fotoNova !== null) {
            ImagemCadastro::remover($vendedorAnterior['foto'] ?? '');
        }

        $session = session();

        if (isset($dados['id_vendedor'])) {
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/vendedores');
        }

        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/vendedores');
    }

    public function delete($id_vendedor)
    {
        $vendedor = $this->vendedor_model->where('id_vendedor', $id_vendedor)->first();

        if (empty($vendedor)) {
            return redirect()->to('/vendedores');
        }

        $session = session();

        if ($this->vendedor_model->ehGeral($vendedor)) {
            $session->setFlashdata('alert', 'error_delete_geral');

            return redirect()->to('/vendedores');
        }

        if (! empty($vendedor['id_funcionario'])) {
            $funcionario = $this->funcionario_model->find($vendedor['id_funcionario']);
            $this->funcionario_model->update($vendedor['id_funcionario'], [
                'tipo_funcionario' => FuncionarioModel::removerAtuacao($funcionario ?? [], 'Vendedor'),
            ]);
        } else {
            ImagemCadastro::remover($vendedor['foto'] ?? '');
        }

        $this->vendedor_model->ocultar((int) $id_vendedor);
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/vendedores');
    }
}
