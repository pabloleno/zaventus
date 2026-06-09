<?php

use App\Libraries\Moeda;

if (! function_exists('moeda')) {
    function moeda($valor, bool $comSimbolo = false): string
    {
        return Moeda::formatar($valor, $comSimbolo);
    }
}

if (! function_exists('decimal_monetario')) {
    function decimal_monetario($valor): string
    {
        return Moeda::decimal($valor);
    }
}
