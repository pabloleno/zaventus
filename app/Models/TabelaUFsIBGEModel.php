<?php

namespace App\Models;

use CodeIgniter\Model;

class TabelaUFsIBGEModel extends Model
{
    protected $table = 'tabela_ufs_ibge';
    protected $primaryKey = 'id_tabela';
    protected $allowedFields = [
        'id_tabela',
        'unidade_da_federacao',
        'UF'
    ];
}