<?php

namespace App\Controllers;

require_once APPPATH . 'ThirdParty/mysqldump/autoload.php';

use Ifsnop\Mysqldump as IMysqldump;

use App\Models\LoginModel;
use App\Models\ConfigEmpresaModel;
use App\Models\ConfigNFeNFCeModel;
use App\Models\ConfigNFCeModel;
use App\Models\FormaDePagamentoModel;
use App\Models\TabelaMunicipiosIBGEModel;
use CodeIgniter\Controller;

class Configs extends Controller
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

    private $config_nfe_nfce_model;
    private $config_nfce_model;
    private $config_empresa_model;
    private $forma_de_pagamento_model;
    private $login_model;
    private $tabela_municipios_ibge_model;

    function __construct()
    {
        $this->config_nfe_nfce_model = new ConfigNFeNFCeModel();
        $this->config_nfce_model = new ConfigNFCeModel();
        $this->config_empresa_model = new ConfigEmpresaModel();
        $this->forma_de_pagamento_model = new FormaDePagamentoModel();
        $this->login_model = new LoginModel();
        $this->tabela_municipios_ibge_model = new TabelaMunicipiosIBGEModel();
    }

    public function nfe()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.1'
        ];

        $data['titulo'] = [
            'modulo' => 'Config. NFe',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "NFe", 'rota'   => "", 'active' => true]
        ];

        $data['dados'] = $this->config_nfe_nfce_model->where('id_config', 1)->first();

        echo view('templates/header');
        echo view('configs/nfe', $data);
        echo view('templates/footer');
    }

    public function store_nfe()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];
        $dados['id_config'] = 1; // Só tem uma configuração para NFe / NFCe

        $file = $this->request->getFile('arquivo');

        if ($file->isValid()) // Verifica se foi selecionado o certificado.
        {
            $local = WRITEPATH . "uploads\certificado_nfe.pfx";
            unlink($local);
            $file->store('../../writable/uploads/', "certificado_nfe.pfx");

            $dados['certificado'] = 1;
        }

        $this->config_nfe_nfce_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/nfe');
    }

    public function nfce()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.2'
        ];

        $data['titulo'] = [
            'modulo' => 'Config. NFCe',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "NFCe", 'rota'   => "", 'active' => true]
        ];

        $data['dados'] = $this->config_nfce_model->where('id_config', 1)->first();

        echo view('templates/header');
        echo view('configs/nfce', $data);
        echo view('templates/footer');
    }

    public function store_nfce()
    {
        $file = $this->request->getFile('arquivo');
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];
        if ($file->isValid()) // Verifica se foi selecionado o certificado.
        {
            $local = WRITEPATH . "uploads\certificado_nfce.pfx";
            unlink($local);
            $file->store('../../writable/uploads/', "certificado_nfce.pfx");

            $dados['certificado'] = 1;
        }

        $dados['id_config'] = 1; // Só tem uma configuração para NFe / NFCe

        $this->config_nfce_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/nfce');
    }

    public function empresa()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.3'
        ];

        $data['titulo'] = [
            'modulo' => 'Config. Empresa',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Empresa", 'rota'   => "", 'active' => true]
        ];

        $data['empresa'] = $this->config_empresa_model->where('id_config', 1)->first();
        $data['ufs']     = $this->ufs();

        echo view('templates/header');
        echo view('configs/empresa', $data);
        echo view('templates/footer');
    }

    public function sistema()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.4'
        ];

        $data['titulo'] = [
            'modulo' => 'Config. Sistema',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Sistema", 'rota'   => "", 'active' => true]
        ];

        $session      = session();
        $id_login     = $session->get('id_login');
        $data['tema'] = $this->login_model->where('id_login', $id_login)->first()['tema'];

        $data['formas_de_pagamento'] = $this->forma_de_pagamento_model->findAll();

        echo view('templates/header');
        echo view('configs/sistema', $data);
        echo view('templates/footer');
    }

    public function alteraTema()
    {
        $tema = $this->request->getvar('tema');
        
        $session  = session();
        $id_login = $session->get('id_login');
        $this->login_model->set('tema', $tema)->where('id_login', $id_login)->update();

        return redirect()->to('/login/logout');
    }

    public function store_empresa()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];
        $dados = $this->prepararEmpresaEndereco($dados);
        $dados = $this->prepararEmpresaContatos($dados);
        $dados['id_config'] = 1; // Só tem uma configuração para a Empresa

        $this->config_empresa_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/empresa');
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

    private function prepararEmpresaEndereco(array $dados): array
    {
        $codigo = preg_replace('/\D/', '', (string) ($dados['codigo_do_municipio'] ?? ''));
        $uf = preg_replace('/[^A-Z]/', '', strtoupper((string) ($dados['UF'] ?? '')));

        if ($codigo !== '') {
            $municipio = $this->municipioPorCodigo($codigo);
            $dados['codigo_do_municipio'] = $codigo;
            $dados['municipio'] = $municipio['municipio'] ?? $this->limparMunicipio($dados['municipio'] ?? '');
        } else {
            $dados['codigo_do_municipio'] = '';
            $dados['municipio'] = $this->limparMunicipio($dados['municipio'] ?? '');
        }

        if ($uf === '' && ! empty($dados['codigo_do_municipio'])) {
            $uf = $this->ufPorCodigoMunicipio($dados['codigo_do_municipio']) ?? '';
        }

        $dados['UF'] = $uf;
        $dados['endereco'] = $this->enderecoCompleto($dados);

        return $dados;
    }

    private function prepararEmpresaContatos(array $dados): array
    {
        $telefoneFixo = $dados['telefone_fixo'] ?? '';
        $celular = $dados['celular'] ?? '';
        $whatsapp = $dados['whatsapp'] ?? '';

        $dados['telefone'] = $telefoneFixo !== '' ? $telefoneFixo : ($celular !== '' ? $celular : $whatsapp);

        return $dados;
    }

    private function enderecoCompleto(array $dados): string
    {
        $partes = [
            $dados['logradouro'] ?? '',
            $dados['numero'] ?? '',
            $dados['complemento'] ?? '',
            $dados['bairro'] ?? '',
            $dados['municipio'] ?? '',
            $dados['UF'] ?? '',
            $dados['cep'] ?? '',
        ];

        $partes = array_filter(array_map('trim', $partes));

        return substr(implode(', ', $partes), 0, 128);
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

    // ------------------------------ FORMA DE PAGAMENTO -------------------------------- //
    public function createFormaDePagamento()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.4'
        ];

        $data['titulo'] = [
            'modulo' => 'Nova Forma de Pagamento',
            'icone'  => 'fa fa-circle-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Sistema", 'rota' => "/configs/sistema", 'active' => false],
            ['titulo' => "Nova Forma de Pagamento", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('configs/form_forma_de_pagamento', $data);
        echo view('templates/footer');
    }

    public function editFormaDePagamento($id_forma)
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.4'
        ];

        $data['titulo'] = [
            'modulo' => 'Editar Forma de Pagamento',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Sistema", 'rota' => "/configs/sistema", 'active' => false],
            ['titulo' => "Nova Forma de Pagamento", 'rota'   => "", 'active' => true]
        ];

        $data['forma_de_pagamento'] = $this->forma_de_pagamento_model->where('id_forma', $id_forma)->first();

        echo view('templates/header');
        echo view('configs/form_forma_de_pagamento', $data);
        echo view('templates/footer');
    }

    public function store_forma_de_pagamento()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];

        $this->forma_de_pagamento_model->save($dados);

        $session = session();

        if(isset($dados['id_forma']))
        {
            $session->setFlashdata('alert', 'success_edit_forma_de_pagamento');
            return redirect()->to('/configs/sistema');
        }

        $session->setFlashdata('alert', 'success_create_forma_de_pagamento');
        return redirect()->to('/configs/sistema');
    }

    public function delete_forma_de_pagamento($id_forma)
    {
        $this->forma_de_pagamento_model->where('id_forma', $id_forma)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete_forma_de_pagamento');

        return redirect()->to('/configs/sistema');
    }

    public function backupDataBase()
    {
        try {
            $database = config('Database')->default;
            $dsn = "mysql:host={$database['hostname']};dbname={$database['database']}";

            if (!empty($database['port'])) {
                $dsn .= ";port={$database['port']}";
            }

            if (!empty($database['charset'])) {
                $dsn .= ";charset={$database['charset']}";
            }

            $dump = new IMysqldump\Mysqldump($dsn, $database['username'], $database['password']);
            $dump->start(WRITEPATH.'backup_mysql/BACKUP_DATABASE_SISTEMA.sql');

            header("Content-Type: application/sql");
            // informa o tipo do arquivo ao navegador
            header("Content-Length: " . filesize(WRITEPATH . 'backup_mysql/BACKUP_DATABASE_SISTEMA.sql'));
            // informa o tamanho do arquivo ao navegador
            header("Content-Disposition: attachment; filename=" . basename(WRITEPATH . 'backup_mysql/BACKUP_DATABASE_SISTEMA.sql'));
            // informa ao navegador que é tipo anexo e faz abrir a janela de download, 
            //tambem informa o nome do arquivo
            readfile(WRITEPATH . 'backup_mysql/BACKUP_DATABASE_SISTEMA.sql'); // lê o arquivo
            exit; // aborta pós-ações

            $session = session();
            $session->setFlashdata('alert', 'success_bkp_database');
            
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }
}
