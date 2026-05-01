<?php

namespace App\Models;

class ProdutoPecaOsModel extends PadraoModel
{
    protected $table = 'produtos_pecas_os';
    protected $primaryKey = 'id_produto';
    protected $allowedFields = [
        'id_produto',
        'id_produto_estoque',
        'nome',
        'quantidade',
        'valor_unitario',
        'desconto',
        'id_ordem'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
