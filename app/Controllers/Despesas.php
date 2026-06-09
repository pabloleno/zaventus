<?php

namespace App\Controllers;

use App\Libraries\TipoNegocio;
use App\Models\DespesaModel;
use CodeIgniter\Controller;

class Despesas extends Controller
{
    private $links;
    private $despesa_model;

    public function __construct()
    {
        $this->links = ['menu' => '5.m', 'item' => '5.0', 'subItem' => '5.5'];
        $this->despesa_model = new DespesaModel();
    }

    public function index()
    {
        $data = [
            'links' => $this->links,
            'titulo' => ['modulo' => 'Despesas', 'icone' => 'fa fa-database'],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Despesas', 'rota' => '', 'active' => true],
            ],
            'tipos_negocio' => TipoNegocio::opcoes(true),
        ];

        $dados = $this->request->getGet();
        $filtros = [
            'id_despesa' => trim((string) ($dados['id_despesa'] ?? '')),
            'tipo' => trim((string) ($dados['tipo'] ?? '')),
            'tipo_negocio' => trim((string) ($dados['tipo_negocio'] ?? '')),
            'data_inicio' => trim((string) ($dados['data_inicio'] ?? '')),
            'data_final' => trim((string) ($dados['data_final'] ?? '')),
        ];
        $temFiltro = count(array_filter($filtros, static fn ($valor) => $valor !== '')) > 0;
        $query = $this->despesa_model->orderBy('id_despesa', 'DESC');

        if ($filtros['id_despesa'] !== '') {
            $query->where('id_despesa', $filtros['id_despesa']);
        }
        if ($filtros['tipo'] !== '' && $filtros['tipo'] !== TipoNegocio::TODOS) {
            $query->where('tipo', $filtros['tipo']);
        }
        if ($filtros['tipo_negocio'] !== '' && $filtros['tipo_negocio'] !== TipoNegocio::TODOS) {
            $query->where('tipo_negocio', TipoNegocio::normalizar($filtros['tipo_negocio']));
        }
        if ($filtros['data_inicio'] !== '') {
            $query->where('data >=', $filtros['data_inicio']);
        }
        if ($filtros['data_final'] !== '') {
            $query->where('data <=', $filtros['data_final']);
        }

        $data['despesas'] = $temFiltro ? $query->findAll() : $query->limit(5)->find();

        if ($temFiltro) {
            $data += array_filter($filtros, static fn ($valor) => $valor !== '');
            session()->setFlashdata('alert', 'success_filter');
        } else {
            $data['ultimos_cinco'] = true;
        }

        echo view('templates/header');
        echo view('despesas/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data = $this->dadosFormulario('Nova Despesa', 'fa fa-plus-circle');

        echo view('templates/header');
        echo view('despesas/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_despesa)
    {
        $data = $this->dadosFormulario('Editar Despesa', 'fa fa-edit');
        $data['despesa'] = $this->despesa_model->where('id_despesa', $id_despesa)->first();

        echo view('templates/header');
        echo view('despesas/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getVar();
        $dados['tipo_negocio'] = TipoNegocio::normalizar($dados['tipo_negocio'] ?? null);
        $this->despesa_model->save($dados);

        session()->setFlashdata('alert', isset($dados['id_despesa']) ? 'success_edit' : 'success_create');

        return redirect()->to('/despesas');
    }

    public function delete($id_despesa)
    {
        $this->despesa_model->where('id_despesa', $id_despesa)->delete();
        session()->setFlashdata('alert', 'success_delete');

        return redirect()->to('/despesas');
    }

    private function dadosFormulario(string $modulo, string $icone): array
    {
        return [
            'links' => $this->links,
            'titulo' => ['modulo' => $modulo, 'icone' => $icone],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Despesas', 'rota' => '/despesas', 'active' => false],
                ['titulo' => 'Dados', 'rota' => '', 'active' => true],
            ],
            'tipos_negocio' => TipoNegocio::opcoes(),
        ];
    }
}
