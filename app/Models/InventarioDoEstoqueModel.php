<?php

namespace App\Models;

class InventarioDoEstoqueModel extends PadraoModel
{
    protected $table = 'inventarios_do_estoque';
    protected $primaryKey = 'id_inventario';
    protected $allowedFields = [
        'id_inventario',
        'descricao',
        'data',
        'observacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
