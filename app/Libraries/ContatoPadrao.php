<?php

namespace App\Libraries;

class ContatoPadrao
{
    private const CAMPOS_TELEFONE = [
        'telefone_fixo',
        'fixo',
        'celular',
        'celular_1',
        'comercial',
        'residencial',
    ];

    private const CAMPOS_WHATSAPP = [
        'whatsapp',
        'celular_2',
    ];

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

    public static function telefone(array $dados): string
    {
        return self::primeiroValor($dados, self::CAMPOS_TELEFONE);
    }

    public static function whatsapp(array $dados): string
    {
        return self::primeiroValor($dados, self::CAMPOS_WHATSAPP);
    }

    public static function email(array $dados): string
    {
        return self::primeiroValor($dados, ['email']);
    }

    public static function telefoneLink($valor): string
    {
        $digitos = self::digitos($valor);

        if (strlen($digitos) === 10 || strlen($digitos) === 11) {
            return 'tel:+55' . $digitos;
        }

        if ((strlen($digitos) === 12 || strlen($digitos) === 13) && str_starts_with($digitos, '55')) {
            return 'tel:+' . $digitos;
        }

        return strlen($digitos) >= 8 ? 'tel:' . $digitos : '';
    }

    public static function whatsappLink($valor): string
    {
        $digitos = self::digitos($valor);

        if (strlen($digitos) === 10 || strlen($digitos) === 11) {
            $digitos = '55' . $digitos;
        }

        if (strlen($digitos) >= 12) {
            return 'https://wa.me/' . $digitos;
        }

        return '';
    }

    public static function emailLink($valor): string
    {
        $email = trim((string) $valor);

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? 'mailto:' . $email : '';
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

    private static function digitos($valor): string
    {
        return preg_replace('/\D/', '', (string) $valor) ?? '';
    }
}
