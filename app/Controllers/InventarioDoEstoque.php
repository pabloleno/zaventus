<?php

namespace App\Controllers;

use App\Models\ConfigEmpresaModel;
use App\Models\InventarioDoEstoqueModel;
use App\Models\ProdutoDoInventarioModel;
use App\Models\ProdutoModel;
use CodeIgniter\Controller;

class InventarioDoEstoque extends Controller
{
    private $links;
    private $inventario_do_estoque_model;
    private $produto_model;
    private $produto_do_inventario_model;
    private $config_empresa_model;

    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.11'
        ];

        $this->inventario_do_estoque_model = new InventarioDoEstoqueModel();
        $this->produto_model               = new ProdutoModel();
        $this->produto_do_inventario_model = new ProdutoDoInventarioModel();
        $this->config_empresa_model        = new ConfigEmpresaModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Inventário do Estoque',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Inventário do Estoque", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $id_inventario = $dados['id_inventario'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            if($dados['id_inventario'] != "") // Filtra somente pelo Cód do caixa
            {
                $inventarios = $this->inventario_do_estoque_model->where('id_inventario', $id_inventario)->findAll();

                $data['id_inventario'] = $id_inventario;
            }
            else if($data_inicio != "" && $data_final != "") // Filtra em conjunto, status e data inicio e final
            {
                $inventarios = $this->inventario_do_estoque_model->where('data >=', $data_inicio)->where('data <=', $data_final)->find();

                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimos caixas cadastrados
            {
                $inventarios = $this->inventario_do_estoque_model->orderBy('id_inventario', 'DESC')->limit(5)->find();
                $data['ultimos_cinco'] = TRUE;
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $inventarios = $this->inventario_do_estoque_model->orderBy('id_inventario', 'DESC')->limit(5)->find();
            $data['ultimos_cinco'] = TRUE;
        }
        // ------------------------------------------------------------------------------------------ //

        $data['inventarios'] = $inventarios;

        echo view('templates/header');
        echo view('inventario_do_estoque/index', $data);
        echo view('templates/footer');
    }

    public function create_1()
    {
        $produtos = $this->produto_model->findAll();

        $dados = [
            'descricao' => "Inventario - Data: ".date('d/m/Y')." Hora: ".date('H:i:s'),
            'data'      => date('Y-m-d'),
            'observacoes' => "Inventário completo do estoque atual."
        ];

        $id_inventario = $this->inventario_do_estoque_model->insert($dados);

        foreach($produtos as $produto)
        {
            $this->produto_do_inventario_model->insert([
                'discriminacao'  => $produto['nome'],
                'unidade'        => $produto['unidade'],
                'quantidade'     => $produto['quantidade'],
                'valor_unitario' => $produto['valor_de_venda'],
                'id_inventario'  => $id_inventario
            ]);
        }

        $session = session();
        $session->setFlashdata('alert', 'success_create_1');

        return redirect()->to('/inventarioDoEstoque');
    }

    public function show($id_inventario)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Relatório - Registro de Inventário',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Todos Inventários", 'rota' => "/inventarioDoEstoque", 'active' => false],
            ['titulo' => "Inventário", 'rota'   => "", 'active' => true]
        ];

        $data['empresa'] = $this->config_empresa_model->where('id_config', 1)->first();
        $data['inventario'] = $this->inventario_do_estoque_model->where('id_inventario', $id_inventario)->first();
        $data['produtos_do_inventario'] = $this->produto_do_inventario_model->where('id_inventario', $id_inventario)->findAll();

        $data['id_inventario'] = $id_inventario;

        echo view('templates/header');
        echo view('inventario_do_estoque/show', $data);
        echo view('templates/footer');
    }

    public function edit($id_inventario) // Editar dados do Inventário do Estoque
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar dados do Inventário',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Todos Inventários", 'rota' => "/inventarioDoEstoque", 'active' => false],
            ['titulo' => "Editar Inventário", 'rota'   => "", 'active' => true]
        ];

        $data['inventario'] = $this->inventario_do_estoque_model->where('id_inventario', $id_inventario)->first();

        echo view('templates/header');
        echo view('inventario_do_estoque/edit', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();

        $this->inventario_do_estoque_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', "success_edit_inventario");

        return redirect()->to('/inventarioDoEstoque');
    }

    public function add($id_inventario) // Adiciona um novo produto ao inventário
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Add Produto ao Inventário',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Todos Inventários", 'rota' => "/inventarioDoEstoque", 'active' => false],
            ['titulo' => "Inventário", 'rota' => "/inventarioDoEstoque/show/$id_inventario", 'active' => false],
            ['titulo' => "Add Produto", 'rota'   => "", 'active' => true]
        ];

        $data['id_inventario'] = $id_inventario;

        echo view('templates/header');
        echo view('inventario_do_estoque/form_produto', $data);
        echo view('templates/footer');
    }

    public function listaProdutos($id_inventario) // Lista os produtos para poder excluir ou editar os registros.
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar/Excluir Produto',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Todos Inventários", 'rota' => "/inventarioDoEstoque", 'active' => false],
            ['titulo' => "Inventário", 'rota' => "/inventarioDoEstoque/show/$id_inventario", 'active' => false],
            ['titulo' => "Editar/Excluir Produto", 'rota'   => "", 'active' => true]
        ];

        $data['id_inventario'] = $id_inventario;
        $data['produtos_do_inventario'] = $this->produto_do_inventario_model->where('id_inventario', $id_inventario)->findAll();

        echo view('templates/header');
        echo view('inventario_do_estoque/lista_produtos', $data);
        echo view('templates/footer');
    }

    public function editProduto($id_inventario, $id_produto_do_inventario) // Edita o produto do inventário
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Produto do Inventário',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Todos Inventários", 'rota' => "/inventarioDoEstoque", 'active' => false],
            ['titulo' => "Inventário", 'rota' => "/inventarioDoEstoque/show/$id_inventario", 'active' => false],
            ['titulo' => "Editar/Excluir Produto", 'rota' => "/inventarioDoEstoque/listaProdutos/$id_inventario", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['produto'] = $this->produto_do_inventario_model->where('id_produto_do_inventario', $id_produto_do_inventario)->first();
        $data['id_inventario'] = $id_inventario;

        echo view('templates/header');
        echo view('inventario_do_estoque/form_produto', $data);
        echo view('templates/footer');
    }

    public function store_produto() // Store para create e edit do produto
    {
        $dados = $this->request->getvar();

        $this->produto_do_inventario_model->save($dados);

        $session = session();

        if(isset($dados['id_produto_do_inventario']))
        {
            $session->setFlashdata('alert', 'success_edit');
            return redirect()->to("/inventarioDoEstoque/listaProdutos/{$dados['id_inventario']}");
        }

        $session->setFlashdata('alert', 'success_add');
        return redirect()->to("/inventarioDoEstoque/show/{$dados['id_inventario']}");
    }

    public function delete($id_inventario) // Deleta o inventário
    {
        $this->inventario_do_estoque_model->where('id_inventario', $id_inventario)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/inventarioDoEstoque');
    }

    public function deleteProduto($id_inventario, $id_produto_do_inventario) // Deleta o produto do inventário
    {
        $this->produto_do_inventario_model->where('id_produto_do_inventario', $id_produto_do_inventario)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete_produto');

        return redirect()->to("/inventarioDoEstoque/listaProdutos/$id_inventario");
    }
}
