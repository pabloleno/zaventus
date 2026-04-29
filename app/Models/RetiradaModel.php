<?php

namespace App\Models;

use CodeIgniter\Model;

class RetiradaModel extends Model
{
    protected $table = 'retiradas';
    protected $primaryKey = 'id_retirada';
    protected $allowedFields = [
        'id_retirada',
        'tipo',
        'descricao',
        'valor',
        'data',
        'hora',
        'observacoes',
        'id_caixa',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
