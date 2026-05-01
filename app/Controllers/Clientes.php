<?php

namespace App\Controllers;

use App\Libraries\ContatoPadrao;
use App\Models\TabelaMunicipiosIBGEModel;
use App\Models\OrdemDeServicoModel;
use App\Models\PagamentoDoClienteModel;
use App\Models\OrcamentoModel;
use App\Models\PedidoModel;
use App\Models\VendaModel;
use App\Models\ClienteModel;
use CodeIgniter\Controller;

class Clientes extends Controller
{
    private const CODIGOS_UF_IBGE = [
        'RO' => '11',
        'AC' => '12',
        'AM' => '13',
        'RR' => '14',
        'PA' => '15',
        'AP' => '16',
        'TO' => '17',
        'MA' => '21',
        'PI' => '22',
        'CE' => '23',
        'RN' => '24',
        'PB' => '25',
        'PE' => '26',
        'AL' => '27',
        'SE' => '28',
        'BA' => '29',
        'MG' => '31',
        'ES' => '32',
        'RJ' => '33',
        'SP' => '35',
        'PR' => '41',
        'SC' => '42',
        'RS' => '43',
        'MS' => '50',
        'MT' => '51',
        'GO' => '52',
        'DF' => '53',
    ];

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

        $data['ufs'] = $this->ufs();

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
        $data['ufs']     = $this->ufs();

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

        // Prepara dados de endereco e municipio.
        $dados = $this->prepararEnderecoCliente($dados);
        $dados = ContatoPadrao::sincronizarCliente($dados);

        $this->cliente_model->save($dados);

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
        $uf = preg_replace('/[^A-Z]/', '', strtoupper((string) $uf));

        if (! isset(self::CODIGOS_UF_IBGE[$uf])) {
            return $this->response->setJSON([]);
        }

        $prefixo = self::CODIGOS_UF_IBGE[$uf];
        $municipios = $this->tabela_municipios_ibge_model
            ->select('codigo, municipio')
            ->orderBy('municipio', 'ASC')
            ->findAll();

        $dados = [];

        foreach ($municipios as $municipio) {
            $codigo = preg_replace('/\D/', '', (string) ($municipio['codigo'] ?? ''));
            $nome = $this->limparMunicipio($municipio['municipio'] ?? '');

            if ($codigo === '' || $nome === '' || substr($codigo, 0, 2) !== $prefixo) {
                continue;
            }

            $dados[] = [
                'codigo' => $codigo,
                'municipio' => $nome,
            ];
        }

        return $this->response->setJSON($dados);
    }

    public function delete($id_cliente)
    {
        $this->cliente_model->where('id_cliente', $id_cliente)->delete();
        
        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/clientes');
    }

    private function prepararEnderecoCliente(array $dados): array
    {
        $codigo = preg_replace('/\D/', '', (string) ($dados['codigo_do_municipio'] ?? ''));
        $uf = preg_replace('/[^A-Z]/', '', strtoupper((string) ($dados['UF'] ?? '')));

        if ($codigo !== '') {
            $municipio = $this->municipioPorCodigo($codigo);
            $dados['codigo_do_municipio'] = $codigo;
            $dados['municipio'] = $municipio['municipio'] ?? $this->limparMunicipio($dados['municipio'] ?? '');
        } elseif (! empty($dados['municipio']) && strpos((string) $dados['municipio'], ';') !== false) {
            $separados = explode(';', (string) $dados['municipio'], 2);
            $dados['codigo_do_municipio'] = preg_replace('/\D/', '', $separados[0] ?? '');
            $dados['municipio'] = $this->limparMunicipio($separados[1] ?? '');
        } else {
            $dados['codigo_do_municipio'] = '';
            $dados['municipio'] = $this->limparMunicipio($dados['municipio'] ?? '');
        }

        if ($uf === '' && ! empty($dados['codigo_do_municipio'])) {
            $uf = $this->ufPorCodigoMunicipio($dados['codigo_do_municipio']) ?? '';
        }

        $dados['UF'] = $uf;

        return $dados;
    }

    private function municipioPorCodigo(string $codigo): ?array
    {
        $municipio = $this->tabela_municipios_ibge_model
            ->select('codigo, municipio')
            ->like('codigo', $codigo, 'both')
            ->first();

        if (! $municipio) {
            return null;
        }

        return [
            'codigo' => preg_replace('/\D/', '', (string) ($municipio['codigo'] ?? '')),
            'municipio' => $this->limparMunicipio($municipio['municipio'] ?? ''),
        ];
    }

    private function ufPorCodigoMunicipio(string $codigo): ?string
    {
        $prefixo = substr(preg_replace('/\D/', '', $codigo), 0, 2);

        foreach (self::CODIGOS_UF_IBGE as $uf => $codigoUf) {
            if ($codigoUf === $prefixo) {
                return $uf;
            }
        }

        return null;
    }

    private function limparMunicipio($municipio): string
    {
        return trim(str_replace(["\r", "\n"], '', (string) $municipio));
    }

    private function ufs(): array
    {
        $ufs = [];

        foreach (self::CODIGOS_UF_IBGE as $uf => $codigo) {
            $ufs[] = [
                'UF' => $uf,
                'codigo' => $codigo,
            ];
        }

        return $ufs;
    }
}
