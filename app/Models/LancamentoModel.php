<?php

namespace App\Models;

class LancamentoModel extends PadraoModel
{
    protected $table = 'lancamentos';
    protected $primaryKey = 'id_lancamento';
    protected $allowedFields = [
        'id_lancamento',
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
