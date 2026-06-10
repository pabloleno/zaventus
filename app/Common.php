<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */

use App\Libraries\CampoPadrao;

if (! function_exists('prepara_campos_padrao')) {
    /**
     * Valida e normaliza campos compartilhados antes da persistencia.
     */
    function prepara_campos_padrao(array $dados): array
    {
        return CampoPadrao::preparar($dados);
    }
}

if (! function_exists('redireciona_erros_campos_padrao')) {
    /**
     * Retorna ao formulario preservando os dados e exibindo os erros de validacao.
     */
    function redireciona_erros_campos_padrao(array $erros)
    {
        session()->setFlashdata('errors', $erros);

        return redirect()->back()->withInput();
    }
}
