<?php

namespace App\Models;

use CodeIgniter\Model;

class FormaDePagamentoModel extends Model
{
    protected $table = 'formas_de_pagamento';
    protected $primaryKey = 'id_forma';
    protected $allowedFields = [
        'id_forma',
        'nome',
        'codigo_nfce'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
