<?php

namespace App\Controllers;

use App\Libraries\ContatoPadrao;
use App\Libraries\EnderecoPadrao;
use App\Libraries\ImagemCadastro;
use CodeIgniter\Controller;
use App\Models\FornecedorModel;
use App\Models\TabelaMunicipiosIBGEModel;
use InvalidArgumentException;

class Fornecedores extends Controller
{
    private $links;
    private $fornecedor_model;
    private $tabela_municipios_ibge_model;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.2'
        ];

        $this->fornecedor_model = new FornecedorModel();
        $this->tabela_municipios_ibge_model = new TabelaMunicipiosIBGEModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'fornecedores',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "fornecedores", 'rota'   => "", 'active' => true]
        ];

        $data['fornecedores'] = $this->fornecedor_model->findAll();

        echo view('templates/header');
        echo view('fornecedores/index', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega e exibe os detalhes do registro solicitado.
     */
    public function show($id_fornecedor)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Fornecedor',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Fornecedor", 'rota' => "/fornecedores", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['fornecedor'] = $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->first();

        echo view('templates/header');
        echo view('fornecedores/show', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara os dados e exibe o formulario de cadastro.
     */
    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Fornecedor',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Fornecedores", 'rota'   => "/fornecedores", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        $data['ufs'] = EnderecoPadrao::ufs();

        echo view('templates/header');
        echo view('fornecedores/form', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega o registro solicitado e exibe o formulario de edicao.
     */
    public function edit($id_fornecedor)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Funcionário',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Fornecedores", 'rota'   => "/fornecedores", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['fornecedor'] = $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->first();
        $data['ufs']        = EnderecoPadrao::ufs();

        echo view('templates/header');
        echo view('fornecedores/form', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = EnderecoPadrao::preparar($preparo['dados'], $this->tabela_municipios_ibge_model);
        $dados = ContatoPadrao::sincronizarFornecedor($dados);
        $fornecedorAnterior = ! empty($dados['id_fornecedor'])
            ? $this->fornecedor_model->where('id_fornecedor', $dados['id_fornecedor'])->first()
            : [];

        try {
            $fotoNova = ImagemCadastro::salvar($this->request->getFile('foto'), 'fornecedores');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('erro_foto', $e->getMessage());
        }

        if ($fotoNova !== null) {
            $dados['foto'] = $fotoNova;
        }

        $this->fornecedor_model->save($dados);

        if ($fotoNova !== null) {
            ImagemCadastro::remover($fornecedorAnterior['foto'] ?? '');
        }

        $session = session();
        
        // Caso a ação é editar
        if(isset($dados['id_fornecedor']))
        {
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/fornecedores');
        }

        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/fornecedores');
    }

    /**
     * Lista os municipios pertencentes a UF informada.
     */
    public function municipiosPorUf($uf = null)
    {
        return $this->response->setJSON(
            EnderecoPadrao::municipiosPorUf($this->tabela_municipios_ibge_model, $uf)
        );
    }

    /**
     * Remove o registro solicitado e retorna para a listagem.
     */
    public function delete($id_fornecedor)
    {
        $fornecedor = $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->first();

        if ((int) $id_fornecedor === 1) {
            session()->setFlashdata('alert', 'error_delete_padrao');

            return redirect()->to('/fornecedores');
        }

        $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->delete();
        ImagemCadastro::remover($fornecedor['foto'] ?? '');
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/fornecedores');
    }
}
