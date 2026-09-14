<?php

namespace App\Models;

use App\Libraries\OrcamentoCalculo;

class ProdutoDoInventarioModel extends PadraoModel
{
    protected $table = 'produtos_do_inventario_do_estoque';
    protected $primaryKey = 'id_produto_do_inventario';
    protected $allowedFields = [
        'id_produto_do_inventario',
        'discriminacao',
        'unidade',
        'quantidade',
        'valor_unitario',
        'id_inventario'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected function aplicaPadraoCampos(array $eventData): array
    {
        $originais = $eventData['data'] ?? [];
        $eventData = parent::aplicaPadraoCampos($eventData);
        foreach (['quantidade' => 4, 'valor_unitario' => 2] as $campo => $casas) {
            if (isset($originais[$campo])) {
                $eventData['data'][$campo] = OrcamentoCalculo::decimal($originais[$campo], $casas);
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
