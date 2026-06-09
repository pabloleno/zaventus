<?php

namespace App\Models;

class ContaReceberModel extends PadraoModel
{
    protected $table = 'contas_a_receber';
    protected $primaryKey = 'id_conta';
    protected $allowedFields = [
        'id_conta',
        'status',
        'tipo_negocio',
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
