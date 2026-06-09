<?php

namespace App\Controllers;

use App\Libraries\TipoNegocio;
use App\Models\CaixaModel;
use App\Models\LancamentoModel;
use CodeIgniter\Controller;

class Lancamentos extends Controller
{
    private $links;
    private $lancamento_model;
    private $caixa_model;

    public function __construct()
    {
        $this->links = ['menu' => '5.m', 'item' => '5.0', 'subItem' => '5.2'];
        $this->lancamento_model = new LancamentoModel();
        $this->caixa_model = new CaixaModel();
    }

    public function index()
    {
        $data = [
            'links' => $this->links,
            'titulo' => ['modulo' => 'Lancamentos', 'icone' => 'fa fa-database'],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Lancamentos', 'rota' => '', 'active' => true],
            ],
            'tipos_negocio' => TipoNegocio::opcoes(true),
        ];

        $dados = $this->request->getGet();
        $filtros = [
            'id_lancamento' => trim((string) ($dados['id_lancamento'] ?? '')),
            'tipo_negocio' => trim((string) ($dados['tipo_negocio'] ?? '')),
            'data_inicio' => trim((string) ($dados['data_inicio'] ?? '')),
            'data_final' => trim((string) ($dados['data_final'] ?? '')),
        ];
        $temFiltro = count(array_filter($filtros, static fn ($valor) => $valor !== '')) > 0;
        $query = $this->lancamento_model->orderBy('id_lancamento', 'DESC');

        if ($filtros['id_lancamento'] !== '') {
            $query->where('id_lancamento', $filtros['id_lancamento']);
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

        $data['lancamentos'] = $temFiltro ? $query->findAll() : $query->limit(5)->find();

        if ($temFiltro) {
            $data += array_filter($filtros, static fn ($valor) => $valor !== '');
            session()->setFlashdata('alert', 'success_filter');
        } else {
            $data['ultimos_cinco'] = true;
        }

        echo view('templates/header');
        echo view('lancamentos/index', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data = $this->dadosFormulario('Novo Lancamento', 'fa fa-plus-circle');
        $data['caixas'] = $this->caixa_model->where('status', 'Aberto')->findAll();

        echo view('templates/header');
        echo view('lancamentos/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_lancamento)
    {
        $data = $this->dadosFormulario('Editar Lancamento', 'fa fa-edit');
        $data['lancamento'] = $this->lancamento_model->where('id_lancamento', $id_lancamento)->first();

        echo view('templates/header');
        echo view('lancamentos/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getVar();
        $dados['tipo_negocio'] = TipoNegocio::normalizar($dados['tipo_negocio'] ?? null);
        $this->lancamento_model->save($dados);

        session()->setFlashdata('alert', isset($dados['id_lancamento']) ? 'success_edit' : 'success_create');

        return redirect()->to('/lancamentos');
    }

    public function delete($id_lancamento)
    {
        $this->lancamento_model->where('id_lancamento', $id_lancamento)->delete();
        session()->setFlashdata('alert', 'success_delete');

        return redirect()->to('/lancamentos');
    }

    private function dadosFormulario(string $modulo, string $icone): array
    {
        return [
            'links' => $this->links,
            'titulo' => ['modulo' => $modulo, 'icone' => $icone],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Lancamentos', 'rota' => '/lancamentos', 'active' => false],
                ['titulo' => 'Dados', 'rota' => '', 'active' => true],
            ],
            'tipos_negocio' => TipoNegocio::opcoes(),
        ];
    }
}
