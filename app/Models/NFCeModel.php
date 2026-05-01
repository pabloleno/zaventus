<?php

namespace App\Models;

class NFCeModel extends PadraoModel
{
    protected $table = 'nfces';
    protected $primaryKey = 'id_nfce';
    protected $allowedFields = [
        'id_nfce',
        'data',
        'hora',
        'chave',
        'xml',
        'arquivo_xml',
        'status',
        'erro',
        'id_venda'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}