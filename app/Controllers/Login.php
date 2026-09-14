<?php

namespace App\Controllers;

use App\Libraries\ImagemCadastro;
use App\Libraries\LoginAttemptLimiter;
use App\Models\ConfigEmpresaModel;
use App\Models\LoginModel;
use CodeIgniter\Controller;
use Config\SystemOptions;
use InvalidArgumentException;

class Login extends Controller
{
    private $links;
    private $empresa_model;
    private $login_model;
    private LoginAttemptLimiter $login_attempt_limiter;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.5'
        ];

        $this->empresa_model = new ConfigEmpresaModel();
        $this->login_model = new LoginModel();
        $this->login_attempt_limiter = new LoginAttemptLimiter();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['empresa'] = $this->empresa_model->where('id_config', 1)->first();

        echo view('login/index', $data);
    }

    /**
     * Carrega e exibe os usuarios e suas permissoes.
     */
    public function usuarios()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Usuários',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Lançamentos", 'rota'   => "", 'active' => true]
        ];

        $data['usuarios'] = $this->login_model->findAll();

        echo view('templates/header');
        echo view('login/usuarios', $data);
        echo view('templates/footer');
    }

    /**
     * Prepara os dados e exibe o formulario de cadastro.
     */
    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Usuário',
            'icone'  => 'fa fa-circle-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Usuários", 'rota' => "/login/usuarios", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('login/form', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega o registro solicitado e exibe o formulario de edicao.
     */
    public function edit($id_login)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Usuário',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Usuários", 'rota' => "/login/usuarios", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['usuario'] = $this->login_model->where('id_login', $id_login)->first();

        echo view('templates/header');
        echo view('login/form', $data);
        echo view('templates/footer');
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $dados = $this->request->getPost();

        $permitir = static function (array $dados, string $modulo, string $permissao): int {
            if (!isset($dados[$modulo])) {
                return 0;
            }

            return ((string) ($dados[$permissao] ?? '0') === '1') ? 1 : 0;
        };

        $dados['controle_de_acesso'] = json_encode([
            'vendas' => [
                'modulo'          => isset($dados['modulo_vendas']) ? 1 : 0,
                'venda_rapida'    => 0,
                'pdv'             => 0,
                'pesq_produto'    => 0,
                'hist_de_vendas'  => $permitir($dados, 'modulo_vendas', 'hist_de_vendas'),
            ],
            'controle_geral' => [
                'modulo'       => isset($dados['modulo_controle_geral']) ? 1 : 0,
                'clientes'     => $permitir($dados, 'modulo_controle_geral', 'clientes'),
                'cobrancas'    => $permitir($dados, 'modulo_controle_geral', 'cobrancas'),
                'fornecedores' => $permitir($dados, 'modulo_controle_geral', 'fornecedores'),
                'funcionarios' => $permitir($dados, 'modulo_controle_geral', 'funcionarios'),
                'vendedores'   => $permitir($dados, 'modulo_controle_geral', 'vendedores'),
            ],
            'estoque' => [
                'modulo'                => isset($dados['modulo_estoque']) ? 1 : 0,
                'produtos'              => $permitir($dados, 'modulo_estoque', 'produtos'),
                'reposicoes'            => $permitir($dados, 'modulo_estoque', 'reposicoes'),
                'saida_de_mercadorias'  => $permitir($dados, 'modulo_estoque', 'saida_de_mercadorias'),
                'categorias_do_produto' => $permitir($dados, 'modulo_estoque', 'categorias_do_produto'),
            ],
            'financeiro' => [
                'modulo'                => isset($dados['modulo_financeiro']) ? 1 : 0,
                'caixas'                => $permitir($dados, 'modulo_financeiro', 'caixas'),
                'lancamentos'           => $permitir($dados, 'modulo_financeiro', 'lancamentos'),
                'retiradas_do_caixa'    => $permitir($dados, 'modulo_financeiro', 'retiradas_do_caixa'),
                'despesas'              => $permitir($dados, 'modulo_financeiro', 'despesas'),
                'contas_a_pagar'        => $permitir($dados, 'modulo_financeiro', 'contas_a_pagar'),
                'contas_a_receber'      => $permitir($dados, 'modulo_financeiro', 'contas_a_receber'),
                'orcamentos'            => 0,
                'pedidos'               => 0,
                'relatorio_dre'         => $permitir($dados, 'modulo_financeiro', 'relatorio_dre'),
                'inventario_do_estoque' => $permitir($dados, 'modulo_financeiro', 'inventario_do_estoque'),
                'controle_fiscal'       => 0,
            ],
            'relatorios' => [
                'modulo'     => isset($dados['modulo_relatorios']) ? 1 : 0,
                'vendas'     => $permitir($dados, 'modulo_relatorios', 'vendas'),
                'estoque'    => $permitir($dados, 'modulo_relatorios', 'estoque'),
                'financeiro' => $permitir($dados, 'modulo_relatorios', 'financeiro'),
                'geral'      => $permitir($dados, 'modulo_relatorios', 'geral'),
            ],
            'configs' => [
                'modulo'          => isset($dados['modulo_configs']) ? 1 : 0,
                'nfe'             => 0,
                'nfce'            => 0,
                'empresa'         => $permitir($dados, 'modulo_configs', 'empresa'),
                'sistema'         => $permitir($dados, 'modulo_configs', 'sistema'),
                'desenvolvedor'   => $permitir($dados, 'modulo_configs', 'desenvolvedor'),
                'usuarios'        => $permitir($dados, 'modulo_configs', 'usuarios'),
                'backup_de_dados' => $permitir($dados, 'modulo_configs', 'backup_de_dados'),
            ],
        ]);
        $editando = isset($dados['id_login']) && $dados['id_login'] !== '';
        $usuarioAnterior = $editando
            ? $this->login_model->where('id_login', $dados['id_login'])->first()
            : [];

        if (!empty($dados['senha'])) {
            $dados['senha'] = password_hash((string) $dados['senha'], PASSWORD_BCRYPT);
        } elseif ($editando) {
            unset($dados['senha']);
        } else {
            session()->setFlashdata('alert', 'error_password_required');

            return redirect()->back()->withInput();
        }

        if (!$editando) {
            $dados['tema'] = 0;
        } elseif (!isset($dados['tema'])) {
            unset($dados['tema']);
        }

        try {
            $fotoNova = ImagemCadastro::salvar($this->request->getFile('foto'), 'usuarios');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('erro_foto', $e->getMessage());
        }

        if ($fotoNova !== null) {
            $dados['foto'] = $fotoNova;
        } elseif (! empty($usuarioAnterior['foto'])) {
            $dados['foto'] = $usuarioAnterior['foto'];
        }

        $dados = array_intersect_key($dados, array_flip([
            'id_login',
            'usuario',
            'senha',
            'primeiro_nome',
            'foto',
            'ultimo_acesso',
            'tema',
            'controle_de_acesso',
        ]));

        $this->login_model->save($dados);
        $idLoginSalvo = (int) ($dados['id_login'] ?? $this->login_model->getInsertID());

        if ($fotoNova !== null) {
            ImagemCadastro::remover($usuarioAnterior['foto'] ?? '');
        }

        $session = session();

        if ((int) $session->get('id_login') === $idLoginSalvo) {
            $session->set('usuario', $dados['usuario'] ?? $session->get('usuario'));
            $session->set('primeiro_nome', $dados['primeiro_nome'] ?? $session->get('primeiro_nome'));
            $session->set('foto', $dados['foto'] ?? '');
            $session->set('controle_de_acesso', $dados['controle_de_acesso'] ?? $session->get('controle_de_acesso'));
        }

        // Se o usuário estiver editando
        if($editando)
        {
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/login/usuarios');
        }

        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/login/usuarios');
    }

    /**
     * Valida as credenciais e inicia a sessao do usuario.
     */
    public function autenticar()
    {
        if (! $this->request->is('post')) {
            return $this->response
                ->setStatusCode(405)
                ->setHeader('Allow', 'POST')
                ->setBody('Metodo nao permitido para esta acao.');
        }

        $dados = $this->request->getPost();
        $usuario = (string) ($dados['usuario'] ?? '');
        $senha = (string) ($dados['senha'] ?? '');
        $ipAddress = (string) $this->request->getIPAddress();
        $session = session();

        if (! $this->login_attempt_limiter->canAttempt($ipAddress, $usuario)) {
            $retryAfter = max(1, $this->login_attempt_limiter->retryAfterSeconds());

            $session->setFlashdata('alert', 'error_too_many_login_attempts');
            $session->setFlashdata('retry_after', $retryAfter);

            return redirect()
                ->to('/login')
                ->setHeader('Retry-After', (string) $retryAfter);
        }

        $login = $this->login_model->where('usuario', $usuario)->first();
        if(!empty($login) && $this->senhaConfere($senha, (string) $login['senha']))
        {
            $empresa = $this->empresa_model->where('id_config', 1)->first();
            $this->login_attempt_limiter->clear($ipAddress, $usuario);
            $session->regenerate(true);

            // Alerta de succeso de autenticação
            $session->setFlashdata('alert', 'success_autentication');

            // Insere variáveis na sessão
            $session->set('id_login', $login['id_login']);
            $session->set('usuario', $login['usuario']);
            $session->set('primeiro_nome', $login['primeiro_nome']);
            $session->set('foto', $login['foto'] ?? '');
            $session->set('nome_fantasia', $empresa['nome_fantasia']);
            $session->set('tema', ((int) ($login['tema'] ?? 0) === 1) ? 1 : 0);
            $session->set('controle_de_acesso', $login['controle_de_acesso']);
            $session->set('idioma', $empresa['idioma'] ?? config(SystemOptions::class)->defaultLanguage);
            $session->set('fuso_horario', $empresa['fuso_horario'] ?? config(SystemOptions::class)->defaultTimezone);
            $session->set('favicon', trim((string) ($empresa['favicon'] ?? '')) ?: 'assets/img/favicon-cmy-7f5ab7a5892e.png');
            $session->set('logo_login', trim((string) ($empresa['logo_login'] ?? '')) ?: 'assets/img/zaventus-logo-completa-353079a01cd5.png');

            if ($this->senhaPrecisaAtualizar((string) $login['senha'])) {
                $this->login_model->update($login['id_login'], [
                    'senha' => password_hash($senha, PASSWORD_BCRYPT),
                ]);
            }

            // Guarda o último acesso do usuário
            // $ultimo_acesso = date('d/m/Y') . " às " . date('H:i:s');
            // $this->login_model->set('ultimo_acesso', $ultimo_acesso)->where('id_login', $session->get('id_login'))->update();

            // Redireciona para a Dashboard do sistema
            return redirect()->to('/inicio');
        }
        else
        {
            // Informa que os dados estão errados
            $session->setFlashdata('alert', 'error_autentication');

            // Retorna para o login
            return redirect()->to('/login');
        }
    }

    /**
     * Encerra a sessao autenticada e retorna para o login.
     */
    public function logout()
    {
        $session = session();
        $session->destroy();

        return redirect()->to('/login');
    }

    /**
     * Remove o registro solicitado e retorna para a listagem.
     */
    public function delete($id_login)
    {
        $usuario = $this->login_model->where('id_login', $id_login)->first();
        $this->login_model->where('id_login', $id_login)->delete();
        ImagemCadastro::remover($usuario['foto'] ?? '');

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/login/usuarios');
    }

    /**
     * Compara a senha informada com o hash armazenado.
     */
    private function senhaConfere(string $senhaInformada, string $senhaArmazenada): bool
    {
        if ($senhaInformada === '' || $senhaArmazenada === '') {
            return false;
        }

        if (! empty(password_get_info($senhaArmazenada)['algo'])) {
            return password_verify($senhaInformada, $senhaArmazenada);
        }

        return hash_equals($senhaArmazenada, $senhaInformada);
    }

    /**
     * Informa se o hash da senha deve ser atualizado.
     */
    private function senhaPrecisaAtualizar(string $senhaArmazenada): bool
    {
        return empty(password_get_info($senhaArmazenada)['algo'])
            || password_needs_rehash($senhaArmazenada, PASSWORD_BCRYPT);
    }
}
