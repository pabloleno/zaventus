<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\SaidaDeMercadoriaModel;
use App\Libraries\ConsumoAtendimento;
use CodeIgniter\Controller;
use InvalidArgumentException;
use Throwable;

class SaidaDeMercadorias extends Controller
{
    private $links;
    private $saida_de_mercadoria_model;
    private $produto_model;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '4.m',
            'item' => '4.0',
            'subItem' => '4.4'
        ];

        $this->produto_model = new ProdutoModel();
        $this->saida_de_mercadoria_model = new SaidaDeMercadoriaModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Saída de Mercadorias',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Saída de Mercadorias", 'rota'   => "", 'active' => true]
        ];

        $data['saida_de_mercadorias'] = $this->saida_de_mercadoria_model->select('saida_de_mercadorias.*, produtos.nome, produtos.unidade, saida_de_mercadorias.quantidade as qtd_da_saida')->join('produtos', 'produtos.id_produto = saida_de_mercadorias.id_produto')->findAll();

        echo view('templates/header');
        echo view('saida_de_mercadorias/index', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara os dados e exibe o formulario de cadastro.
     */
    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Nova Saída',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Reposições", 'rota' => "/reposicoes", 'active' => false],
            ['titulo' => "Nova", 'rota'   => "", 'active' => true]
        ];

        $data['produtos'] = $this->produto_model->where('ativo', 1)->findAll();

        echo view('templates/header');
        echo view('saida_de_mercadorias/create', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $dados = $this->request->getPost();
        if (! empty($dados['id_saida'])) {
            $saida = is_scalar($dados['id_saida']) ? $this->saida_de_mercadoria_model->find($dados['id_saida']) : null;
            if (! empty($saida['id_ordem'])) {
                return redirect()->to('/ordensDeServicos/show/' . (int) $saida['id_ordem']);
            }
            return redirect()->to('/saidaDeMercadorias')->with('erros_estoque', ['Para corrigir uma saída, estorne o registro e cadastre uma nova saída.']);
        }
        try {
            (new ConsumoAtendimento())->registrarManual($dados, (int) session()->get('id_login'), false);
        } catch (InvalidArgumentException $exception) {
            return redirect()->back()->withInput()->with('erros_estoque', [$exception->getMessage()]);
        } catch (Throwable $exception) {
            log_message('error', 'Falha ao registrar movimentação: ' . $exception->getMessage());
            return redirect()->back()->withInput()->with('erros_estoque', ['Não foi possível registrar a movimentação.']);
        }
        return redirect()->to('/saidaDeMercadorias')->with('alert', 'success_create');
    }

    public function delete($id_saida)
    {
        $saida = $this->saida_de_mercadoria_model->find($id_saida);
        if (! empty($saida['id_ordem'])) {
            return redirect()->to('/ordensDeServicos/show/' . (int) $saida['id_ordem']);
        }
        try {
            $motivo = $this->request->getPost('motivo');
            (new ConsumoAtendimento())->estornarManual((int) $id_saida, (int) session()->get('id_login'), is_string($motivo) ? $motivo : '', false);
        } catch (InvalidArgumentException $exception) {
            return redirect()->to('/saidaDeMercadorias')->with('erros_estoque', [$exception->getMessage()]);
        } catch (Throwable $exception) {
            log_message('error', 'Falha ao estornar movimentação: ' . $exception->getMessage());
            return redirect()->to('/saidaDeMercadorias')->with('erros_estoque', ['Não foi possível estornar a movimentação.']);
        }
        return redirect()->to('/saidaDeMercadorias')->with('alert', 'success_estorno');
    }
}
