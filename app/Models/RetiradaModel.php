<?php

namespace App\Models;

class RetiradaModel extends PadraoModel
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
