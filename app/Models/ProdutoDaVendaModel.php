<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoDaVendaModel extends Model
{
    protected $table = 'produtos_da_venda';
    protected $primaryKey = 'id_produto_da_venda';
    protected $allowedFields = [
        'id_produto_da_venda',
        'nome',
        'unidade',
        'codigo_de_barras',
        'quantidade',
        'valor_unitario',
        'subtotal',
        'desconto',
        'valor_final',
        'NCM',
        'CSOSN',
        'CFOP',
        'id_venda',
        'id_produto'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
