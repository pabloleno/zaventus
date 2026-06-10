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
                'credencial_publica_obrigatoria' => true,
                'ativacao_suportada' => true,
                'teste_conexao_suportado' => true,
                'recursos' => 'Carteira digital, cartao e pagamentos internacionais.',
                'recomendacao' => 'Indicado para vendas online e clientes internacionais. Nao processa PIX.',
            ],
            'pagbank' => [
                'credencial_publica' => 'Identificador da aplicacao/conta',
                'credencial_secreta' => 'Token ou Client Secret',
                'credencial_publica_obrigatoria' => false,
                'ativacao_suportada' => true,
                'teste_conexao_suportado' => true,
                'recursos' => 'PIX, boleto, cartoes e cobrancas.',
                'recomendacao' => 'Boa opcao geral para produtos e servicos no Brasil.',
            ],
            'asaas' => [
                'credencial_publica' => 'Identificador opcional da conta',
                'credencial_secreta' => 'API Key',
                'credencial_publica_obrigatoria' => false,
                'ativacao_suportada' => true,
                'teste_conexao_suportado' => true,
                'recursos' => 'PIX, boleto, cartao e cobrancas recorrentes.',
                'recomendacao' => 'Indicado para servicos, cobrancas e recorrencia.',
            ],
            'banco_inter' => [
                'credencial_publica' => 'Client ID',
                'credencial_secreta' => 'Client Secret',
                'credencial_publica_obrigatoria' => true,
                'ativacao_suportada' => false,
                'teste_conexao_suportado' => false,
                'motivo_teste_indisponivel' => 'O Banco Inter exige certificado mTLS. O conector com certificado ainda nao foi implementado.',
                'recursos' => 'PIX e cobrancas bancarias.',
                'recomendacao' => 'Exige tambem certificado mTLS instalado com seguranca no servidor.',
            ],
            'nubank' => [
                'credencial_publica' => 'Nao aplicavel',
                'credencial_secreta' => 'Nao aplicavel',
                'credencial_publica_obrigatoria' => false,
                'ativacao_suportada' => false,
                'teste_conexao_suportado' => false,
                'motivo_teste_indisponivel' => 'O Nu Empresas permanece como forma manual; nao ha API publica de cobranca suportada neste sistema.',
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
            'credencial_publica_obrigatoria' => true,
            'ativacao_suportada' => false,
            'teste_conexao_suportado' => false,
            'motivo_teste_indisponivel' => 'Provedor sem diagnostico automatico implementado.',
            'recursos' => 'Consulte a documentacao oficial.',
            'recomendacao' => 'Homologue o provedor antes de ativar em producao.',
        ];
    }
}
