<?php

namespace App\Controllers;

use App\Models\ReposicaoModel;
use App\Models\ProvisorioReposicaoProdutosPorXmlModel;
use App\Models\ProvisorioAddProdutoPorXmlModel;
use App\Models\CategoriasDosProdutosModel;
use App\Models\ProdutoModel;
use App\Models\FornecedorModel;
use CodeIgniter\Controller;

class Produtos extends Controller
{
    private $links;

    private $reposicao_model;
    private $provisorio_reposicao_produtos_por_xml_model;
    private $provisorio_add_produto_por_xml_model;
    private $produto_model;
    private $categoria_model;
    private $fornecedor_model;

    function __construct()
    {
        $this->links = [
            'menu' => '4.m',
            'item' => '4.0',
            'subItem' => '4.1'
        ];

        $this->reposicao_model                             = new ReposicaoModel();
        $this->provisorio_reposicao_produtos_por_xml_model = new ProvisorioReposicaoProdutosPorXmlModel();
        $this->provisorio_add_produto_por_xml_model        = new ProvisorioAddProdutoPorXmlModel();
        $this->produto_model                               = new ProdutoModel();
        $this->categoria_model                             = new CategoriasDosProdutosModel();
        $this->fornecedor_model                            = new FornecedorModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Produtos',
            'icone'  => 'fa fa-box-open'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Produtos", 'rota'   => "", 'active' => true]
        ];

        $data['produtos'] = $this->produto_model->findAll();

        echo view('templates/header');
        echo view('produtos/index', $data);
        echo view('templates/footer');
    }

    public function pesquisar()
    {
        $data['links'] = $this->links = [
            'menu' => '2.m',
            'item' => '2.0',
            'subItem' => '2.3'
        ];

        $data['titulo'] = [
            'modulo' => 'Pesquisar Produto',
            'icone'  => 'fa fa-box-open'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Pesq. Produto", 'rota'   => "", 'active' => true]
        ];

        $data['produtos'] = $this->produto_model->findAll();

        // Para pesquisa feito pelo nome
        $id_produto = $this->request->getvar('id_produto');
        if(isset($id_produto))
        {
            $data['produto_pesq'] = $this->produto_model->where('id_produto', $id_produto)->first();

            $session = session();
            $session->setFlashdata('alert', 'success_pesquisar');
        }
        
        // Para pesquisa feito pelo codigo de barras
        $codigo_de_barras = $this->request->getvar('codigo_de_barras');
        if (isset($codigo_de_barras))
        {
            $produto = $this->produto_model->where('codigo_de_barras', $codigo_de_barras)->first();

            $session = session();
            if(isset($produto['id_produto']))
            {
                $data['produto_pesq'] = $produto;
                $session->setFlashdata('alert', 'success_pesquisar');
            }
            else
            {
                $session->setFlashdata('alert', 'error_pesq_produto_por_cod_de_barras');
            }
        }

        echo view('templates/header');
        echo view('produtos/pesquisar', $data);
        echo view('templates/footer');
    }

    public function show($id_produto)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Produto',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Produtos", 'rota' => "/produtos", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['produto'] = $this->produto_model
            ->select('
                produtos.nome AS nome_do_produto,
                categorias_dos_produtos.nome AS nome_da_categoria_do_produto,
                fornecedores.nome_do_representante AS nome_do_representante,
                fornecedores.nome_da_empresa AS nome_da_empresa,
                unidade, codigo_de_barras,
                localizacao,
                quantidade,
                quantidade_minima,
                margem_de_lucro,
                valor_de_custo,
                valor_de_venda,
                lucro, arquivo,
                NCM,
                CSOSN,
                CFOP,
                validade
            ')
            ->join('categorias_dos_produtos', 'categorias_dos_produtos.id_categoria = produtos.id_categoria')
            ->join('fornecedores', 'fornecedores.id_fornecedor = produtos.id_fornecedor')
            ->where('produtos.id_produto', $id_produto)
            ->first();

        echo view('templates/header');
        echo view('produtos/show', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Produto',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Produtos", 'rota' => "/produtos", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        $data['categorias'] = $this->categoria_model->findAll();
        $data['fornecedores'] = $this->fornecedor_model->findAll();

        echo view('templates/header');
        echo view('produtos/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_produto)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Produto',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Produtos", 'rota' => "/produtos", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['produto'] = $this->produto_model->where('id_produto', $id_produto)->first();
        $data['categorias'] = $this->categoria_model->findAll();
        $data['fornecedores'] = $this->fornecedor_model->findAll();

        echo view('templates/header');
        echo view('produtos/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $file = $this->request->getFile('arquivo');
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];

        if ($file->isValid()) // Verifica se foi selecionado uma imagem, e atribui ao array o nome do arquivo depois de movido para a pasta.
        {
            if(isset($dados['id_produto'])) // Se a ação for editar, e se foi selecionado uma foto para trocar, então remove a que já existe e cadastra a nova
            {
                $produto = $this->produto_model->where('id_produto', $dados['id_produto'])->first();
                if($produto['arquivo'] != "")
                {
                    $arquivo_antigo = FCPATH . "assets/img/produtos/{$produto['arquivo']}";

                    if(is_file($arquivo_antigo))
                    {
                        unlink($arquivo_antigo);
                    }
                }
            }

            $name = $file->getRandomName();
            $file->move(FCPATH . 'assets/img/produtos', $name);

            $dados['arquivo'] = $name;
        }

        $this->produto_model->save($dados);

        $session = session();
        // Caso a ação seja editar
        if(isset($dados['id_produto']))
        {
            $session->setFlashdata('alert', 'success_edit');
            return redirect()->to("/produtos/edit/{$dados['id_produto']}");
        }

        $session->setFlashdata('alert', 'success_create');
        return redirect()->to('/produtos');
    }

    // -------------------------- CADASTRO DE PRODUTOS POR XML ------------------------------------ //

    public function add_por_xml()
    {
        // Remove todos os registros da tabela provisorio_add_produto_por_xml_model só para ter certeza que ela estará vazia
        $this->provisorio_add_produto_por_xml_model->emptyTable('provisorio_add_produto_por_xml');

        $file = $this->request->getFile('xml');
        $xml  = simplexml_load_file($file);

        $emitente_da_xml = $xml->NFe->infNFe->emit;

        $fornecedor = $this->fornecedor_model->where('cnpj', $emitente_da_xml->CNPJ)->first();

        if(empty($fornecedor)) // Verifica se o fornecedor existe, se não existir ele será cadastrado provisoriamente e espera a ação do usuário.
        {
            $id_fornecedor = $this->fornecedor_model->insert([
                'nome_do_representante' => $emitente_da_xml->xNome,
                'nome_da_empresa'       => $emitente_da_xml->xFant,
                'cnpj'                  => $emitente_da_xml->CNPJ,
                'ie'                    => $emitente_da_xml->IE,
                'cep'                   => $emitente_da_xml->enderEmit->CEP,
                'logradouro'            => $emitente_da_xml->enderEmit->xLgr,
                'numero'                => $emitente_da_xml->enderEmit->nro,
                'complemento'           => $emitente_da_xml->enderEmit->xCpl,
                'bairro'                => $emitente_da_xml->enderEmit->xBairro,
                'municipio'             => $emitente_da_xml->enderEmit->xMun,
                'comercial'             => $emitente_da_xml->enderEmit->fone,
                'anotacoes'             => "Fornecedor cadastrado por XML"
            ]);

            // Pega os dados do fornecedor cadastrado
            $fornecedor = $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->first();

            // Informa com a variável que foi cadastrado o fornecedor
            $acao_cad_fornecedor = TRUE;
        }

        foreach($xml->NFe->infNFe->det as $item)
        {
            $this->provisorio_add_produto_por_xml_model->insert([
                'nome'              => $item->prod->xProd,
                'unidade'           => $item->prod->uCom,
                'codigo_de_barras'  => $item->prod->cEAN,
                'quantidade'        => $item->prod->qCom,
                'quantidade_minima' => 1,
                'valor_de_custo'    => $item->prod->vUnCom,
                'NCM'               => $item->prod->NCM,
                'CFOP'              => $item->prod->CFOP,
                'id_categoria'      => 1,
                'id_fornecedor'     => $fornecedor['id_fornecedor']
            ]);
        }

        if(isset($acao_cad_fornecedor)) // Se foi cadastrado motra para o usuário se ele quer cadastrar esse fornecedor, se não ele remove e altera o id_funcionario
        {
            $data['links'] = $this->links;

            $data['titulo'] = [
                'modulo' => 'Editar Produto',
                'icone'  => 'fa fa-edit'
            ];

            $data['caminhos'] = [
                ['titulo' => "Dashboard", 'rota' => "/dashboard", 'active' => false],
                ['titulo' => "Produtos", 'rota' => "/produtos", 'active' => false],
                ['titulo' => "Editar", 'rota'   => "", 'active' => true]
            ];

            $data['fornecedor'] = $fornecedor;

            echo View('templates/header');
            echo View('produtos/acao_add_fornecedor_por_xml', $data);
            echo View('templates/footer');
        }
        else
        {
            return redirect()->to("/produtos/provisorio_add_produtos_por_xml");
        }
    }

    public function remove_fornecedor_cadastrado_por_xml($id_fornecedor)
    {
        // Pega todos os produtos da xml que foram cadastrados na tabela provisoria
        $produtos = $this->provisorio_add_produto_por_xml_model->findAll();

        // Altera todas os id_fornecedor para 1=fornecedor GERAL
        foreach($produtos as $produto)
        {
            $this->provisorio_add_produto_por_xml_model->save([
                'id_produto'    => $produto['id_produto'],
                'id_fornecedor' => 1 // 1=fornecedor GERAL
            ]);
        }

        // Remove o fornecedor
        $this->fornecedor_model->where('id_fornecedor', $id_fornecedor)->delete();

        return redirect()->to('/produtos/provisorio_add_produtos_por_xml');
    }

    public function provisorio_add_produtos_por_xml()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Finalize as informações dos produtos',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Produtos", 'rota' => "/produtos", 'active' => false],
            ['titulo' => "Cad. por XML", 'rota'   => "", 'active' => true]
        ];

        $data['produtos'] = $this->provisorio_add_produto_por_xml_model->findAll();

        echo View('templates/header');
        echo View('produtos/provisorio_add_produtos_por_xml', $data);
        echo View('templates/footer');
    }

    public function altera_dados_do_produto_provisorio_cad_por_xml()
    {
        $dados = $this->request->getvar();
        
        $this->provisorio_add_produto_por_xml_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_update_prod');

        return redirect()->to("/produtos/provisorio_add_produtos_por_xml/#prod_{$dados['id_produto']}");
    }

    public function finalizar_e_cadastrar_produtos_por_xml()
    {
        // Pega todos os produtos da tabela provisorio_add_produto_por_xml_model e insere na tabela de produtos
        $produtos_provisorio = $this->provisorio_add_produto_por_xml_model->findAll();

        foreach($produtos_provisorio as $produto)
        {
            unset($produto['id_produto']); // Remove o id_produto para inserir os dados
            $this->produto_model->insert($produto);
        }

        // Remove todos os registros da tabela provisorio_add_produto_por_xml_model
        $this->provisorio_add_produto_por_xml_model->emptyTable('provisorio_add_produto_por_xml');

        // Cria uma mensagem de alerta
        $session = session();
        $session->setFlashdata('alert', 'success_create_prod_por_xml');

        // Redireciona para a página de produtos
        return redirect()->to('/produtos');
    }

    // -------------------------- REPOSIÇÃO DE PRODUTOS POR XML ------------------------------------ //

    public function reposicao_por_xml()
    {
        // Remove todos os registros da tabela provisorio_reposicao_produtos_por_xml_model só para ter certeza que ela estará vazia
        $this->provisorio_reposicao_produtos_por_xml_model->emptyTable('provisorio_reposicao_produtos_por_xml');

        $file = $this->request->getFile('xml');
        $xml  = simplexml_load_file($file);

        foreach($xml->NFe->infNFe->det as $item)
        {
            $produto = $this->produto_model->where('codigo_de_barras', $item->prod->cEAN)->first();

            if(!empty($produto)) // Se o produto existir coloca o nome dele para repor
            {
                $this->provisorio_reposicao_produtos_por_xml_model->save([
                    'nome'                    => $produto['nome'],
                    'quantidade_da_reposicao' => $item->prod->qCom,
                    'id_produto'              => $produto['id_produto']
                ]);
            }
            else // Caso não exista, o usuário terá que escolher o produto para repor
            {
                $this->provisorio_reposicao_produtos_por_xml_model->save([
                    'nome'                    => $item->prod->xProd,
                    'quantidade_da_reposicao' => $item->prod->qCom,
                    'id_produto'              => 0 // Zero para informar que o produto não foi localizado
                ]);
            }
        }

        return redirect()->to('provisorio_reposicao_produtos_por_xml');
    }

    public function provisorio_reposicao_produtos_por_xml()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Produtos da Reposição',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Reposições", 'rota' => "/reposicoes", 'active' => false],
            ['titulo' => "Reposição por XML", 'rota'   => "", 'active' => true]
        ];

        $data['produtos_do_estoque'] = $this->produto_model->findAll();
        $data['produtos']            = $this->provisorio_reposicao_produtos_por_xml_model->findAll();

        echo View('templates/header');
        echo View('produtos/provisorio_reposicao_produtos_por_xml', $data);
        echo View('templates/footer');
    }

    public function altera_dados_do_produto_provisorio_reposicao_por_xml()
    {
        $dados = $this->request->getvar();
        
        $this->provisorio_reposicao_produtos_por_xml_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_update_prod');

        return redirect()->to("/produtos/provisorio_reposicao_produtos_por_xml/#prod_{$dados['id_produto']}");
    }

    public function finalizar_e_repoe_produtos_por_xml()
    {
        $produtos_provisorio = $this->provisorio_reposicao_produtos_por_xml_model->findAll();

        foreach($produtos_provisorio as $prod_prov)
        {
            $id_produto = $prod_prov['id_produto']; // Id_produto

            $produto_do_estoque = $this->produto_model->where('id_produto', $id_produto)->first(); // Pega o produto do estoque

            $quantidade = $produto_do_estoque['quantidade'] + $prod_prov['quantidade_da_reposicao']; // Soma a quantidade do produto do estoque com o da reposição

            $this->produto_model->save([
                'id_produto' => $id_produto,
                'quantidade' => $quantidade
            ]);

            $this->reposicao_model->save([
                'data'        => date('Y-m-d'),
                'hora'        => date('H:i:s'),
                'quantidade'  => $prod_prov['quantidade_da_reposicao'],
                'observacoes' => "Reposição do produto feita por XML",
                'id_produto'  => $id_produto
            ]);
        }

        $session = session();
        $session->setFlashdata('alert', 'success_reposicao_por_xml');

        return redirect()->to("/reposicoes");
    }

    // --------------------------------------------------------------------------------------------- //

    public function delete($id_produto)
    {
        $this->produto_model->where('id_produto', $id_produto)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/produtos');
    }

    public function removerImagem($id_produto)
    {
        $produto = $this->produto_model->where('id_produto', $id_produto)->first();
        $foto = $produto['arquivo'];
        $arquivo = FCPATH . "assets/img/produtos/$foto";

        $session = session();
        if($foto != "" && is_file($arquivo) && unlink($arquivo))
        {
            $this->produto_model->set('arquivo', "")->where('id_produto', $id_produto)->update();

            $session->setFlashdata('alert', 'success_remove_image');
            return redirect()->to("/produtos/edit/$id_produto");
        }

        $session->setFlashdata('alert', 'error_remove_image');

        return redirect()->to("/produtos/edit/$id_produto");
    }
}
