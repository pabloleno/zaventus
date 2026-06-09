<?php

namespace App\Controllers;

use App\Libraries\TipoNegocio;
use App\Models\ContaPagarModel;
use CodeIgniter\Controller;

class ContasPagar extends Controller
{
    private $links;
    private $conta_a_pagar_model;

    public function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.6',
        ];

        $this->conta_a_pagar_model = new ContaPagarModel();
    }

    public function index()
    {
        $data['links'] = $this->links;
        $data['titulo'] = ['modulo' => 'Contas a Pagar', 'icone' => 'fa fa-database'];
        $data['caminhos'] = [
            ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
            ['titulo' => 'Contas a Pagar', 'rota' => '', 'active' => true],
        ];

        $filtros = $this->filtros();
        $temFiltro = count(array_filter($filtros, static fn ($valor) => $valor !== '')) > 0;
        $query = $this->conta_a_pagar_model->orderBy('id_conta', 'DESC');
        $this->aplicaFiltros($query, $filtros);

        $data['contas_a_pagar'] = $temFiltro ? $query->findAll() : $query->limit(5)->find();
        $data['tipos_negocio'] = TipoNegocio::opcoes(true);

        if ($temFiltro) {
            $data += array_filter($filtros, static fn ($valor) => $valor !== '');
            session()->setFlashdata('alert', 'success_filter');
        } else {
            $data['ultimos_cinco'] = true;
        }

        echo view('templates/header');
        echo view('contas_a_pagar/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data = $this->dadosFormulario('Nova Conta a Pagar', 'fa fa-plus-circle');

        echo view('templates/header');
        echo view('contas_a_pagar/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_conta)
    {
        $data = $this->dadosFormulario('Editar Conta a Pagar', 'fa fa-edit');
        $data['conta'] = $this->conta_a_pagar_model->where('id_conta', $id_conta)->first();

        echo view('templates/header');
        echo view('contas_a_pagar/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getVar();
        $dados['tipo_negocio'] = TipoNegocio::normalizar($dados['tipo_negocio'] ?? null);
        $this->conta_a_pagar_model->save($dados);

        session()->setFlashdata('alert', isset($dados['id_conta']) ? 'success_edit' : 'success_create');

        return redirect()->to('/contasPagar');
    }

    public function delete($id_conta)
    {
        $this->conta_a_pagar_model->where('id_conta', $id_conta)->delete();
        session()->setFlashdata('alert', 'success_delete');

        return redirect()->to('/contasPagar');
    }

    private function dadosFormulario(string $modulo, string $icone): array
    {
        return [
            'links' => $this->links,
            'titulo' => ['modulo' => $modulo, 'icone' => $icone],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Conta a Pagar', 'rota' => '/contasPagar', 'active' => false],
                ['titulo' => 'Dados', 'rota' => '', 'active' => true],
            ],
            'tipos_negocio' => TipoNegocio::opcoes(),
        ];
    }

    private function filtros(): array
    {
        $dados = $this->request->getGet();

        return [
            'id_conta' => trim((string) ($dados['id_conta'] ?? '')),
            'status' => trim((string) ($dados['status'] ?? '')),
            'tipo_negocio' => trim((string) ($dados['tipo_negocio'] ?? '')),
            'data_inicio' => trim((string) ($dados['data_inicio'] ?? '')),
            'data_final' => trim((string) ($dados['data_final'] ?? '')),
        ];
    }

    private function aplicaFiltros($query, array $filtros): void
    {
        if ($filtros['id_conta'] !== '') {
            $query->where('id_conta', $filtros['id_conta']);
        }
        if ($filtros['status'] !== '' && $filtros['status'] !== TipoNegocio::TODOS) {
            $query->where('status', $filtros['status']);
        }
        if ($filtros['tipo_negocio'] !== '' && $filtros['tipo_negocio'] !== TipoNegocio::TODOS) {
            $query->where('tipo_negocio', TipoNegocio::normalizar($filtros['tipo_negocio']));
        }
        if ($filtros['data_inicio'] !== '') {
            $query->where('data_de_vencimento >=', $filtros['data_inicio']);
        }
        if ($filtros['data_final'] !== '') {
            $query->where('data_de_vencimento <=', $filtros['data_final']);
        }
    }
}
