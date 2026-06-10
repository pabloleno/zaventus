<?php

namespace App\Controllers;

use App\Models\CaixaModel;
use App\Models\RetiradaModel;
use CodeIgniter\Controller;

class Retiradas extends Controller
{
    private $links;
    private $retirada_model;
    private $caixa_model;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.4'
        ];

        $this->retirada_model = new RetiradaModel();
        $this->caixa_model = new CaixaModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Retiradas',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Retiradas", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $id_retirada = $dados['id_retirada'];

            $tipo = $dados['tipo'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            if($dados['id_retirada'] != "") // Filtra somente pelo Cód do caixa
            {
                $retiradas = $this->retirada_model->where('id_retirada', $id_retirada)->findAll();

                $data['id_retirada'] = $id_retirada;
            }
            else if($tipo != "" && $data_inicio == "" && $data_final == "") // Filtra somente pelo tipo com data inicio e final em branco.
            {
                if($tipo == "Todos") // Retorna todas as retiradas
                {
                    $retiradas = $this->retirada_model->findAll();
                }
                else // Retorna a retirada de acordo com seu tipo
                {
                    $retiradas = $this->retirada_model->where('tipo', $tipo)->find();
                }

                $data['tipo'] = $tipo;
            }
            else if($tipo != "" && $data_inicio != "" && $data_final != "") // Filtra tipo com data inicio e data final
            {
                if($tipo == "Todos")
                {
                    $retiradas = $this->retirada_model->where('data >=', $data_inicio)->where('data <=', $data_final)->find(); // Retorna todas as retiradas
                }
                else
                {
                    $retiradas = $this->retirada_model->where('tipo', $tipo)->where('data >=', $data_inicio)->where('data <=', $data_final)->find(); // Retorna somente as retiradas com o tipo e data de inicio e final correspondentes
                }

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;

                $data['tipo'] = $tipo;
            }
            else if($data_inicio != "" && $data_final != "") // Filtra em conjunto, status e data inicio e final
            {
                $retiradas = $this->retirada_model->where('data >=', $data_inicio)->where('data <=', $data_final)->find();

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimos caixas cadastrados
            {
                $retiradas = $this->retirada_model->orderBy('id_retirada', 'DESC')->findAll();
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $retiradas = $this->retirada_model->orderBy('id_retirada', 'DESC')->findAll();
        }
        // ------------------------------------------------------------------------------------------ //

        $data['retiradas'] = $retiradas;

        echo view('templates/header');
        echo view('retiradas/index', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara os dados e exibe o formulario de cadastro.
     */
    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Retirada',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Lançamentos", 'rota' => "/retiradas", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        $data['caixas'] = $this->caixa_model->where('status', 'Aberto')->findAll();

        echo view('templates/header');
        echo view('retiradas/form', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega o registro solicitado e exibe o formulario de edicao.
     */
    public function edit($id_retirada)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Retirada',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Lançamentos", 'rota' => "/retiradas", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['retirada'] = $this->retirada_model->where('id_retirada', $id_retirada)->first();

        echo view('templates/header');
        echo view('retiradas/form', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $dados = $this->request->getvar();
        $this->retirada_model->save($dados);

        if(isset($dados['id_retirada']))
        {
            $session = session();
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/retiradas');
        }

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/retiradas');
    }

    /**
     * Remove o registro solicitado e retorna para a listagem.
     */
    public function delete($id_retirada)
    {
        $this->retirada_model->where('id_retirada', $id_retirada)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/retiradas');
    }
}
