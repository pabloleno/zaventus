<?php

namespace App\Libraries;

class CampoPadrao
{
    private const TAMANHO_TEXTO_CURTO = 50;
    private const DATA_MINIMA_PADRAO = '1900-01-01';
    private const DATA_MAXIMA_PADRAO = '2100-12-31';

    private const CAMPOS_IGNORADOS_TEXTO = [
        'anotacoes',
        'observacoes',
        'detalhes_da_atividade',
        'controle_de_acesso',
        'xml',
        'protocolo',
        'xml_protocolado_cancelamento',
        'senha',
        'arquivo',
        'certificado',
        'endereco',
    ];

    private const CAMPOS_NUMERICOS = [
        'ncm' => 8,
        'cfop' => 4,
        'csosn' => 3,
        'crt' => 1,
        'cuf' => 2,
        'cmunfg' => 7,
        'cmun' => 7,
        'codigo_do_municipio' => 7,
        'cpais' => 4,
        'cep' => 8,
        'ie' => 13,
        'inscricao_estadual' => 13,
        'cpf' => 11,
    ];

    public static function preparar(array $dados): array
    {
        $erros = self::validar($dados);

        return [
            'dados' => empty($erros) ? self::normalizar($dados) : $dados,
            'erros' => $erros,
        ];
    }

    public static function validar(array $dados): array
    {
        $erros = [];

        foreach ($dados as $campo => $valor) {
            $valor = self::valorString($valor);

            if ($valor === null || trim($valor) === '') {
                continue;
            }

            $campoNormalizado = self::normalizarNomeCampo($campo);
            $digitos = self::somenteDigitos($valor);

            if (self::campoData($campoNormalizado)) {
                $data = self::normalizarData($valor);

                if (! self::dataValida($data)) {
                    $erros[$campo] = self::label($campo) . ' deve ser uma data valida.';
                    continue;
                }

                if ($data < self::DATA_MINIMA_PADRAO || $data > self::DATA_MAXIMA_PADRAO) {
                    $erros[$campo] = self::label($campo) . ' deve estar entre 01/01/1900 e 31/12/2100.';
                }

                continue;
            }

            if (self::campoTelefoneFixo($campoNormalizado)) {
                if (! in_array(strlen($digitos), [10, 11], true)) {
                    $erros[$campo] = self::label($campo) . ' deve conter 10 ou 11 digitos com DDD.';
                }

                continue;
            }

            if (self::campoTelefone($campoNormalizado) && strlen($digitos) !== 11) {
                $erros[$campo] = self::label($campo) . ' deve conter 11 digitos com DDD.';
                continue;
            }

            if (self::campoCnpj($campoNormalizado) && strlen($digitos) !== 14) {
                $erros[$campo] = self::label($campo) . ' deve conter 14 digitos.';
                continue;
            }

            if (self::campoEmail($campoNormalizado)) {
                if (self::tamanho($valor) > self::TAMANHO_TEXTO_CURTO) {
                    $erros[$campo] = self::label($campo) . ' deve ter no maximo 50 caracteres.';
                    continue;
                }

                if (strpos($valor, '@') === false || ! filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    $erros[$campo] = self::label($campo) . ' deve conter um e-mail valido com @.';
                }
            }
        }

        return $erros;
    }

    public static function normalizar(array $dados): array
    {
        foreach ($dados as $campo => $valor) {
            $valor = self::valorString($valor);

            if ($valor === null) {
                continue;
            }

            $campoNormalizado = self::normalizarNomeCampo($campo);
            $valor = trim($valor);

            if (self::campoData($campoNormalizado)) {
                $dados[$campo] = self::normalizarData($valor);
                continue;
            }

            if (self::campoTelefone($campoNormalizado)) {
                $dados[$campo] = substr(self::somenteDigitos($valor), 0, 11);
                continue;
            }

            if (self::campoCnpj($campoNormalizado)) {
                $dados[$campo] = substr(self::somenteDigitos($valor), 0, 14);
                continue;
            }

            if (self::campoEmail($campoNormalizado)) {
                $dados[$campo] = self::limitar($valor, self::TAMANHO_TEXTO_CURTO);
                continue;
            }

            if ($campoNormalizado === 'uf') {
                $dados[$campo] = strtoupper(self::limitar($valor, 2));
                continue;
            }

            if (isset(self::CAMPOS_NUMERICOS[$campoNormalizado])) {
                $dados[$campo] = substr(self::somenteDigitos($valor), 0, self::CAMPOS_NUMERICOS[$campoNormalizado]);
                continue;
            }

            if (self::campoTextoCurto($campoNormalizado)) {
                $dados[$campo] = self::limitar($valor, self::TAMANHO_TEXTO_CURTO);
            }
        }

        return $dados;
    }

    private static function campoTelefone(string $campo): bool
    {
        return preg_match('/(^|_)(telefone|fone|fixo|celular|whatsapp|comercial|residencial)(_|$)/', $campo) === 1;
    }

    private static function campoTelefoneFixo(string $campo): bool
    {
        return $campo === 'telefone_fixo';
    }

    private static function campoCnpj(string $campo): bool
    {
        return strpos($campo, 'cnpj') !== false;
    }

    private static function campoEmail(string $campo): bool
    {
        return strpos($campo, 'email') !== false;
    }

    private static function campoData(string $campo): bool
    {
        return $campo === 'data'
            || preg_match('/^data(_|$)/', $campo) === 1
            || preg_match('/(^|_)(validade|vencimento|nascimento|contratacao|admissao|demissao|emissao|expedicao)(_|$)/', $campo) === 1;
    }

    private static function campoTextoCurto(string $campo): bool
    {
        if (in_array($campo, self::CAMPOS_IGNORADOS_TEXTO, true)) {
            return false;
        }

        return preg_match('/(^|_)(nome|razao|fantasia|logradouro|endereco|numero|complemento|bairro|municipio|cidade|contato|localizacao|cargo|sexo|status|unidade)(_|$)/', $campo) === 1
            || in_array($campo, ['natop', 'verproc', 'xlgr', 'xnome', 'xfant', 'xcpl', 'xbairro', 'xmun', 'xpais', 'xcontato', 'usuario', 'primeiro_nome'], true);
    }

    private static function normalizarNomeCampo(string $campo): string
    {
        return strtolower($campo);
    }

    private static function somenteDigitos(string $valor): string
    {
        return preg_replace('/\D/', '', $valor);
    }

    private static function limitar(string $valor, int $limite): string
    {
        return function_exists('mb_substr')
            ? mb_substr($valor, 0, $limite)
            : substr($valor, 0, $limite);
    }

    private static function normalizarData(string $valor): string
    {
        $valor = trim($valor);

        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $valor, $partes) === 1) {
            return $partes[3] . '-' . $partes[2] . '-' . $partes[1];
        }

        return $valor;
    }

    private static function dataValida(string $valor): bool
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $valor, $partes) !== 1) {
            return false;
        }

        return checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1]);
    }

    private static function tamanho(string $valor): int
    {
        return function_exists('mb_strlen')
            ? mb_strlen($valor)
            : strlen($valor);
    }

    private static function valorString($valor): ?string
    {
        if (is_string($valor) || is_numeric($valor)) {
            return (string) $valor;
        }

        if (is_object($valor) && method_exists($valor, '__toString')) {
            return (string) $valor;
        }

        return null;
    }

    private static function label(string $campo): string
    {
        $labels = [
            'cnpj' => 'CNPJ',
            'CNPJ' => 'CNPJ',
            'CNPJ_responsavel_tecnico' => 'CNPJ do responsavel tecnico',
            'telefone' => 'Telefone',
            'fone' => 'Telefone',
            'fone_responsavel_tecnico' => 'Telefone do responsavel tecnico',
            'celular' => 'Celular',
            'whatsapp' => 'Whatsapp',
            'telefone_fixo' => 'Telefone fixo',
            'comercial' => 'Telefone comercial',
            'residencial' => 'Telefone residencial',
            'email' => 'E-mail',
            'email_responsavel_tecnico' => 'E-mail do responsavel tecnico',
        ];

        return $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
    }
}
