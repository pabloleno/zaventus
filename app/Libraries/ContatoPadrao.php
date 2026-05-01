<?php

namespace App\Libraries;

class ContatoPadrao
{
    public static function primeiroValor(array $dados, array $campos): string
    {
        foreach ($campos as $campo) {
            $valor = trim((string) ($dados[$campo] ?? ''));

            if ($valor !== '') {
                return $valor;
            }
        }

        return '';
    }

    public static function whatsappLink($valor): string
    {
        $digitos = preg_replace('/\D/', '', (string) $valor);

        if (strlen($digitos) === 11) {
            return 'https://web.whatsapp.com/send?phone=55' . $digitos;
        }

        if (strlen($digitos) > 11) {
            return 'https://web.whatsapp.com/send?phone=' . $digitos;
        }

        return '';
    }

    public static function sincronizarCliente(array $dados): array
    {
        $telefoneFixo = trim((string) ($dados['telefone_fixo'] ?? ''));

        $dados['comercial'] = $telefoneFixo;
        $dados['residencial'] = $telefoneFixo;

        return $dados;
    }

    public static function sincronizarFornecedor(array $dados): array
    {
        $dados['comercial'] = trim((string) ($dados['telefone_fixo'] ?? ''));

        return $dados;
    }

    public static function sincronizarFuncionario(array $dados): array
    {
        return self::sincronizarCliente($dados);
    }

    public static function sincronizarTecnico(array $dados): array
    {
        $dados['fixo'] = trim((string) ($dados['telefone_fixo'] ?? ''));
        $dados['celular_1'] = trim((string) ($dados['celular'] ?? ''));
        $dados['celular_2'] = trim((string) ($dados['whatsapp'] ?? ''));

        return $dados;
    }
}
