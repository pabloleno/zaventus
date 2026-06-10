<?php

namespace App\Libraries;

class ProvedoresPagamento
{
    /**
     * Retorna orientacoes operacionais especificas de cada provedor.
     */
    public static function catalogo(): array
    {
        return [
            'paypal' => [
                'credencial_publica' => 'Client ID',
                'credencial_secreta' => 'Client Secret',
                'recursos' => 'Carteira digital, cartao e pagamentos internacionais.',
                'recomendacao' => 'Indicado para vendas online e clientes internacionais. Nao processa PIX.',
            ],
            'pagbank' => [
                'credencial_publica' => 'Identificador da aplicacao/conta',
                'credencial_secreta' => 'Token ou Client Secret',
                'recursos' => 'PIX, boleto, cartoes e cobrancas.',
                'recomendacao' => 'Boa opcao geral para produtos e servicos no Brasil.',
            ],
            'asaas' => [
                'credencial_publica' => 'Identificador opcional da conta',
                'credencial_secreta' => 'API Key',
                'recursos' => 'PIX, boleto, cartao e cobrancas recorrentes.',
                'recomendacao' => 'Indicado para servicos, cobrancas e recorrencia.',
            ],
            'banco_inter' => [
                'credencial_publica' => 'Client ID',
                'credencial_secreta' => 'Client Secret',
                'recursos' => 'PIX e cobrancas bancarias.',
                'recomendacao' => 'Exige tambem certificado mTLS instalado com seguranca no servidor.',
            ],
            'nubank' => [
                'credencial_publica' => 'Nao aplicavel',
                'credencial_secreta' => 'Nao aplicavel',
                'recursos' => 'PIX e cobrancas manuais pelo Nu Empresas.',
                'recomendacao' => 'Sem API publica de cobranca suportada; mantenha o recebimento como PIX manual.',
            ],
        ];
    }

    /**
     * Retorna as orientacoes do provedor ou valores genericos seguros.
     */
    public static function detalhes(string $provedor): array
    {
        return self::catalogo()[$provedor] ?? [
            'credencial_publica' => 'Identificador',
            'credencial_secreta' => 'Credencial secreta',
            'recursos' => 'Consulte a documentacao oficial.',
            'recomendacao' => 'Homologue o provedor antes de ativar em producao.',
        ];
    }
}
