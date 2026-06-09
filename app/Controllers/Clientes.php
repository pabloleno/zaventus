<?php

namespace App\Controllers;

use App\Libraries\ContatoPadrao;
use App\Libraries\EnderecoPadrao;
use App\Libraries\ImagemCadastro;
use App\Models\TabelaMunicipiosIBGEModel;
use App\Models\OrdemDeServicoModel;
use App\Models\PagamentoDoClienteModel;
use App\Models\OrcamentoModel;
use App\Models\PedidoModel;
use App\Models\VendaModel;
use App\Models\ClienteModel;
use CodeIgniter\Controller;
use InvalidArgumentException;

class Clientes extends Controller
{
    private $links;
    private $cliente_model;
    private $venda_model;
    private $orcamento_model;
    private $pedido_model;
    private $pagamento_do_cliente_model;
    private $ordem_de_servico_model;
    private $tabela_municipios_ibge_model;

    function __construct()
    {
        $this->links = [
            'menu' => '3.m',
            'item' => '3.0',
            'subItem' => '3.1'
        ];

        $this->cliente_model                = new ClienteModel();
        $this->venda_model                  = new VendaModel();
        $this->orcamento_model              = new OrcamentoModel();
        $this->pedido_model                 = new PedidoModel();
        $this->pagamento_do_cliente_model   = new PagamentoDoClienteModel();
        $this->ordem_de_servico_model       = new OrdemDeServicoModel();
        $this->tabela_municipios_ibge_model = new TabelaMunicipiosIBGEModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Clientes',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Clientes", 'rota'   => "", 'active' => true]
        ];

        $data['clientes'] = $this->cliente_model->findAll();

        echo view('templates/header');
        echo view('clientes/index', $data);
        echo view('templates/footer');
    }

    public function show($id_cliente)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Cliente',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Clientes", 'rota' => "/clientes", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['cliente']            = $this->cliente_model->where('id_cliente', $id_cliente)->first();
        $data['vendas']             = $this->venda_model->where('id_cliente', $id_cliente)->find();
        $data['pagamentos']         = $this->pagamento_do_cliente_model->where('id_cliente', $id_cliente)->find();
        $data['orcamentos']         = $this->orcamento_model->where('id_cliente', $id_cliente)->find();
        $data['pedidos']            = $this->pedido_model->where('id_cliente', $id_cliente)->find();
        // $data['ordens_de_servicos'] = $this->ordem_de_servico_model->where('id_cliente', $id_cliente)->find();

        $data['ordens_de_servicos'] = $this->ordem_de_servico_model
            ->select('
                id_ordem,
                clientes.nome AS nome_do_cliente,
                data_de_entrada,
                hora_de_entrada,
                data_de_saida,
                hora_de_saida,
                situacao
            ')
            ->join('clientes', 'ordens_de_servicos.id_cliente = clientes.id_cliente')
            ->where('ordens_de_servicos.id_cliente', $id_cliente)
            ->findAll();

        echo view('templates/header');
        echo view('clientes/show', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Cliente',
            'icone'  => 'fa fa-user-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Clientes", 'rota'   => "/clientes", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        $data['ufs'] = EnderecoPadrao::ufs();

        echo view('templates/header');
        echo view('clientes/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_cliente)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Cliente',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Clientes", 'rota'   => "/clientes", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['cliente'] = $this->cliente_model->where('id_cliente', $id_cliente)->first();
        $data['ufs']     = EnderecoPadrao::ufs();

        echo view('templates/header');
        echo view('clientes/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];
        $clienteAnterior = ! empty($dados['id_cliente'])
            ? $this->cliente_model->where('id_cliente', $dados['id_cliente'])->first()
            : [];

        // Prepara dados de endereco e municipio.
        $dados = EnderecoPadrao::preparar($dados, $this->tabela_municipios_ibge_model);
        $dados = ContatoPadrao::sincronizarCliente($dados);

        try {
            $fotoNova = ImagemCadastro::salvar($this->request->getFile('foto'), 'clientes');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('erro_foto', $e->getMessage());
        }

        if ($fotoNova !== null) {
            $dados['foto'] = $fotoNova;
        }

        $this->cliente_model->save($dados);

        if ($fotoNova !== null) {
            ImagemCadastro::remover($clienteAnterior['foto'] ?? '');
        }

        // Caso a ação é editar
        if(isset($dados['id_cliente']))
        {
            $session = session();
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/clientes');
        }

        $session = session();
        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/clientes');
    }

    public function municipiosPorUf($uf = null)
    {
        return $this->response->setJSON(
            EnderecoPadrao::municipiosPorUf($this->tabela_municipios_ibge_model, $uf)
        );
    }

    public function delete($id_cliente)
    {
        $cliente = $this->cliente_model->where('id_cliente', $id_cliente)->first();

        if ((int) $id_cliente === 1) {
            session()->setFlashdata('alert', 'error_delete_padrao');

            return redirect()->to('/clientes');
        }

        $this->cliente_model->where('id_cliente', $id_cliente)->delete();
        ImagemCadastro::remover($cliente['foto'] ?? '');
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/clientes');
    }

}
