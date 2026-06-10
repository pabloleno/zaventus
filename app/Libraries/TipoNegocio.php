<?php

namespace App\Libraries;

class TipoNegocio
{
    public const TODOS = 'Todos';
    public const GERAL = 'Geral';
    public const PRODUTOS = 'Produtos';
    public const SERVICOS = 'Servicos';

    /**
     * Retorna as opcoes de opcoes.
     */
    public static function opcoes(bool $incluirTodos = false): array
    {
        $opcoes = [
            self::GERAL => 'Geral',
            self::PRODUTOS => 'Produtos',
            self::SERVICOS => "Servi\u{00E7}os",
        ];

        if ($incluirTodos) {
            return [self::TODOS => 'Todos'] + $opcoes;
        }

        return $opcoes;
    }

    /**
     * Retorna as opcoes de vendas.
     */
    public static function opcoesVendas(bool $incluirTodos = true): array
    {
        $opcoes = [
            self::PRODUTOS => 'Produtos',
            self::SERVICOS => "Servi\u{00E7}os",
        ];

        if ($incluirTodos) {
            return [self::TODOS => 'Todos'] + $opcoes;
        }

        return $opcoes;
    }

    /**
     * Normaliza os dados recebidos para o formato esperado pela aplicacao.
     */
    public static function normalizar($tipo, string $padrao = self::GERAL, bool $aceitarTodos = false): string
    {
        $tipo = trim((string) $tipo);
        $validos = array_keys(self::opcoes($aceitarTodos));

        return in_array($tipo, $validos, true) ? $tipo : $padrao;
    }

    /**
     * Normaliza venda.
     */
    public static function normalizarVenda($tipo): string
    {
        $tipo = trim((string) $tipo);

        return in_array($tipo, array_keys(self::opcoesVendas()), true) ? $tipo : self::TODOS;
    }

    /**
     * Retorna o rotulo legivel do tipo de negocio.
     */
    public static function rotulo($tipo): string
    {
        $opcoes = self::opcoes(true);

        return $opcoes[$tipo] ?? $opcoes[self::GERAL];
    }
}
