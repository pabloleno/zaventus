<?php

namespace App\Controllers;

use App\Models\CaixaModel;
use App\Models\LancamentoModel;
use CodeIgniter\Controller;

class Lancamentos extends Controller
{
    private $links;
    private $lancamento_model;
    private $caixa_model;

    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.2'
        ];

        $this->lancamento_model = new LancamentoModel();
        $this->caixa_model = new CaixaModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Lançamentos',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Lançamentos", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $id_lancamento = $dados['id_lancamento'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            if($dados['id_lancamento'] != "") // Filtra somente pelo Cód do caixa
            {
                $lancamentos = $this->lancamento_model->where('id_lancamento', $id_lancamento)->findAll();

                $data['id_lancamento'] = $id_lancamento;
            }
            else if($data_inicio != "" && $data_final != "") // Filtra em conjunto, status e data inicio e final
            {
                $lancamentos = $this->lancamento_model->where('data >=', $data_inicio)->where('data <=', $data_final)->find();

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimos caixas cadastrados
            {
                $lancamentos = $this->lancamento_model->orderBy('id_lancamento', 'DESC')->limit(5)->find();
                $data['ultimos_cinco'] = TRUE;
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $lancamentos = $this->lancamento_model->orderBy('id_lancamento', 'DESC')->limit(5)->find();
            $data['ultimos_cinco'] = TRUE;
        }
        // ------------------------------------------------------------------------------------------ //

        $data['lancamentos'] = $lancamentos;

        echo view('templates/header');
        echo view('lancamentos/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Lançamento',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Lançamentos", 'rota' => "/lancamentos", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        $data['caixas'] = $this->caixa_model->where('status', 'Aberto')->findAll();

        echo view('templates/header');
        echo view('lancamentos/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_lancamento)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Lançamento',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Lançamentos", 'rota' => "/lancamentos", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['lancamento'] = $this->lancamento_model->where('id_lancamento', $id_lancamento)->first();

        echo view('templates/header');
        echo view('lancamentos/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->lancamento_model->save($dados);

        // Se o usuário estiver editando
        if(isset($dados['id_lancamento']))
        {
            $session = session();
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/lancamentos');
        }

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/lancamentos');
    }

    public function delete($id_lancamento)
    {
        $this->lancamento_model->where('id_lancamento', $id_lancamento)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/lancamentos');
    }
}
