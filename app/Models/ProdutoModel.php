<?php

namespace App\Models;

use App\Libraries\OrcamentoCalculo;

class ProdutoModel extends PadraoModel
{
    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';
    protected $allowedFields = [
        'id_produto',
        'nome',
        'unidade',
        'codigo_de_barras',
        'localizacao',
        'quantidade',
        'quantidade_minima',
        'margem_de_lucro',
        'valor_de_custo',
        'valor_de_venda',
        'lucro',
        'NCM',
        'CSOSN',
        'CFOP',
        'arquivo',
        'id_categoria',
        'id_fornecedor',
        'validade',
        'ativo',
        'observacoes',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected function aplicaPadraoCampos(array $eventData): array
    {
        $originais = $eventData['data'] ?? [];
        $eventData = parent::aplicaPadraoCampos($eventData);
        foreach (['nome', 'localizacao'] as $campo) {
            if (isset($originais[$campo])) {
                $eventData['data'][$campo] = trim((string) $originais[$campo]);
            }
        }
        foreach (['quantidade', 'quantidade_minima', 'valor_de_custo', 'valor_de_venda', 'lucro', 'margem_de_lucro'] as $campo) {
            if (isset($originais[$campo])) {
                $valor = (string) $originais[$campo];
                $assinado = in_array($campo, ['lucro', 'margem_de_lucro'], true) && str_starts_with($valor, '-');
                $eventData['data'][$campo] = ($assinado ? '-' : '') . OrcamentoCalculo::decimal($assinado ? substr($valor, 1) : $valor, str_starts_with($campo, 'quantidade') ? 4 : 2);
            }
        }
        return $eventData;
    }

    protected function aplicaPadraoCamposBatch(array $eventData): array
    {
        foreach ($eventData['data'] ?? [] as $indice => $linha) {
            $eventData['data'][$indice] = $this->aplicaPadraoCampos(['data' => $linha])['data'];
        }
        return $eventData;
    }
}
