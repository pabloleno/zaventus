<?php

namespace App\Controllers;

use App\Models\ContaReceberModel;
use CodeIgniter\Controller;

class ContasReceber extends Controller
{
    private $links;
    private $conta_a_receber_model;

    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.7'
        ];

        $this->conta_a_receber_model = new ContaReceberModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Contas à Receber',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Contas à Receber", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if (!empty($dados)) {
            $id_conta = $dados['id_conta'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            $status = $dados['status'];

            if ($dados['id_conta'] != "") // Filtra somente pelo Cód da conta
            {
                $contas = $this->conta_a_receber_model->where('id_conta', $id_conta)->findAll();

                $data['id_conta'] = $id_conta;
            } else if ($dados['data_inicio'] != "" && $dados['data_final'] != "" && $status == "") // Filtra somente pela data inicial e data final, quando não tem status definido
            {
                $contas = $this->conta_a_receber_model->where('data_de_vencimento >=', $data_inicio)->where('data_de_vencimento <=', $data_final)->find();

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            } else if ($status != "" && $data_inicio == "" && $data_final == "") // Filtra pelo Status, quando não tem data de inicio e fim definida
            {
                if ($status == "Todos") {
                    $contas = $this->conta_a_receber_model->findAll();
                } else if ($status == "Aberta") {
                    $contas = $this->conta_a_receber_model->where('status', 'Aberta')->findAll();
                } else if ($status == "Vencida") {
                    $contas = $this->conta_a_receber_model->where('status', 'Vencida')->findAll();
                } else if ($status == "Paga") {
                    $contas = $this->conta_a_receber_model->where('status', 'Paga')->findAll();
                }

                $data['status'] = $status;
            } else if ($status != "" && $data_inicio != "" && $data_final != "") // Filtra em conjunto, status e data inicio e final
            {
                if ($status == "Todos") // Pega todos os status (Abertas, vencidas e pagas)
                {
                    $contas = $this->conta_a_receber_model->where('data_de_vencimento >=', $data_inicio)->where('data_de_vencimento <=', $data_final)->find();
                } else // Pega de acordo com o status
                {
                    $contas = $this->conta_a_receber_model->where('status', $status)->where('data_de_vencimento >=', $data_inicio)->where('data_de_vencimento <=', $data_final)->find();
                }

                $data['status'] = $status;
                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            } else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimas contas cadastrados
            {
                $contas = $this->conta_a_receber_model->orderBy('id_conta', 'DESC')->limit(5)->find();
                $data['ultimos_cinco'] = TRUE;
            }

            $session->setFlashdata('alert', 'success_filter');
        } else {
            $contas = $this->conta_a_receber_model->orderBy('id_conta', 'DESC')->limit(5)->find();
            $data['ultimos_cinco'] = TRUE;
        }
        // ------------------------------------------------------------------------------------------ //

        $data['contas_a_receber'] = $contas;

        echo view('templates/header');
        echo view('contas_a_receber/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Conta à Receber',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Conta à Receber", 'rota' => "/contasReceber", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('contas_a_receber/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_conta)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Conta à Receber',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Conta à Receber", 'rota' => "/contasReceber", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['conta'] = $this->conta_a_receber_model->where('id_conta', $id_conta)->first();

        echo view('templates/header');
        echo view('contas_a_receber/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $this->conta_a_receber_model->save($dados);

        $session = session();
        
        // Caso a ação for editar
        if(isset($dados['id_conta']))
        {
            $session->setFlashdata('alert', 'success_edit');
            return redirect()->to('/contasReceber');
        }

        $session->setFlashdata('alert', 'success_create');
        return redirect()->to('/contasReceber');
    }

    public function delete($id_conta)
    {
        $this->conta_a_receber_model->where('id_conta', $id_conta)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/contasReceber');
    }
}
