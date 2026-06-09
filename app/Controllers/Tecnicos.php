<?php

namespace App\Controllers;

use App\Libraries\ContatoPadrao;
use App\Libraries\EnderecoPadrao;
use App\Libraries\ImagemCadastro;
use App\Models\FuncionarioModel;
use App\Models\TecnicoModel;
use App\Models\TabelaMunicipiosIBGEModel;
use CodeIgniter\Controller;
use InvalidArgumentException;

class Tecnicos extends Controller
{
    private $links;
    private $funcionario_model;
    private $tecnico_model;
    private $tabela_municipios_ibge_model;

    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.5'
        ];

        $this->funcionario_model = new FuncionarioModel();
        $this->tecnico_model = new TecnicoModel();
        $this->tabela_municipios_ibge_model = new TabelaMunicipiosIBGEModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Técnicos',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Técnicos", 'rota'   => "", 'active' => true]
        ];

        $data['tecnicos'] = $this->tecnico_model->visiveis();

        echo view('templates/header');
        echo view('tecnicos/index', $data);
        echo view('templates/footer');
    }

    public function show($id_tecnico)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Técnico',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Técnicos", 'rota' => "/tecnicos", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['tecnico'] = $this->tecnico_model->where('id_tecnico', $id_tecnico)->first();

        echo view('templates/header');
        echo view('tecnicos/show', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        return redirect()->to('/funcionarios/create?tipo=Tecnico');
    }

    public function edit($id_tecnico)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Técnico',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Técnicos", 'rota'   => "/tecnicos", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['tecnico'] = $this->tecnico_model->where('id_tecnico', $id_tecnico)->first();

        if (empty($data['tecnico'])) {
            return redirect()->to('/tecnicos');
        }

        if (! empty($data['tecnico']['id_funcionario'])) {
            return redirect()->to('/funcionarios/edit/' . $data['tecnico']['id_funcionario']);
        }

        $data['ufs']     = EnderecoPadrao::ufs();

        echo view('templates/header');
        echo view('tecnicos/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = EnderecoPadrao::preparar($preparo['dados'], $this->tabela_municipios_ibge_model, 'uf', 'cidade');
        $dados = ContatoPadrao::sincronizarTecnico($dados);
        $tecnicoAnterior = ! empty($dados['id_tecnico'])
            ? $this->tecnico_model->where('id_tecnico', $dados['id_tecnico'])->first()
            : [];

        try {
            $fotoNova = ImagemCadastro::salvar($this->request->getFile('foto'), 'tecnicos');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('erro_foto', $e->getMessage());
        }

        if ($fotoNova !== null) {
            $dados['foto'] = $fotoNova;
        }

        $this->tecnico_model->save($dados);

        if ($fotoNova !== null) {
            ImagemCadastro::remover($tecnicoAnterior['foto'] ?? '');
        }

        // Caso a ação é editar
        if(isset($dados['id_tecnico']))
        {
            $session = session();
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/tecnicos');
        }

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/tecnicos');
    }

    public function municipiosPorUf($uf = null)
    {
        return $this->response->setJSON(
            EnderecoPadrao::municipiosPorUf($this->tabela_municipios_ibge_model, $uf)
        );
    }

    public function delete($id_tecnico)
    {
        $tecnico = $this->tecnico_model->where('id_tecnico', $id_tecnico)->first();

        if (empty($tecnico)) {
            return redirect()->to('/tecnicos');
        }

        if ($this->tecnico_model->ehGeral($tecnico)) {
            session()->setFlashdata('alert', 'error_delete_geral');

            return redirect()->to('/tecnicos');
        }

        if (! empty($tecnico['id_funcionario'])) {
            $funcionario = $this->funcionario_model->find($tecnico['id_funcionario']);
            $this->funcionario_model->update($tecnico['id_funcionario'], [
                'tipo_funcionario' => FuncionarioModel::removerAtuacao($funcionario ?? [], 'Tecnico'),
            ]);
        } else {
            ImagemCadastro::remover($tecnico['foto'] ?? '');
        }

        $this->tecnico_model->ocultar((int) $id_tecnico);
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/tecnicos');
    }
}
