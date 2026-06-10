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

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    public function __construct()
    {
        $this->links = ['menu' => '5.m', 'item' => '5.0', 'subItem' => '5.2'];
        $this->lancamento_model = new LancamentoModel();
        $this->caixa_model = new CaixaModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
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

        $data['lancamentos'] = $query->findAll();

        if ($temFiltro) {
            $data += array_filter($filtros, static fn ($valor) => $valor !== '');
            session()->setFlashdata('alert', 'success_filter');
        }

        echo view('templates/header');
        echo view('lancamentos/index', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara os dados e exibe o formulario de cadastro.
     */
    public function create()
    {
        $data = $this->dadosFormulario('Novo Lancamento', 'fa fa-plus-circle');
        $data['caixas'] = $this->caixa_model->where('status', 'Aberto')->findAll();

        echo view('templates/header');
        echo view('lancamentos/form', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega o registro solicitado e exibe o formulario de edicao.
     */
    public function edit($id_lancamento)
    {
        $data = $this->dadosFormulario('Editar Lancamento', 'fa fa-edit');
        $data['lancamento'] = $this->lancamento_model->where('id_lancamento', $id_lancamento)->first();

        echo view('templates/header');
        echo view('lancamentos/form', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $dados = $this->request->getVar();
        $dados['tipo_negocio'] = TipoNegocio::normalizar($dados['tipo_negocio'] ?? null);
        $this->lancamento_model->save($dados);

        session()->setFlashdata('alert', isset($dados['id_lancamento']) ? 'success_edit' : 'success_create');

        return redirect()->to('/lancamentos');
    }

    /**
     * Remove o registro solicitado e retorna para a listagem.
     */
    public function delete($id_lancamento)
    {
        $this->lancamento_model->where('id_lancamento', $id_lancamento)->delete();
        session()->setFlashdata('alert', 'success_delete');

        return redirect()->to('/lancamentos');
    }

    /**
     * Monta os dados compartilhados pelo formulario de cadastro e edicao.
     */
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
