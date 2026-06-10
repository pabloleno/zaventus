<?php

namespace App\Libraries;

class CredencialIntegracaoPagamento
{
    /**
     * Criptografa uma credencial antes de persisti-la no banco.
     */
    public static function criptografar(string $valor): string
    {
        try {
            return base64_encode(service('encrypter')->encrypt($valor));
        } catch (\Throwable $exception) {
            throw new \RuntimeException(
                'Configure encryption.key no arquivo .env antes de salvar credenciais bancarias.',
                0,
                $exception
            );
        }
    }

    /**
     * Recupera a credencial para uso futuro por um conector homologado.
     */
    public static function descriptografar(string $valor): string
    {
        if ($valor === '') {
            return '';
        }

        try {
            $binario = base64_decode($valor, true);

            if ($binario === false) {
                throw new \RuntimeException('Credencial armazenada em formato invalido.');
            }

            return service('encrypter')->decrypt($binario);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Nao foi possivel descriptografar a credencial bancaria.', 0, $exception);
        }
    }
}
