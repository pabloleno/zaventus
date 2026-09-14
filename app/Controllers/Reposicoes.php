<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\ReposicaoModel;
use App\Libraries\ConsumoAtendimento;
use CodeIgniter\Controller;
use InvalidArgumentException;
use Throwable;

class Reposicoes extends Controller
{
    private $links;
    private $reposicao_model;
    private $produto_model;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '4.m',
            'item' => '4.0',
            'subItem' => '4.3'
        ];

        $this->reposicao_model = new ReposicaoModel();
        $this->produto_model = new ProdutoModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Reposições de Produtos',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Reposições", 'rota'   => "", 'active' => true]
        ];

        $data['reposicoes'] = $this->reposicao_model->select('reposicoes.*, produtos.nome, produtos.unidade, reposicoes.quantidade as qtd_da_reposicao')->join('produtos', 'produtos.id_produto = reposicoes.id_produto')->findAll();

        echo view('templates/header');
        echo view('reposicoes/index', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara os dados e exibe o formulario de cadastro.
     */
    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Reposição',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Reposições", 'rota' => "/reposicoes", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        $data['produtos'] = $this->produto_model->where('ativo', 1)->findAll();

        echo view('templates/header');
        echo view('reposicoes/create', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $dados = $this->request->getPost();

        try {
            (new ConsumoAtendimento())->registrarManual($dados, (int) session()->get('id_login'), true);
        } catch (InvalidArgumentException $exception) {
            return redirect()->back()->withInput()->with('erros_estoque', [$exception->getMessage()]);
        } catch (Throwable $exception) {
            log_message('error', 'Falha ao registrar movimentação: ' . $exception->getMessage());
            return redirect()->back()->withInput()->with('erros_estoque', ['Não foi possível registrar a movimentação.']);
        }
        return redirect()->to('/reposicoes')->with('alert', 'success_create');
    }

    public function delete($id_reposicao)
    {

        try {
            $motivo = $this->request->getPost('motivo');
            (new ConsumoAtendimento())->estornarManual((int) $id_reposicao, (int) session()->get('id_login'), is_string($motivo) ? $motivo : '', true);
        } catch (InvalidArgumentException $exception) {
            return redirect()->to('/reposicoes')->with('erros_estoque', [$exception->getMessage()]);
        } catch (Throwable $exception) {
            log_message('error', 'Falha ao estornar movimentação: ' . $exception->getMessage());
            return redirect()->to('/reposicoes')->with('erros_estoque', ['Não foi possível estornar a movimentação.']);
        }
        return redirect()->to('/reposicoes')->with('alert', 'success_estorno');
    }
}
