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
use App\Libraries\EnderecoPadrao;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\SystemOptions;

class Configs extends Controller
{
    private const DIRETORIO_PERSONALIZACAO = 'uploads/personalizacao';
    private const FAVICON_PADRAO = 'favicon.ico';
    private const LOGO_LOGIN_PADRAO = 'assets/img/zaventus-login-marca.png';

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
        $data['ufs']     = EnderecoPadrao::ufs();

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
            'modulo' => lang('App.system.module'),
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => lang('App.menu.home'), 'rota' => "/inicio", 'active' => false],
            ['titulo' => lang('App.menu.system'), 'rota'   => "", 'active' => true]
        ];

        $session      = session();
        $id_login     = $session->get('id_login');
        $data['tema'] = $this->login_model->where('id_login', $id_login)->first()['tema'];

        $data['formas_de_pagamento'] = $this->forma_de_pagamento_model->findAll();
        $data['config_sistema'] = $this->configSistema();
        $data['idiomas'] = config(SystemOptions::class)->languages;
        $data['fusos_horarios'] = $this->fusosHorarios();

        echo view('templates/header');
        echo view('configs/sistema', $data);
        echo view('templates/footer');
    }

    public function store_sistema()
    {
        $idioma = trim((string) $this->request->getPost('idioma'));
        $fuso_horario = trim((string) $this->request->getPost('fuso_horario'));
        $finalizacao_pdv = trim((string) $this->request->getPost('finalizacao_pdv'));
        $erros = [];

        if (! $this->idiomaValido($idioma)) {
            $erros[] = 'Selecione um idioma valido.';
        }

        if (! $this->fusoHorarioValido($fuso_horario)) {
            $erros[] = 'Selecione um fuso horario valido.';
        }

        if (! in_array($finalizacao_pdv, ['cupom_nao_fiscal', 'nfce'], true)) {
            $erros[] = 'Selecione uma forma valida para finalizar o PDV.';
        }

        if (! empty($erros)) {
            session()->setFlashdata('errors', $erros);
            session()->setFlashdata('alert', 'error_config_sistema');

            return redirect()->to('/configs/sistema')->withInput();
        }

        $this->config_empresa_model
            ->set([
                'idioma'       => $idioma,
                'fuso_horario' => $fuso_horario,
                'finalizacao_pdv' => $finalizacao_pdv,
            ])
            ->where('id_config', 1)
            ->update();

        $this->aplicarConfiguracaoSistema($idioma, $fuso_horario);

        session()->setFlashdata('alert', 'success_config_sistema');

        return redirect()->to('/configs/sistema');
    }

    public function store_personalizacao()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        $empresa = $this->config_empresa_model->where('id_config', 1)->first() ?? [];
        $arquivos = [
            'favicon' => $this->request->getFile('favicon'),
            'logo_login' => $this->request->getFile('logo_login'),
        ];
        $selecionados = [];
        $erros = [];

        foreach ($arquivos as $campo => $arquivo) {
            if (! $arquivo instanceof UploadedFile || $arquivo->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $erro = $this->validarImagemPersonalizacao($arquivo, $campo);

            if ($erro !== null) {
                $erros[] = $erro;
                continue;
            }

            $selecionados[$campo] = $arquivo;
        }

        if (empty($selecionados) && empty($erros)) {
            $erros[] = 'Selecione um favicon ou uma logo para atualizar.';
        }

        if (! empty($erros)) {
            return $this->redirecionarErroPersonalizacao($erros);
        }

        $novosCaminhos = [];

        try {
            foreach ($selecionados as $campo => $arquivo) {
                $novosCaminhos[$campo] = $this->salvarImagemPersonalizacao($arquivo, $campo);
            }

            $atualizado = $this->config_empresa_model
                ->set($novosCaminhos)
                ->where('id_config', 1)
                ->update();

            if (! $atualizado) {
                throw new \RuntimeException('Nao foi possivel salvar a personalizacao.');
            }
        } catch (\Throwable $exception) {
            foreach ($novosCaminhos as $caminho) {
                $this->removerImagemPersonalizacao($caminho);
            }

            return $this->redirecionarErroPersonalizacao([$exception->getMessage()]);
        }

        foreach ($novosCaminhos as $campo => $caminho) {
            $this->removerImagemPersonalizacao((string) ($empresa[$campo] ?? ''));
        }

        session()->set($novosCaminhos);
        session()->setFlashdata('alert', 'success_personalizacao');

        return redirect()->to('/configs/sistema');
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
        $dados = EnderecoPadrao::preparar($dados, $this->tabela_municipios_ibge_model);
        $dados['endereco'] = $this->enderecoCompleto($dados);
        $dados = $this->prepararEmpresaContatos($dados);
        $dados['id_config'] = 1; // Só tem uma configuração para a Empresa

        $this->config_empresa_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/empresa');
    }

    public function municipiosPorUf($uf = null)
    {
        return $this->response->setJSON(
            EnderecoPadrao::municipiosPorUf($this->tabela_municipios_ibge_model, $uf)
        );
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

    private function configSistema(): array
    {
        $options = config(SystemOptions::class);
        $empresa = $this->config_empresa_model->where('id_config', 1)->first() ?? [];

        $idioma = (string) ($empresa['idioma'] ?? '');
        $fuso_horario = (string) ($empresa['fuso_horario'] ?? '');

        return [
            'idioma'       => $this->idiomaValido($idioma) ? $idioma : $options->defaultLanguage,
            'fuso_horario' => $this->fusoHorarioValido($fuso_horario) ? $fuso_horario : $options->defaultTimezone,
            'favicon'       => trim((string) ($empresa['favicon'] ?? '')) ?: self::FAVICON_PADRAO,
            'logo_login'    => trim((string) ($empresa['logo_login'] ?? '')) ?: self::LOGO_LOGIN_PADRAO,
            'finalizacao_pdv' => in_array(($empresa['finalizacao_pdv'] ?? ''), ['cupom_nao_fiscal', 'nfce'], true)
                ? $empresa['finalizacao_pdv']
                : 'cupom_nao_fiscal',
        ];
    }

    private function fusosHorarios(): array
    {
        $opcoes = [];
        $options = config(SystemOptions::class);

        foreach ($options->timezones as $timezone => $nome) {
            if (! in_array($timezone, timezone_identifiers_list(), true)) {
                continue;
            }

            $opcoes[$timezone] = $this->rotuloFusoHorario($timezone, $nome);
        }

        return $opcoes;
    }

    private function rotuloFusoHorario(string $timezone, string $nome): string
    {
        $offset = (new \DateTimeImmutable('now', new \DateTimeZone($timezone)))->getOffset();
        $sinal = $offset >= 0 ? '+' : '-';
        $offset = abs($offset);

        return sprintf(
            'GMT %s%02d:%02d - %s',
            $sinal,
            (int) floor($offset / 3600),
            (int) floor(($offset % 3600) / 60),
            $nome
        );
    }

    private function idiomaValido(string $idioma): bool
    {
        return array_key_exists($idioma, config(SystemOptions::class)->languages);
    }

    private function fusoHorarioValido(string $fuso_horario): bool
    {
        return array_key_exists($fuso_horario, config(SystemOptions::class)->timezones)
            && in_array($fuso_horario, timezone_identifiers_list(), true);
    }

    private function aplicarConfiguracaoSistema(string $idioma, string $fuso_horario): void
    {
        date_default_timezone_set($fuso_horario);

        $appConfig = config(\Config\App::class);
        $appConfig->defaultLocale = $idioma;
        $appConfig->appTimezone = $fuso_horario;

        $this->request->setLocale($idioma);
        service('language')->setLocale($idioma);

        if (class_exists('\Locale')) {
            \Locale::setDefault($idioma);
        }

        session()->set([
            'idioma'       => $idioma,
            'fuso_horario' => $fuso_horario,
        ]);
    }

    private function validarImagemPersonalizacao(UploadedFile $arquivo, string $campo): ?string
    {
        $rotulo = $campo === 'favicon' ? 'favicon' : 'logo do login';

        if (! $arquivo->isValid()) {
            return sprintf('Falha no envio da %s: %s', $rotulo, $arquivo->getErrorString());
        }

        if ((int) $arquivo->getSize() > 2 * 1024 * 1024) {
            return sprintf('A %s deve ter no maximo 2 MB.', $rotulo);
        }

        $extensao = strtolower($arquivo->getClientExtension());
        $permitidas = $campo === 'favicon'
            ? ['ico', 'png']
            : ['png', 'jpg', 'jpeg', 'webp'];

        if (! in_array($extensao, $permitidas, true)) {
            return sprintf(
                'Formato invalido para %s. Use: %s.',
                $rotulo,
                implode(', ', $permitidas)
            );
        }

        if ($extensao === 'ico') {
            $cabecalho = @file_get_contents($arquivo->getTempName(), false, null, 0, 4);

            if ($cabecalho !== "\x00\x00\x01\x00") {
                return 'O arquivo selecionado nao e um favicon ICO valido.';
            }

            return null;
        }

        if (@getimagesize($arquivo->getTempName()) === false) {
            return sprintf('O arquivo selecionado nao e uma imagem valida para %s.', $rotulo);
        }

        return null;
    }

    private function salvarImagemPersonalizacao(UploadedFile $arquivo, string $campo): string
    {
        $extensao = strtolower($arquivo->getClientExtension());
        $nome = sprintf('%s-%s.%s', $campo, bin2hex(random_bytes(12)), $extensao);
        $arquivo->move(FCPATH . self::DIRETORIO_PERSONALIZACAO, $nome);

        return self::DIRETORIO_PERSONALIZACAO . '/' . $nome;
    }

    private function removerImagemPersonalizacao(string $caminho): void
    {
        $caminho = str_replace('\\', '/', trim($caminho));

        if (! str_starts_with($caminho, self::DIRETORIO_PERSONALIZACAO . '/')) {
            return;
        }

        $arquivo = realpath(FCPATH . $caminho);
        $diretorio = realpath(FCPATH . self::DIRETORIO_PERSONALIZACAO);

        if ($arquivo !== false && $diretorio !== false && str_starts_with($arquivo, $diretorio . DIRECTORY_SEPARATOR)) {
            @unlink($arquivo);
        }
    }

    private function redirecionarErroPersonalizacao(array $erros)
    {
        session()->setFlashdata('errors', $erros);
        session()->setFlashdata('alert', 'error_personalizacao');

        return redirect()->to('/configs/sistema')->withInput();
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
            'modulo' => lang('App.system.newPayment'),
            'icone'  => 'fa fa-circle-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => lang('App.menu.home'), 'rota' => "/inicio", 'active' => false],
            ['titulo' => lang('App.menu.system'), 'rota' => "/configs/sistema", 'active' => false],
            ['titulo' => lang('App.system.newPayment'), 'rota'   => "", 'active' => true]
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
            'modulo' => lang('App.system.editPayment'),
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => lang('App.menu.home'), 'rota' => "/inicio", 'active' => false],
            ['titulo' => lang('App.menu.system'), 'rota' => "/configs/sistema", 'active' => false],
            ['titulo' => lang('App.system.editPayment'), 'rota'   => "", 'active' => true]
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
