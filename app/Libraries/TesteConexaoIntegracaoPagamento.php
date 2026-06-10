<?php

namespace App\Libraries;

class TesteConexaoIntegracaoPagamento
{
    /**
     * Testa somente a autenticacao em uma operacao de leitura segura.
     */
    public static function executar(array $integracao): array
    {
        $provedor = (string) ($integracao['provedor'] ?? '');
        $detalhes = ProvedoresPagamento::detalhes($provedor);

        if (! ($detalhes['teste_conexao_suportado'] ?? false)) {
            return self::resultado(
                'indisponivel',
                (string) ($detalhes['motivo_teste_indisponivel'] ?? 'Teste automatico indisponivel para este provedor.')
            );
        }

        if (empty($integracao['credencial_secreta'])) {
            return self::resultado('erro', 'Cadastre a credencial secreta antes de testar a conexao.');
        }

        if (($detalhes['credencial_publica_obrigatoria'] ?? false) && empty($integracao['credencial_publica'])) {
            return self::resultado('erro', 'Cadastre o identificador publico exigido pelo provedor antes de testar.');
        }

        try {
            $segredo = CredencialIntegracaoPagamento::descriptografar((string) $integracao['credencial_secreta']);

            return match ($provedor) {
                'paypal' => self::testarPayPal($integracao, $segredo),
                'pagbank' => self::testarPagBank($integracao, $segredo),
                'asaas' => self::testarAsaas($integracao, $segredo),
                default => self::resultado('indisponivel', 'Teste automatico ainda nao implementado para este provedor.'),
            };
        } catch (\Throwable $exception) {
            log_message('error', 'Falha segura no diagnostico de integracao de pagamento: ' . $provedor);

            return self::resultado('erro', 'Nao foi possivel concluir o teste. Confira as credenciais, o ambiente e a conexao do servidor.');
        }
    }

    /**
     * Valida Client ID e Secret solicitando um token OAuth sem criar cobrancas.
     */
    private static function testarPayPal(array $integracao, string $segredo): array
    {
        $baseUrl = ($integracao['ambiente'] ?? 'sandbox') === 'producao'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $resposta = self::cliente()->request('POST', $baseUrl . '/v1/oauth2/token', [
            'auth' => [(string) $integracao['credencial_publica'], $segredo],
            'form_params' => ['grant_type' => 'client_credentials'],
            'headers' => ['Accept' => 'application/json'],
            'http_errors' => false,
        ]);

        return self::resultadoHttp($resposta->getStatusCode(), 'PayPal autenticado com sucesso.');
    }

    /**
     * Valida o token PagBank consultando a chave publica sem alterar dados.
     */
    private static function testarPagBank(array $integracao, string $segredo): array
    {
        $baseUrl = ($integracao['ambiente'] ?? 'sandbox') === 'producao'
            ? 'https://api.pagseguro.com'
            : 'https://sandbox.api.pagseguro.com';

        $resposta = self::cliente()->request('GET', $baseUrl . '/public-keys/card', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $segredo,
            ],
            'http_errors' => false,
        ]);

        return self::resultadoHttp($resposta->getStatusCode(), 'PagBank autenticado com sucesso.');
    }

    /**
     * Valida a API Key Asaas consultando o status cadastral da conta.
     */
    private static function testarAsaas(array $integracao, string $segredo): array
    {
        $baseUrl = ($integracao['ambiente'] ?? 'sandbox') === 'producao'
            ? 'https://api.asaas.com'
            : 'https://api-sandbox.asaas.com';

        $resposta = self::cliente()->request('GET', $baseUrl . '/v3/myAccount/status/', [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'ZaventusGestao/1.0',
                'access_token' => $segredo,
            ],
            'http_errors' => false,
        ]);

        return self::resultadoHttp($resposta->getStatusCode(), 'Asaas autenticado com sucesso.');
    }

    /**
     * Cria cliente isolado com limites curtos para nao travar o painel.
     */
    private static function cliente()
    {
        return \Config\Services::curlrequest([
            'connect_timeout' => 5,
            'timeout' => 12,
        ], null, null, false);
    }

    /**
     * Traduz o status HTTP sem persistir respostas que possam conter dados sensiveis.
     */
    private static function resultadoHttp(int $codigo, string $mensagemSucesso): array
    {
        if ($codigo >= 200 && $codigo < 300) {
            return self::resultado('sucesso', $mensagemSucesso . ' Nenhuma cobranca foi criada.');
        }

        if (in_array($codigo, [401, 403], true)) {
            return self::resultado('erro', 'Credencial recusada pelo provedor. Confira o ambiente e gere uma nova credencial.');
        }

        return self::resultado('erro', 'O provedor respondeu HTTP ' . $codigo . '. Confira a credencial e a liberacao da conta.');
    }

    /**
     * Normaliza o retorno persistido e exibido no painel.
     */
    private static function resultado(string $status, string $mensagem): array
    {
        return [
            'status' => $status,
            'mensagem' => mb_substr($mensagem, 0, 255),
        ];
    }
}
