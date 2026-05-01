<?php

namespace App\Models;

class ReposicaoModel extends PadraoModel
{
    protected $table = 'reposicoes';
    protected $primaryKey = 'id_reposicao';
    protected $allowedFields = [
        'id_reposicao',
        'data',
        'hora',
        'quantidade',
        'observacoes',
        'id_produto'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
