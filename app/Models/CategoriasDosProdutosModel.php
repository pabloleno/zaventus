<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriasDosProdutosModel extends Model
{
    protected $table = 'categorias_dos_produtos';
    protected $primaryKey = 'id_categoria';
    protected $allowedFields = [
        'nome',
        'descricao',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
