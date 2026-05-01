<?php

namespace App\Models;

class ContaPagarModel extends PadraoModel
{
    protected $table = 'contas_a_pagar';
    protected $primaryKey = 'id_conta';
    protected $allowedFields = [
        'id_conta',
        'status',
        'nome',
        'data_de_vencimento',
        'valor',
        'observacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
