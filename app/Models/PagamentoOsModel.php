<?php

namespace App\Models;

class PagamentoOsModel extends PadraoModel
{
    protected $table = 'pagamentos_os';
    protected $primaryKey = 'id_pagamento';
    protected $allowedFields = [
        'id_pagamento',
        'tipo',
        'id_ordem'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}