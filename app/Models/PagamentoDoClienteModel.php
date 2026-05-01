<?php

namespace App\Models;

class PagamentoDoClienteModel extends PadraoModel
{
    protected $table = 'pagamentos_do_cliente';
    protected $primaryKey = 'id_pagamento';
    protected $allowedFields = [
        'id_pagamento',
        'descricao',
        'valor',
        'data',
        'hora',
        'observacoes',
        'id_cliente',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}