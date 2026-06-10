<?php

use App\Libraries\Moeda;

if (! function_exists('moeda')) {
    /**
     * Formata um valor monetario para exibicao.
     */
    function moeda($valor, bool $comSimbolo = false): string
    {
        return Moeda::formatar($valor, $comSimbolo);
    }
}

if (! function_exists('decimal_monetario')) {
    /**
     * Converte um valor monetario para a representacao decimal persistida.
     */
    function decimal_monetario($valor): string
    {
        return Moeda::decimal($valor);
    }
}
