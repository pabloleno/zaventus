<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthGuard implements FilterInterface
{
    /**
     * @var array<string, array{0:string, 1:string}>
     */
    private array $controllerPermissions = [
        'caixas'                 => ['financeiro', 'caixas'],
        'categoriasdosprodutos'  => ['estoque', 'categorias_do_produto'],
        'clientes'               => ['controle_geral', 'clientes'],
        'contaspagar'            => ['financeiro', 'contas_a_pagar'],
        'contasreceber'          => ['financeiro', 'contas_a_receber'],
        'controlefiscal'         => ['financeiro', 'controle_fiscal'],
        'despesas'               => ['financeiro', 'despesas'],
        'fornecedores'           => ['controle_geral', 'fornecedores'],
        'funcionarios'           => ['controle_geral', 'funcionarios'],
        'imprimedanfe'           => ['financeiro', 'controle_fiscal'],
        'inventariodoestoque'    => ['financeiro', 'inventario_do_estoque'],
        'lancamentos'            => ['financeiro', 'lancamentos'],
        'nfe'                    => ['financeiro', 'controle_fiscal'],
        'orcamentos'             => ['financeiro', 'orcamentos'],
        'pagamentosdocliente'    => ['controle_geral', 'clientes'],
        'pdv'                    => ['vendas', 'pdv'],
        'pedidos'                => ['financeiro', 'pedidos'],
        'relatoriodre'           => ['financeiro', 'relatorio_dre'],
        'reposicoes'             => ['estoque', 'reposicoes'],
        'retiradas'              => ['financeiro', 'retiradas_do_caixa'],
        'saidademercadorias'     => ['estoque', 'saida_de_mercadorias'],
        'vendarapida'            => ['vendas', 'venda_rapida'],
        'vendas'                 => ['vendas', 'hist_de_vendas'],
        'vendedores'             => ['controle_geral', 'vendedores'],
    ];

    /**
     * Valida e prepara a requisicao antes de ela chegar ao controller.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request instanceof CLIRequest || ! $request instanceof IncomingRequest) {
            return null;
        }

        [$controller, $method] = $this->routeParts($request);

        if ($this->isPublicRoute($controller, $method)) {
            return null;
        }

        if ($this->isBlockedMethod($method)) {
            return service('response')->setStatusCode(404);
        }

        $session = session();

        if (! $session->get('id_login')) {
            $session->setFlashdata('alert', 'session_expired');

            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['error' => 'authentication_required']);
            }

            return redirect()->to('/login');
        }

        $permission = $this->requiredPermission($controller, $method);

        if ($permission !== null && ! $this->hasPermission($session->get('controle_de_acesso'), $permission[0], $permission[1])) {
            $session->setFlashdata('alert', 'access_denied');

            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['error' => 'access_denied']);
            }

            return redirect()->to('/inicio');
        }

        return null;
    }

    /**
     * Mantem o ponto de extensao executado depois da requisicao.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    /**
     * Extrai o controller e o metodo solicitados a partir da URL.
     * @return array{0:string, 1:string}
     */
    private function routeParts(IncomingRequest $request): array
    {
        $path = trim($request->getUri()->getPath(), '/');
        $segments = array_values(array_filter(explode('/', strtolower($path)), 'strlen'));

        if (($segments[0] ?? '') === 'index.php') {
            array_shift($segments);
        }

        return [
            $segments[0] ?? '',
            $segments[1] ?? 'index',
        ];
    }

    /**
     * Informa se a rota pode ser acessada sem sessao autenticada.
     */
    private function isPublicRoute(string $controller, string $method): bool
    {
        if ($controller === '' || $controller === 'home') {
            return true;
        }

        return $controller === 'login' && in_array($method, ['index', 'autenticar', 'logout'], true);
    }

    /**
     * Informa se o metodo nao pode ser acessado como endpoint.
     */
    private function isBlockedMethod(string $method): bool
    {
        return str_starts_with($method, '__')
            || in_array($method, ['format', 'initcontroller'], true);
    }

    /**
     * Determina a permissao exigida para a rota solicitada.
     * @return array{0:string, 1:string}|null
     */
    private function requiredPermission(string $controller, string $method): ?array
    {
        if ($controller === 'login') {
            return ['configs', 'usuarios'];
        }

        if ($controller === 'configs') {
            return $this->configPermission($method);
        }

        if ($controller === 'produtos') {
            return $this->productPermission($method);
        }

        if ($controller === 'relatorios') {
            return $this->reportPermission($method);
        }

        return $this->controllerPermissions[$controller] ?? null;
    }

    /**
     * Define a permissao exigida para uma acao de configuracao.
     * @return array{0:string, 1:string}|null
     */
    private function configPermission(string $method): ?array
    {
        if ($method === 'alteratema') {
            return null;
        }

        if (in_array($method, ['nfe', 'store_nfe'], true)) {
            return ['configs', 'nfe'];
        }

        if (in_array($method, ['nfce', 'store_nfce'], true)) {
            return ['configs', 'nfce'];
        }

        if (in_array($method, ['empresa', 'store_empresa'], true)) {
            return ['configs', 'empresa'];
        }

        if ($method === 'backupdatabase') {
            return ['configs', 'backup_de_dados'];
        }

        return ['configs', 'sistema'];
    }

    /**
     * Define a permissao exigida para uma acao relacionada a produtos.
     * @return array{0:string, 1:string}
     */
    private function productPermission(string $method): array
    {
        if ($method === 'pesquisar') {
            return ['vendas', 'pesq_produto'];
        }

        if (str_contains($method, 'reposicao') || str_contains($method, 'repoe')) {
            return ['estoque', 'reposicoes'];
        }

        return ['estoque', 'produtos'];
    }

    /**
     * Define a permissao exigida para acessar um relatorio.
     * @return array{0:string, 1:string}
     */
    private function reportPermission(string $method): array
    {
        if (in_array($method, ['historicocompleto', 'porcliente', 'porvendedor'], true)) {
            return ['relatorios', 'vendas'];
        }

        if (in_array($method, ['produtos', 'estoqueminimo', 'validadedosprodutos'], true)) {
            return ['relatorios', 'estoque'];
        }

        if (in_array($method, ['clientes', 'fornecedores', 'funcionarios', 'vendedores'], true)) {
            return ['relatorios', 'geral'];
        }

        return ['relatorios', 'financeiro'];
    }

    /**
     * Informa se o usuario possui a permissao exigida para a rota.
     */
    private function hasPermission($accessControl, string $module, string $permission): bool
    {
        $permissions = json_decode((string) $accessControl, true);

        if (! is_array($permissions)) {
            return false;
        }

        return (int) ($permissions[$module]['modulo'] ?? 0) === 1
            && (int) ($permissions[$module][$permission] ?? 0) === 1;
    }
}
