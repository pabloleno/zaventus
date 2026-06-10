<?php

namespace App\Libraries;

class Moeda
{
    private const CAMPOS_MONETARIOS = [
        'desconto',
        'frete',
        'lucro',
        'margem_de_lucro',
        'outros',
        'salario',
        'subtotal',
        'troco',
        'comissao',
    ];

    /**
     * Normaliza os dados recebidos para o formato esperado pela aplicacao.
     */
    public static function normalizar($valor): float
    {
        if (is_int($valor) || is_float($valor)) {
            return round((float) $valor, 2);
        }

        $valor = trim((string) $valor);

        if ($valor === '') {
            return 0.0;
        }

        $valor = preg_replace('/[^\d,.\-]/', '', $valor);

        if (strpos($valor, ',') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (substr_count($valor, '.') > 1) {
            $ultimaPosicao = strrpos($valor, '.');
            $valor = str_replace('.', '', substr($valor, 0, $ultimaPosicao))
                . substr($valor, $ultimaPosicao);
        }

        return round(is_numeric($valor) ? (float) $valor : 0.0, 2);
    }

    /**
     * Converte um valor monetario para a representacao decimal persistida.
     */
    public static function decimal($valor): string
    {
        return number_format(self::normalizar($valor), 2, '.', '');
    }

    /**
     * Formata o valor para exibicao conforme o padrao monetario.
     */
    public static function formatar($valor, bool $comSimbolo = false): string
    {
        $formatado = number_format(self::normalizar($valor), 2, ',', '.');

        return $comSimbolo ? 'R$ ' . $formatado : $formatado;
    }

    /**
     * Informa se o campo representa um valor monetario.
     */
    public static function campoMonetario(string $campo): bool
    {
        $campo = strtolower(trim($campo));

        return $campo === 'valor'
            || str_starts_with($campo, 'valor_')
            || str_ends_with($campo, '_valor')
            || in_array($campo, self::CAMPOS_MONETARIOS, true);
    }
}
