<?php

namespace App\Tests\Libraries;

use App\Libraries\OrcamentoCalculo;
use CodeIgniter\Test\CIUnitTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;

final class OrcamentoCalculoTest extends CIUnitTestCase
{
    public function testCalculaFachadaInstalacaoECortesiaComDesconto(): void
    {
        $resultado = OrcamentoCalculo::total([
            ['tipo_preco' => 'metro_quadrado', 'largura' => '5,00', 'altura' => '1,20', 'valor' => '300'],
            ['tipo_preco' => 'fixo', 'valor' => '300'],
            ['tipo_preco' => 'unidade', 'valor' => '80', 'cortesia' => 1],
        ], 'valor', '100');

        $this->assertSame('6.0000', $resultado['itens'][0]['area']);
        $this->assertSame('1800.00', $resultado['itens'][0]['total']);
        $this->assertSame('80.00', $resultado['itens'][2]['valor_catalogo']);
        $this->assertSame('0.00', $resultado['itens'][2]['valor']);
        $this->assertSame('2100.00', $resultado['subtotal']);
        $this->assertSame('100.00', $resultado['desconto']);
        $this->assertSame('2000.00', $resultado['total']);
    }

    public function testBannerFixoMultiplicaQuantidadeSemCobrarArea(): void
    {
        $item = OrcamentoCalculo::item([
            'tipo_preco' => 'fixo', 'quantidade' => 2, 'valor' => 70,
            'largura' => '90', 'altura' => '120', 'unidade_dimensao' => 'cm',
        ]);

        $this->assertSame('140.00', $item['total']);
        $this->assertSame('2.0000', $item['quantidade']);
        $this->assertSame('90.0000', $item['largura']);
        $this->assertSame('120.0000', $item['altura']);
        $this->assertSame('1.0800', $item['area']);
    }

    public function testMultiplasCortesiasMantemDadosEReceitaZeroMesmoComTotalForjado(): void
    {
        $resultado = OrcamentoCalculo::total([
            ['valor' => '80', 'valor_catalogo' => '100', 'cortesia' => '1', 'total' => '99999'],
            ['tipo_preco' => 'metro_quadrado', 'largura' => '5', 'altura' => '1.2', 'quantidade' => '2', 'valor' => '300', 'cortesia' => true],
        ]);

        $this->assertSame('0.00', $resultado['total']);
        $this->assertSame('0.00', $resultado['subtotal']);
        $this->assertSame('100.00', $resultado['itens'][0]['valor_catalogo']);
        $this->assertSame('6.0000', $resultado['itens'][1]['area']);
        $this->assertSame($resultado, OrcamentoCalculo::total($resultado['itens']));
    }

    public function testConverteMilimetrosECalculaMetroLinearFracionado(): void
    {
        $area = OrcamentoCalculo::item(['tipo_preco' => 'metro_quadrado', 'largura' => '900', 'altura' => '1200', 'unidade_dimensao' => 'mm', 'valor' => '100']);
        $linear = OrcamentoCalculo::item(['tipo_preco' => 'metro_linear', 'largura' => '125', 'unidade_dimensao' => 'cm', 'quantidade' => '1,5', 'valor' => '40']);

        $this->assertSame('108.00', $area['total']);
        $this->assertSame('1.0800', $area['area']);
        $this->assertSame('75.00', $linear['total']);
        $this->assertNull($linear['altura']);
        $this->assertNull($linear['area']);
    }

    public function testQuantidadeSignificaUnidadesEArredondaSomenteTotalDoItem(): void
    {
        $item = OrcamentoCalculo::item(['tipo_preco' => 'quantidade', 'quantidade' => '500', 'valor' => '0.15']);
        $fracao = OrcamentoCalculo::item(['quantidade' => '0.5', 'valor' => '0.03']);

        $this->assertSame('75.00', $item['total']);
        $this->assertSame('0.02', $fracao['total']);
    }

    public function testDescontoPercentualArredondaCentavosESomaFreteEOutros(): void
    {
        $resultado = OrcamentoCalculo::total([['valor' => '10.05']], 'percentual', '10', '2.50', '1');

        $this->assertSame('1.01', $resultado['desconto']);
        $this->assertSame('12.54', $resultado['total']);
        $this->assertSame('2.50', $resultado['frete']);
        $this->assertSame('1.00', $resultado['outros']);
    }

    public function testMantemPrecisaoNoLimiteMonetarioENaoEstouraCalculoIntermediario(): void
    {
        $item = OrcamentoCalculo::item(['valor' => '9999999999999.99']);
        $grande = OrcamentoCalculo::item(['valor' => '0.01', 'quantidade' => '99999999999.9999']);

        $this->assertSame('9999999999999.99', $item['total']);
        $this->assertSame('1000000000.00', $grande['total']);
    }

    #[DataProvider('decimaisValidos')]
    public function testNormalizaDecimal($entrada, int $casas, string $esperado): void
    {
        $this->assertSame($esperado, OrcamentoCalculo::decimal($entrada, $casas));
    }

    public static function decimaisValidos(): array
    {
        return [
            ['1.234,56789', 4, '1234.5679'], ['1,25', 2, '1.25'], ['1.25', 2, '1.25'],
            ['0.005', 2, '0.01'], [0, 4, '0.0000'], ['.5', 4, '0.5000'],
            [0.1, 2, '0.10'], [1.0e-5, 4, '0.0000'], ['99999999999.9999', 4, '99999999999.9999'],
        ];
    }

    #[DataProvider('decimaisInvalidos')]
    public function testRejeitaDecimalInvalido($valor): void
    {
        $this->expectException(InvalidArgumentException::class);
        OrcamentoCalculo::decimal($valor);
    }

    public static function decimaisInvalidos(): array
    {
        return array_map(static fn ($valor): array => [$valor], [
            '', '-1', '-0.01', 'NaN', NAN, INF, true, null, [], '1abc', '1e3',
            '1,2.3', '1.23,45', '1,000,00', 'R$ 10', '100000000000', '99999999999.99995',
        ]);
    }

    #[DataProvider('itensInvalidos')]
    public function testRejeitaItemInvalido(array $item): void
    {
        $this->expectException(InvalidArgumentException::class);
        OrcamentoCalculo::item($item);
    }

    public static function itensInvalidos(): array
    {
        return array_map(static fn (array $item): array => [$item], [
            ['quantidade' => 0], ['quantidade' => '-1'], ['valor' => '-0.01'],
            ['tipo_preco' => 'invalido'], ['unidade_dimensao' => 'km'], ['cortesia' => 'sim'],
            ['tipo_preco' => 'metro_linear'], ['tipo_preco' => 'metro_quadrado', 'largura' => '5'],
            ['tipo_preco' => 'metro_quadrado', 'largura' => '5', 'altura' => '0', 'cortesia' => 1],
            ['valor' => '9999999999999.99', 'quantidade' => '2'],
        ]);
    }

    #[DataProvider('totaisInvalidos')]
    public function testRejeitaDescontosETotaisInvalidos(string $tipo, $desconto, $frete, $outros): void
    {
        $this->expectException(InvalidArgumentException::class);
        OrcamentoCalculo::total([['valor' => '10'], ['valor' => '80', 'cortesia' => 1]], $tipo, $desconto, $frete, $outros);
    }

    public static function totaisInvalidos(): array
    {
        return [
            ['valor', '11', 0, 0], ['percentual', '100.0001', 0, 0], ['percentual', '-1', 0, 0],
            ['invalido', 0, 0, 0], ['nenhum', '-1', 0, 0], ['nenhum', 0, '-1', 0],
            ['nenhum', 0, 0, '9999999999999.99'],
        ];
    }
}
