<?php

namespace App\Controllers;

require_once APPPATH . 'ThirdParty/mysqldump/autoload.php';

use Ifsnop\Mysqldump as IMysqldump;

use App\Models\LoginModel;
use App\Models\ConfigEmpresaModel;
use App\Models\ConfigNFeNFCeModel;
use App\Models\ConfigNFCeModel;
use App\Models\FormaDePagamentoModel;
use App\Models\IntegracaoPagamentoModel;
use App\Models\TabelaMunicipiosIBGEModel;
use App\Libraries\EnderecoPadrao;
use App\Libraries\ProvedoresPagamento;
use App\Libraries\UploadSecurityPolicy;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\SystemOptions;

class Configs extends Controller
{
    private const DIRETORIO_PERSONALIZACAO = 'uploads/personalizacao';
    private const FAVICON_PADRAO = 'assets/img/favicon-cmy-7f5ab7a5892e.png';
    private const LOGO_LOGIN_PADRAO = 'assets/img/zaventus-logo-completa-353079a01cd5.png';

    private $config_nfe_nfce_model;
    private $config_nfce_model;
    private $config_empresa_model;
    private $forma_de_pagamento_model;
    private $integracao_pagamento_model;
    private $login_model;
    private $tabela_municipios_ibge_model;
    private UploadSecurityPolicy $upload_policy;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->config_nfe_nfce_model = new ConfigNFeNFCeModel();
        $this->config_nfce_model = new ConfigNFCeModel();
        $this->config_empresa_model = new ConfigEmpresaModel();
        $this->forma_de_pagamento_model = new FormaDePagamentoModel();
        $this->integracao_pagamento_model = new IntegracaoPagamentoModel();
        $this->login_model = new LoginModel();
        $this->tabela_municipios_ibge_model = new TabelaMunicipiosIBGEModel();
        $this->upload_policy = new UploadSecurityPolicy();
    }

    /**
     * Carrega os dados necessarios para a operacao com NFe.
     */
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

    /**
     * Valida e persiste nfe.
     */
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

        if ($file instanceof UploadedFile && $file->getError() !== UPLOAD_ERR_NO_FILE)
        {
            $errosUpload = $this->upload_policy->validateCertificate($file);

            if (! empty($errosUpload)) {
                session()->setFlashdata('errors', $errosUpload);

                return redirect()->back()->withInput();
            }

            $this->salvarCertificadoFiscal($file, 'certificado_nfe.pfx');

            $dados['certificado'] = 1;
        }

        $this->config_nfe_nfce_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/nfe');
    }

    /**
     * Carrega os dados necessarios para a operacao com NFC-e.
     */
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

    /**
     * Valida e persiste nfce.
     */
    public function store_nfce()
    {
        $file = $this->request->getFile('arquivo');
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];
        if ($file instanceof UploadedFile && $file->getError() !== UPLOAD_ERR_NO_FILE)
        {
            $errosUpload = $this->upload_policy->validateCertificate($file);

            if (! empty($errosUpload)) {
                session()->setFlashdata('errors', $errosUpload);

                return redirect()->back()->withInput();
            }

            $this->salvarCertificadoFiscal($file, 'certificado_nfce.pfx');

            $dados['certificado'] = 1;
        }

        $dados['id_config'] = 1; // Só tem uma configuração para NFe / NFCe

        $this->config_nfce_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/nfce');
    }

    /**
     * Carrega e exibe os dados configurados da empresa.
     */
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

    /**
     * Carrega e exibe as configuracoes globais do sistema.
     */
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

        $formasPagamento = $this->prepararFormasPagamentoSistema($this->forma_de_pagamento_model->comIntegracao());
        $data['formas_de_pagamento'] = $formasPagamento;
        $data['resumo_formas_pagamento'] = $this->resumoFormasPagamento($formasPagamento);
        $data['integracoes_pagamento'] = $this->prepararIntegracoesPagamentoSistema($formasPagamento);
        $data['config_sistema'] = $this->configSistema();
        $data['idiomas'] = config(SystemOptions::class)->languages;
        $data['fusos_horarios'] = $this->fusosHorarios();

        echo view('templates/header');
        echo view('configs/sistema', $data);
        echo view('templates/footer');
    }

    /**
     * Adiciona metadados de leitura para a tela operacional de pagamentos.
     */
    private function prepararFormasPagamentoSistema(array $formasPagamento): array
    {
        return array_map(function (array $forma): array {
            $tipo = $this->classificarFormaPagamento((string) ($forma['nome'] ?? ''));
            $status = $this->statusFormaPagamentoIntegracao($forma);

            $forma['tipo_pagamento_rotulo'] = $tipo['rotulo'];
            $forma['tipo_pagamento_icone'] = $tipo['icone'];
            $forma['tipo_pagamento_classe'] = $tipo['classe'];
            $forma['status_integracao_rotulo'] = $status['rotulo'];
            $forma['status_integracao_classe'] = $status['classe'];
            $forma['disponivel_produtos'] = 0;
            $forma['disponivel_servicos'] = 1;

            return $forma;
        }, $formasPagamento);
    }

    /**
     * Resume as formas cadastradas para destacar o que esta manual ou integrado.
     */
    private function resumoFormasPagamento(array $formasPagamento): array
    {
        $resumo = [
            'total' => count($formasPagamento),
            'manuais' => 0,
            'vinculadas' => 0,
            'ativas' => 0,
            'pendentes' => 0,
        ];

        foreach ($formasPagamento as $forma) {
            $temIntegracao = ! empty($forma['id_integracao']);

            if (! $temIntegracao) {
                $resumo['manuais']++;
                continue;
            }

            $resumo['vinculadas']++;

            if (($forma['status_integracao_classe'] ?? '') === 'success') {
                $resumo['ativas']++;
            } else {
                $resumo['pendentes']++;
            }
        }

        return $resumo;
    }

    /**
     * Lista provedores com status e contagem de formas vinculadas.
     */
    private function prepararIntegracoesPagamentoSistema(array $formasPagamento): array
    {
        return array_map(function (array $integracao) use ($formasPagamento): array {
            $detalhes = ProvedoresPagamento::detalhes((string) ($integracao['provedor'] ?? ''));
            $formasVinculadas = array_values(array_filter(
                $formasPagamento,
                static fn (array $forma): bool => (int) ($forma['id_integracao'] ?? 0) === (int) $integracao['id_integracao']
            ));

            $integracao['detalhes'] = $detalhes;
            $integracao['formas_vinculadas'] = count($formasVinculadas);
            $integracao['formas_servicos'] = count($formasVinculadas);
            $status = $this->statusIntegracaoPagamento($integracao, $detalhes);
            $integracao['status_integracao'] = $status['rotulo'];
            $integracao['status_classe'] = $status['classe'];

            return $integracao;
        }, $this->integracao_pagamento_model->orderBy('nome')->findAll());
    }

    /**
     * Prepara as opcoes do select de integracao usado no cadastro da forma.
     */
    private function prepararIntegracoesPagamentoFormulario(): array
    {
        return array_map(function (array $integracao): array {
            $detalhes = ProvedoresPagamento::detalhes((string) ($integracao['provedor'] ?? ''));
            $status = $this->statusIntegracaoPagamento($integracao, $detalhes);

            $integracao['detalhes'] = $detalhes;
            $integracao['status_integracao'] = $status['rotulo'];
            $integracao['status_classe'] = $status['classe'];

            return $integracao;
        }, $this->integracao_pagamento_model->orderBy('nome')->findAll());
    }

    /**
     * Classifica uma forma em uma familia visual esperada pelo usuario.
     */
    private function classificarFormaPagamento(string $nome): array
    {
        $normalizado = $this->normalizarTextoBusca($nome);

        if (str_contains($normalizado, 'pix')) {
            return ['rotulo' => 'PIX', 'icone' => 'fas fa-qrcode', 'classe' => 'info'];
        }

        if (str_contains($normalizado, 'boleto')) {
            return ['rotulo' => 'Boleto', 'icone' => 'fas fa-barcode', 'classe' => 'primary'];
        }

        if (str_contains($normalizado, 'credito') || str_contains($normalizado, 'debito') || str_contains($normalizado, 'cartao')) {
            return ['rotulo' => 'Cartao', 'icone' => 'fas fa-credit-card', 'classe' => 'success'];
        }

        if (str_contains($normalizado, 'dinheiro')) {
            return ['rotulo' => 'Dinheiro', 'icone' => 'fas fa-money-bill-wave', 'classe' => 'secondary'];
        }

        if (str_contains($normalizado, 'paypal') || str_contains($normalizado, 'carteira')) {
            return ['rotulo' => 'Carteira', 'icone' => 'fas fa-wallet', 'classe' => 'warning'];
        }

        return ['rotulo' => 'Outros', 'icone' => 'fas fa-receipt', 'classe' => 'dark'];
    }

    /**
     * Define como a forma deve apresentar sua relacao com a integracao.
     */
    private function statusFormaPagamentoIntegracao(array $forma): array
    {
        if (empty($forma['id_integracao'])) {
            return ['rotulo' => 'Manual / sem API', 'classe' => 'secondary'];
        }

        if (empty($forma['integracao_nome'])) {
            return ['rotulo' => 'Vinculo sem provedor', 'classe' => 'danger'];
        }

        if ((int) ($forma['integracao_api_publica'] ?? 0) !== 1) {
            return ['rotulo' => 'Recebimento manual', 'classe' => 'secondary'];
        }

        if ((int) ($forma['integracao_ativo'] ?? 0) === 1 && ($forma['integracao_ultimo_teste_status'] ?? '') === 'sucesso') {
            return ['rotulo' => 'Integracao ativa', 'classe' => 'success'];
        }

        if ((int) ($forma['integracao_ativo'] ?? 0) === 1) {
            return ['rotulo' => 'Teste pendente', 'classe' => 'warning'];
        }

        return ['rotulo' => 'Integracao inativa', 'classe' => 'warning'];
    }

    /**
     * Define o status geral de um provedor de pagamento.
     */
    private function statusIntegracaoPagamento(array $integracao, array $detalhes): array
    {
        if ((int) ($integracao['api_publica'] ?? 0) !== 1) {
            return ['rotulo' => 'Manual sem API', 'classe' => 'secondary'];
        }

        if (! ($detalhes['ativacao_suportada'] ?? false)) {
            return ['rotulo' => 'Conector pendente', 'classe' => 'warning'];
        }

        if ((int) ($integracao['ativo'] ?? 0) !== 1) {
            return ['rotulo' => 'Inativa', 'classe' => 'secondary'];
        }

        if (($integracao['ultimo_teste_status'] ?? '') === 'sucesso') {
            return ['rotulo' => 'Ativa e validada', 'classe' => 'success'];
        }

        return ['rotulo' => 'Teste pendente', 'classe' => 'warning'];
    }

    /**
     * Normaliza texto apenas para busca por termos conhecidos.
     */
    private function normalizarTextoBusca(string $texto): string
    {
        $texto = trim($texto);
        $texto = function_exists('mb_strtolower') ? mb_strtolower($texto, 'UTF-8') : strtolower($texto);

        return strtr($texto, [
            'á' => 'a',
            'à' => 'a',
            'ã' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'í' => 'i',
            'ì' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ó' => 'o',
            'ò' => 'o',
            'õ' => 'o',
            'ô' => 'o',
            'ö' => 'o',
            'ú' => 'u',
            'ù' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ç' => 'c',
        ]);
    }

    /**
     * Valida e persiste sistema.
     */
    public function store_sistema()
    {
        $idioma = trim((string) $this->request->getPost('idioma'));
        $fuso_horario = trim((string) $this->request->getPost('fuso_horario'));
        $erros = [];

        if (! $this->idiomaValido($idioma)) {
            $erros[] = 'Selecione um idioma valido.';
        }

        if (! $this->fusoHorarioValido($fuso_horario)) {
            $erros[] = 'Selecione um fuso horario valido.';
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
            ])
            ->where('id_config', 1)
            ->update();

        $this->aplicarConfiguracaoSistema($idioma, $fuso_horario);

        session()->setFlashdata('alert', 'success_config_sistema');

        return redirect()->to('/configs/sistema');
    }

    /**
     * Valida e persiste personalizacao.
     */
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

    /**
     * Atualiza tema.
     */
    public function alteraTema()
    {
        $tema = $this->request->getvar('tema');
        
        $session  = session();
        $id_login = $session->get('id_login');
        $this->login_model->set('tema', $tema)->where('id_login', $id_login)->update();

        return redirect()->to('/login/logout');
    }

    /**
     * Valida e persiste empresa.
     */
    public function store_empresa()
    {
        $dados = $this->request->getvar();
        $email = $this->request->getPost('email') ?? '';
        $condicoes = $this->request->getPost('condicoes_orcamento') ?? '';
        unset($dados['email'], $dados['condicoes_orcamento']);
        if (! is_string($email) || ! is_string($condicoes)
            || mb_strlen($email) > 128 || ($email !== '' && filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false)
            || mb_strlen($condicoes) > 8000) {
            session()->setFlashdata('errors', ['Informe um e-mail valido e condicoes do orcamento com ate 8000 caracteres.']);
            return redirect()->to('/configs/empresa')->withInput();
        }
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];
        $dados = EnderecoPadrao::preparar($dados, $this->tabela_municipios_ibge_model);
        $dados['endereco'] = $this->enderecoCompleto($dados);
        $dados = $this->prepararEmpresaContatos($dados);
        $dados['id_config'] = 1; // Só tem uma configuração para a Empresa
        foreach (['email' => trim($email), 'condicoes_orcamento' => trim($condicoes)] as $campo => $valor) {
            if (db_connect()->fieldExists($campo, 'config_empresa')) {
                if ($this->request->getPost($campo) !== null) {
                    $dados[$campo] = $valor;
                }
            } else {
                unset($dados[$campo]);
            }
        }

        $this->config_empresa_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/empresa');
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
     * Prepara empresa contatos.
     */
    private function prepararEmpresaContatos(array $dados): array
    {
        $telefoneFixo = $dados['telefone_fixo'] ?? '';
        $celular = $dados['celular'] ?? '';
        $whatsapp = $dados['whatsapp'] ?? '';

        $dados['telefone'] = $telefoneFixo !== '' ? $telefoneFixo : ($celular !== '' ? $celular : $whatsapp);

        return $dados;
    }

    /**
     * Monta o endereco de completo.
     */
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

    /**
     * Carrega as opcoes globais usadas pela configuracao do sistema.
     */
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
        ];
    }

    /**
     * Retorna os fusos horarios disponiveis para configuracao.
     */
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

    /**
     * Monta o rotulo legivel de um fuso horario.
     */
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

    /**
     * Informa se o idioma selecionado e suportado.
     */
    private function idiomaValido(string $idioma): bool
    {
        return array_key_exists($idioma, config(SystemOptions::class)->languages);
    }

    /**
     * Informa se o fuso horario selecionado e suportado.
     */
    private function fusoHorarioValido(string $fuso_horario): bool
    {
        return array_key_exists($fuso_horario, config(SystemOptions::class)->timezones)
            && in_array($fuso_horario, timezone_identifiers_list(), true);
    }

    /**
     * Aplica configuracao sistema.
     */
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

    /**
     * Valida imagem personalizacao.
     */
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

    /**
     * Valida e armazena uma imagem de personalizacao da empresa.
     */
    private function salvarImagemPersonalizacao(UploadedFile $arquivo, string $campo): string
    {
        $extensao = strtolower($arquivo->getClientExtension());
        $nome = sprintf('%s-%s.%s', $campo, bin2hex(random_bytes(12)), $extensao);
        $arquivo->move(FCPATH . self::DIRETORIO_PERSONALIZACAO, $nome);

        return self::DIRETORIO_PERSONALIZACAO . '/' . $nome;
    }

    /**
     * Salva certificado fiscal validado fora do diretorio publico.
     */
    private function salvarCertificadoFiscal(UploadedFile $arquivo, string $nome): void
    {
        $diretorio = WRITEPATH . 'uploads';

        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $local = $diretorio . DIRECTORY_SEPARATOR . $nome;

        if (is_file($local)) {
            @unlink($local);
        }

        $arquivo->move($diretorio, $nome, true);
    }

    /**
     * Remove imagem personalizacao.
     */
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

    /**
     * Retorna ao formulario exibindo o erro de personalizacao encontrado.
     */
    private function redirecionarErroPersonalizacao(array $erros)
    {
        session()->setFlashdata('errors', $erros);
        session()->setFlashdata('alert', 'error_personalizacao');

        return redirect()->to('/configs/sistema')->withInput();
    }

    // ------------------------------ FORMA DE PAGAMENTO -------------------------------- //
    /**
     * Prepara e exibe o formulario de uma nova forma de pagamento.
     */
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
        $data['integracoes_pagamento'] = $this->prepararIntegracoesPagamentoFormulario();

        echo view('templates/header');
        echo view('configs/form_forma_de_pagamento', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega e exibe a forma de pagamento solicitada para edicao.
     */
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
        $data['integracoes_pagamento'] = $this->prepararIntegracoesPagamentoFormulario();

        echo view('templates/header');
        echo view('configs/form_forma_de_pagamento', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste forma de pagamento.
     */
    public function store_forma_de_pagamento()
    {
        $dados = $this->request->getvar();
        $preparo = prepara_campos_padrao($dados);

        if (! empty($preparo['erros'])) {
            return redireciona_erros_campos_padrao($preparo['erros']);
        }

        $dados = $preparo['dados'];
        $dados['nome'] = trim((string) ($dados['nome'] ?? ''));
        // Campos legados permanecem apenas por compatibilidade com o schema.
        $dados['codigo_nfce'] = '99';
        $dados['id_integracao'] = ! empty($dados['id_integracao']) ? (int) $dados['id_integracao'] : null;
        $dados['disponivel_produtos'] = 0;
        $dados['disponivel_servicos'] = 1;

        if ($dados['nome'] === '') {
            session()->setFlashdata('errors', ['Informe o nome da forma de pagamento.']);

            return redirect()->back()->withInput();
        }

        if ($dados['id_integracao'] !== null && ! $this->integracao_pagamento_model->find($dados['id_integracao'])) {
            session()->setFlashdata('errors', ['Selecione um provedor de pagamento valido.']);

            return redirect()->back()->withInput();
        }

        $formaMesmoNome = $this->forma_de_pagamento_model->where('nome', $dados['nome'])->first();
        if ($formaMesmoNome && (int) $formaMesmoNome['id_forma'] !== (int) ($dados['id_forma'] ?? 0)) {
            session()->setFlashdata('errors', ['Ja existe uma forma de pagamento com este nome.']);

            return redirect()->back()->withInput();
        }

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

    /**
     * Remove forma de pagamento.
     */
    public function delete_forma_de_pagamento($id_forma)
    {
        $this->forma_de_pagamento_model->where('id_forma', $id_forma)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete_forma_de_pagamento');

        return redirect()->to('/configs/sistema');
    }

    /**
     * Gera o backup de data base.
     */
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
