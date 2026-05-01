<?php

namespace App\Models;

class DespesaModel extends PadraoModel
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
