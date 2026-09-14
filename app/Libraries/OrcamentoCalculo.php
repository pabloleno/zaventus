<?php

namespace App\Libraries;

use InvalidArgumentException;
use RuntimeException;

final class OrcamentoCalculo
{
    private const ESCALA_CALCULO = 20;
    private const TIPOS_PRECO = ['fixo', 'unidade', 'metro_linear', 'metro_quadrado', 'quantidade'];
    private const DIVISORES_DIMENSAO = ['m' => '1', 'cm' => '100', 'mm' => '1000'];

    public static function decimal($valor, int $casas = 4): string
    {
        self::exigirBcMath();

        if ($casas < 0 || $casas > 4) {
            throw new InvalidArgumentException('A precisão decimal deve estar entre zero e quatro casas.');
        }

        if (is_float($valor) && is_finite($valor)) {
            $valor = self::expandirExpoente(json_encode($valor, JSON_PRESERVE_ZERO_FRACTION));
        }

        if (! is_string($valor) && ! is_int($valor)) {
            throw new InvalidArgumentException('Informe um número decimal válido e não negativo.');
        }

        $valor = trim((string) $valor);

        if (strlen($valor) > 128) {
            throw new InvalidArgumentException('O valor informado excede o limite permitido.');
        }

        if (preg_match('/^\d{1,3}(?:\.\d{3})+,\d+$/D', $valor) === 1) {
            $valor = str_replace('.', '', $valor);
        }

        if (preg_match('/^(?:\d+(?:[.,]\d+)?|[.,]\d+)$/D', $valor) !== 1) {
            throw new InvalidArgumentException('Informe um número decimal válido e não negativo.');
        }

        $valor = str_replace(',', '.', $valor);
        $arredondado = self::arredondar($valor, $casas);
        $limite = str_repeat('9', 15 - $casas) . ($casas > 0 ? '.' . str_repeat('9', $casas) : '');

        if (bccomp($arredondado, $limite, $casas) > 0) {
            throw new InvalidArgumentException('O valor informado excede o limite permitido.');
        }

        return $arredondado;
    }

    public static function item(array $item): array
    {
        $tipo = $item['tipo_preco'] ?? 'unidade';
        $unidade = $item['unidade_dimensao'] ?? 'm';

        if (! in_array($tipo, self::TIPOS_PRECO, true)) {
            throw new InvalidArgumentException('Selecione um tipo de preço válido.');
        }

        if (! is_string($unidade) || ! isset(self::DIVISORES_DIMENSAO[$unidade])) {
            throw new InvalidArgumentException('A unidade das dimensões deve ser m, cm ou mm.');
        }

        $cortesia = $item['cortesia'] ?? 0;
        if (! in_array($cortesia, [0, 1, '0', '1', false, true], true)) {
            throw new InvalidArgumentException('Informe se o item é uma cortesia.');
        }

        $quantidade = self::decimal($item['quantidade'] ?? 1);
        if (bccomp($quantidade, '0', 4) <= 0) {
            throw new InvalidArgumentException('A quantidade do item deve ser maior que zero.');
        }

        $valor = self::decimal($item['valor'] ?? 0, 2);
        $valorCatalogo = self::decimal($item['valor_catalogo'] ?? $valor, 2);
        $largura = self::dimensao($item['largura'] ?? null);
        $altura = self::dimensao($item['altura'] ?? null);

        if (in_array($tipo, ['metro_linear', 'metro_quadrado'], true) && ! self::positiva($largura)) {
            throw new InvalidArgumentException('Informe uma largura maior que zero para o serviço por metro.');
        }

        if ($tipo === 'metro_quadrado' && ! self::positiva($altura)) {
            throw new InvalidArgumentException('Informe uma altura maior que zero para o serviço por metro quadrado.');
        }

        $divisor = self::DIVISORES_DIMENSAO[$unidade];
        $larguraMetros = $largura === null ? null : bcdiv($largura, $divisor, self::ESCALA_CALCULO);
        $alturaMetros = $altura === null ? null : bcdiv($altura, $divisor, self::ESCALA_CALCULO);
        $areaExata = $larguraMetros !== null && $alturaMetros !== null
            ? bcmul($larguraMetros, $alturaMetros, self::ESCALA_CALCULO)
            : null;
        $area = $areaExata === null ? null : self::decimal($areaExata);
        $fator = $tipo === 'metro_linear' ? $larguraMetros : ($tipo === 'metro_quadrado' ? $areaExata : '1');

        // Cortesia mantém medidas e trabalho, mas sua receita é sempre zero.
        $valor = (int) $cortesia === 1 ? '0.00' : $valor;
        $subtotal = self::decimal(
            bcmul(bcmul($fator, $quantidade, self::ESCALA_CALCULO), $valor, self::ESCALA_CALCULO),
            2
        );

        return array_replace($item, [
            'tipo_preco' => $tipo,
            'unidade_dimensao' => $unidade,
            'quantidade' => $quantidade,
            'largura' => $largura,
            'altura' => $altura,
            'area' => $area,
            'cortesia' => (int) $cortesia,
            'valor_catalogo' => $valorCatalogo,
            'valor' => $valor,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }

    public static function total(array $itens, string $descontoTipo = 'nenhum', $desconto = 0, $frete = 0, $outros = 0): array
    {
        self::exigirBcMath();

        if (! in_array($descontoTipo, ['nenhum', 'valor', 'percentual'], true)) {
            throw new InvalidArgumentException('Selecione um tipo de desconto válido.');
        }

        $normalizados = [];
        $subtotal = '0.00';

        foreach ($itens as $item) {
            if (! is_array($item)) {
                throw new InvalidArgumentException('Informe itens válidos para o orçamento.');
            }

            $normalizado = self::item($item);
            $normalizados[] = $normalizado;
            $subtotal = self::decimal(bcadd($subtotal, $normalizado['total'], 2), 2);
        }

        $descontoInformado = self::decimal($desconto, $descontoTipo === 'percentual' ? 4 : 2);
        $frete = self::decimal($frete, 2);
        $outros = self::decimal($outros, 2);

        if ($descontoTipo === 'percentual') {
            if (bccomp($descontoInformado, '100', 4) > 0) {
                throw new InvalidArgumentException('O desconto percentual não pode ultrapassar 100%.');
            }

            $valorDesconto = self::decimal(bcdiv(bcmul($subtotal, $descontoInformado, 6), '100', 8), 2);
        } else {
            $valorDesconto = $descontoTipo === 'valor' ? $descontoInformado : '0.00';
        }

        if (bccomp($valorDesconto, $subtotal, 2) > 0) {
            throw new InvalidArgumentException('O desconto não pode ultrapassar o subtotal dos serviços cobrados.');
        }

        $total = self::decimal(bcadd(bcadd(bcsub($subtotal, $valorDesconto, 2), $frete, 2), $outros, 2), 2);

        return [
            'itens' => $normalizados,
            'subtotal' => $subtotal,
            'desconto' => $valorDesconto,
            'total' => $total,
            'frete' => $frete,
            'outros' => $outros,
        ];
    }

    private static function dimensao($valor): ?string
    {
        return $valor === null || $valor === '' ? null : self::decimal($valor);
    }

    private static function positiva(?string $valor): bool
    {
        return $valor !== null && bccomp($valor, '0', 4) > 0;
    }

    private static function arredondar(string $valor, int $casas): string
    {
        $metade = $casas === 0 ? '0.5' : '0.' . str_repeat('0', $casas) . '5';

        return bcadd($valor, $metade, $casas);
    }

    private static function expandirExpoente(string $valor): string
    {
        if (preg_match('/^(\d+)(?:\.(\d+))?e([+-]?\d+)$/iD', $valor, $partes) !== 1) {
            return $valor;
        }

        $digitos = $partes[1] . ($partes[2] ?? '');
        $posicao = strlen($partes[1]) + (int) $partes[3];

        if ($posicao <= 0) {
            return '0.' . str_repeat('0', -$posicao) . $digitos;
        }

        if ($posicao >= strlen($digitos)) {
            return str_pad($digitos, $posicao, '0');
        }

        return substr($digitos, 0, $posicao) . '.' . substr($digitos, $posicao);
    }

    private static function exigirBcMath(): void
    {
        if (! extension_loaded('bcmath')) {
            throw new RuntimeException('A extensão PHP BCMath deve estar habilitada para calcular orçamentos.');
        }
    }
}
