<?php

namespace App\Models;

class VendaModel extends PadraoModel
{
    protected $table = 'vendas';
    protected $primaryKey = 'id_venda';
    protected $allowedFields = [
        'id_venda',
        'valor_a_pagar',
        'desconto',
        'valor_recebido',
        'troco',
        'forma_de_pagamento',
        'data',
        'hora',
        'id_cliente',
        'id_vendedor',
        'id_caixa'
    ];
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
