<?php

namespace App\Controllers;

use App\Libraries\ContatoPadrao;
use App\Libraries\EnderecoPadrao;
use App\Libraries\ImagemCadastro;
use CodeIgniter\Controller;
use App\Models\FuncionarioModel;
use App\Models\TabelaMunicipiosIBGEModel;
use App\Models\VendedorModel;
use InvalidArgumentException;

class Funcionarios extends Controller
{
    private $links;
    private $funcionario_model;
    private $tabela_municipios_ibge_model;
    private $vendedor_model;

    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.3'
        ];

        $this->funcionario_model = new FuncionarioModel();
        $this->tabela_municipios_ibge_model = new TabelaMunicipiosIBGEModel();
        $this->vendedor_model = new VendedorModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Funcionários',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Funcionários", 'rota'   => "", 'active' => true]
        ];

        $data['funcionarios'] = $this->funcionario_model->findAll();

        echo view('templates/header');
        echo view('funcionarios/index', $data);
        echo view('templates/footer');
    }

    public function show($id_funcionario)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Funcionário',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Funcionários", 'rota' => "/funcionarios", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['funcionario'] = $this->funcionario_model->where('id_funcionario', $id_funcionario)->first();

        echo view('templates/header');
        echo view('funcionarios/show', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Funcionário',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Funcionários", 'rota'   => "/funcionarios", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        $data['ufs'] = EnderecoPadrao::ufs();
        $data['tipo_funcionario_padrao'] = $this->tipoFuncionario($this->request->getGet('tipo') ?? 'Outros');

        echo view('templates/header');
        echo view('funcionarios/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_funcionario)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Funcionário',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Funcionários", 'rota'   => "/funcionarios", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['funcionario'] = $this->funcionario_model->where('id_funcionario', $id_funcionario)->first();
        $data['ufs']         = EnderecoPadrao::ufs();

        echo view('templates/header');
        echo view('funcionarios/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $dados['tipo_funcionario'] = $this->tipoFuncionario($dados['tipo_funcionario'] ?? 'Outros');

        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = EnderecoPadrao::preparar($preparo['dados'], $this->tabela_municipios_ibge_model);
        $dados = ContatoPadrao::sincronizarFuncionario($dados);
        $funcionarioAnterior = ! empty($dados['id_funcionario'])
            ? $this->funcionario_model->where('id_funcionario', $dados['id_funcionario'])->first()
            : [];

        try {
            $fotoNova = ImagemCadastro::salvar($this->request->getFile('foto'), 'funcionarios');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('erro_foto', $e->getMessage());
        }

        if ($fotoNova !== null) {
            $dados['foto'] = $fotoNova;
        }

        $this->funcionario_model->save($dados);
        $idFuncionario = (int) ($dados['id_funcionario'] ?? $this->funcionario_model->getInsertID());

        if ($idFuncionario > 0) {
            $this->vendedor_model->sincronizarFuncionario($dados, $idFuncionario);
        }

        if ($fotoNova !== null) {
            ImagemCadastro::remover($funcionarioAnterior['foto'] ?? '');
        }

        $session = session();

        // Caso a ação é editar
        if(isset($dados['id_funcionario']))
        {
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/funcionarios');
        }

        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/funcionarios');
    }

    public function municipiosPorUf($uf = null)
    {
        return $this->response->setJSON(
            EnderecoPadrao::municipiosPorUf($this->tabela_municipios_ibge_model, $uf)
        );
    }

    public function delete($id_funcionario)
    {
        $funcionario = $this->funcionario_model->where('id_funcionario', $id_funcionario)->first();
        $this->vendedor_model->ocultarPorFuncionario((int) $id_funcionario);
        $this->funcionario_model->where('id_funcionario', $id_funcionario)->delete();
        ImagemCadastro::remover($funcionario['foto'] ?? '');
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/funcionarios');
    }

    private function tipoFuncionario(string $tipo): string
    {
        return $tipo === 'Vendedor' ? 'Vendedor' : 'Outros';
    }
}
