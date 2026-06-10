<?php

namespace App\Controllers;

use App\Models\ServicoMaoDeObraModel;
use CodeIgniter\Controller;

class ServicosMaoDeObra extends Controller
{
    private $links;
    private $servico_mao_de_obra_model;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.6'
        ];

        $this->servico_mao_de_obra_model = new ServicoMaoDeObraModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Serviços/Mão de Obra',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Serviços/Mão de Obra", 'rota'   => "", 'active' => true]
        ];

        $data['servicos'] = $this->servico_mao_de_obra_model->findAll();

        echo view('templates/header');
        echo view('servicos_mao_de_obra/index', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara os dados e exibe o formulario de cadastro.
     */
    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Serviço/Mão de Obra',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Serviços/Mão de Obra", 'rota'   => "/servicosMaoDeObra", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('servicos_mao_de_obra/form', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega o registro solicitado e exibe o formulario de edicao.
     */
    public function edit($id_servico)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Serviço/Mão de Obra',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Serviços/Mão de Obra", 'rota'   => "/servicosMaoDeObra", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['servico'] = $this->servico_mao_de_obra_model->where('id_servico', $id_servico)->first();

        echo view('templates/header');
        echo view('servicos_mao_de_obra/form', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];

        $this->servico_mao_de_obra_model->save($dados);

        // Caso a ação é editar
        if(isset($dados['id_servico']))
        {
            $session = session();
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/servicosMaoDeObra');
        }

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/servicosMaoDeObra');
    }

    /**
     * Remove o registro solicitado e retorna para a listagem.
     */
    public function delete($id_servico)
    {
        $this->servico_mao_de_obra_model->where('id_servico', $id_servico)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/servicosMaoDeObra');
    }
}
