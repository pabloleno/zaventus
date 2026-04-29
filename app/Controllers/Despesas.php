<?php

namespace App\Controllers;

use App\Models\DespesaModel;
use CodeIgniter\Controller;

class Despesas extends Controller
{
    private $links;
    private $despesa_model;

    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.5'
        ];

        $this->despesa_model = new DespesaModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Despesas',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Despesas", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $id_despesa = $dados['id_despesa'];

            $tipo = $dados['tipo'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            if($dados['id_despesa'] != "") // Filtra somente pelo Cód do caixa
            {
                $despesas = $this->despesa_model->where('id_despesa', $id_despesa)->findAll();

                $data['id_despesa'] = $id_despesa;
            }
            else if($tipo != "" && $data_inicio == "" && $data_final == "") // Filtra somente pelo tipo com data inicio e final em branco.
            {
                if($tipo == "Todos")
                {
                    $despesas = $this->despesa_model->findAll(); // Retorna todas as despesas
                }
                else
                {
                    $despesas = $this->despesa_model->where('tipo', $tipo)->find(); // Retorna a despesa pelo seu tipo
                }

                $data['tipo'] = $tipo;
            }
            else if($tipo != "" && $data_inicio != "" && $data_final != "") // Filtra tipo com data inicio e data final
            {
                if($tipo == "Todos")
                {
                    $despesas = $this->despesa_model->where('data >=', $data_inicio)->where('data <=', $data_final)->find(); // Retorna todas as despesas com a data inicio e final correspondentes
                }
                else
                {
                    $despesas = $this->despesa_model->where('tipo', $tipo)->where('data >=', $data_inicio)->where('data <=', $data_final)->find(); // Retorna as despesas com tipo e data inicio e final correspondentes
                }

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;

                $data['tipo'] = $tipo;
            }
            else if($data_inicio != "" && $data_final != "") // Filtra em conjunto, status e data inicio e final
            {
                $despesas = $this->despesa_model->where('data >=', $data_inicio)->where('data <=', $data_final)->find();

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimos caixas cadastrados
            {
                $despesas = $this->despesa_model->orderBy('id_despesa', 'DESC')->limit(5)->find();
                $data['ultimos_cinco'] = TRUE;
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $despesas = $this->despesa_model->orderBy('id_despesa', 'DESC')->limit(5)->find();
            $data['ultimos_cinco'] = TRUE;
        }
        // ------------------------------------------------------------------------------------------ //

        $data['despesas'] = $despesas;

        echo view('templates/header');
        echo view('despesas/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Despesa',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Despesas", 'rota' => "/despesas", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('despesas/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_despesa)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Despesa',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Despesas", 'rota' => "/despesas", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['despesa'] = $this->despesa_model->where('id_despesa', $id_despesa)->first();

        echo view('templates/header');
        echo view('despesas/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->despesa_model->save($dados);

        // Se a ação do usuário for editar
        if(isset($dados['id_despesa']))
        {
            $session = session();
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/despesas');
        }

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/despesas');
    }

    public function delete($id_despesa)
    {
        $this->despesa_model->where('id_despesa', $id_despesa)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/despesas');
    }
}
