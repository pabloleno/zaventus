<?php

namespace App\Models;

use CodeIgniter\Model;

class DespesaModel extends Model
{
    protected $table = 'despesas';
    protected $primaryKey = 'id_despesa';
    protected $allowedFields = [
        'id_despesa',
        'tipo',
        'descricao',
        'valor',
        'data',
        'hora',
        'observacoes',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
