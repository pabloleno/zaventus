<?php

namespace App\Models;

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
}
