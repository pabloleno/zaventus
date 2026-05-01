<?php

namespace App\Models;

class NFeModel extends PadraoModel
{
    protected $table = 'nfes';
    protected $primaryKey = 'id_nfe';
    protected $allowedFields = [
        'id_nfe',
        'data',
        'hora',
        'chave',
        'xml',
        'protocolo',
        'status',
        'erro',
        'xml_protocolado_cancelamento',
        'id_venda'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}