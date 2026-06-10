<?php

namespace App\Libraries;

use App\Models\TabelaMunicipiosIBGEModel;

class EnderecoPadrao
{
    private const CODIGOS_UF_IBGE = [
        'RO' => '11',
        'AC' => '12',
        'AM' => '13',
        'RR' => '14',
        'PA' => '15',
        'AP' => '16',
        'TO' => '17',
        'MA' => '21',
        'PI' => '22',
        'CE' => '23',
        'RN' => '24',
        'PB' => '25',
        'PE' => '26',
        'AL' => '27',
        'SE' => '28',
        'BA' => '29',
        'MG' => '31',
        'ES' => '32',
        'RJ' => '33',
        'SP' => '35',
        'PR' => '41',
        'SC' => '42',
        'RS' => '43',
        'MS' => '50',
        'MT' => '51',
        'GO' => '52',
        'DF' => '53',
    ];

    /**
     * Retorna a lista de unidades federativas disponiveis.
     */
    public static function ufs(): array
    {
        $ufs = [];

        foreach (self::CODIGOS_UF_IBGE as $uf => $codigo) {
            $ufs[] = [
                'UF' => $uf,
                'codigo' => $codigo,
            ];
        }

        return $ufs;
    }

    /**
     * Lista os municipios pertencentes a UF informada.
     */
    public static function municipiosPorUf(TabelaMunicipiosIBGEModel $model, $uf): array
    {
        $uf = preg_replace('/[^A-Z]/', '', strtoupper((string) $uf));

        if (! isset(self::CODIGOS_UF_IBGE[$uf])) {
            return [];
        }

        $prefixo = self::CODIGOS_UF_IBGE[$uf];
        $municipios = $model
            ->select('codigo, municipio')
            ->orderBy('municipio', 'ASC')
            ->findAll();

        $dados = [];

        foreach ($municipios as $municipio) {
            $codigo = preg_replace('/\D/', '', (string) ($municipio['codigo'] ?? ''));
            $nome = self::limparMunicipio($municipio['municipio'] ?? '');

            if ($codigo === '' || $nome === '' || substr($codigo, 0, 2) !== $prefixo) {
                continue;
            }

            $dados[] = [
                'codigo' => $codigo,
                'municipio' => $nome,
            ];
        }

        return $dados;
    }

    /**
     * Valida e normaliza os dados antes da persistencia.
     */
    public static function preparar(array $dados, TabelaMunicipiosIBGEModel $model, string $ufCampo = 'UF', string $municipioCampo = 'municipio', string $codigoCampo = 'codigo_do_municipio'): array
    {
        $codigo = preg_replace('/\D/', '', (string) ($dados[$codigoCampo] ?? ''));
        $uf = preg_replace('/[^A-Z]/', '', strtoupper((string) ($dados[$ufCampo] ?? '')));

        if ($codigo !== '') {
            $municipio = self::municipioPorCodigo($model, $codigo);
            $dados[$codigoCampo] = $codigo;
            $dados[$municipioCampo] = $municipio['municipio'] ?? self::limparMunicipio($dados[$municipioCampo] ?? '');
        } elseif (! empty($dados[$municipioCampo]) && strpos((string) $dados[$municipioCampo], ';') !== false) {
            $separados = explode(';', (string) $dados[$municipioCampo], 2);
            $dados[$codigoCampo] = preg_replace('/\D/', '', $separados[0] ?? '');
            $dados[$municipioCampo] = self::limparMunicipio($separados[1] ?? '');
        } else {
            $dados[$codigoCampo] = '';
            $dados[$municipioCampo] = self::limparMunicipio($dados[$municipioCampo] ?? '');
        }

        if ($uf === '' && ! empty($dados[$codigoCampo])) {
            $uf = self::ufPorCodigoMunicipio($dados[$codigoCampo]) ?? '';
        }

        $dados[$ufCampo] = $uf;

        return $dados;
    }

    /**
     * Localiza um municipio pelo codigo IBGE informado.
     */
    private static function municipioPorCodigo(TabelaMunicipiosIBGEModel $model, string $codigo): ?array
    {
        $municipio = $model
            ->select('codigo, municipio')
            ->like('codigo', $codigo, 'both')
            ->first();

        if (! $municipio) {
            return null;
        }

        return [
            'codigo' => preg_replace('/\D/', '', (string) ($municipio['codigo'] ?? '')),
            'municipio' => self::limparMunicipio($municipio['municipio'] ?? ''),
        ];
    }

    /**
     * Extrai a UF correspondente a partir do codigo IBGE do municipio.
     */
    private static function ufPorCodigoMunicipio(string $codigo): ?string
    {
        $prefixo = substr(preg_replace('/\D/', '', $codigo), 0, 2);

        foreach (self::CODIGOS_UF_IBGE as $uf => $codigoUf) {
            if ($codigoUf === $prefixo) {
                return $uf;
            }
        }

        return null;
    }

    /**
     * Limpa e normaliza municipio.
     */
    private static function limparMunicipio($municipio): string
    {
        return trim(str_replace(["\r", "\n"], '', (string) $municipio));
    }
}
