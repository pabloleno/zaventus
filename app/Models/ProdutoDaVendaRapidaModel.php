<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoDaVendaRapidaModel extends Model
{
    protected $table = 'produtos_da_venda_rapida';
    protected $primaryKey = 'id_produto_da_venda_rapida';
    protected $allowedFields = [
        'id_produto_da_venda_rapida',
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
        'id_produto'
    ];
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';
}
