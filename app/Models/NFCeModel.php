<?php

namespace App\Models;

use CodeIgniter\Model;

class NFCeModel extends Model
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