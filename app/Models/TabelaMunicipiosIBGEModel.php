<?php

namespace App\Models;

use CodeIgniter\Model;

class TabelaMunicipiosIBGEModel extends Model
{
    protected $table = 'tabela_municipios_ibge';
    protected $primaryKey = 'id_tabela';
    protected $allowedFields = [
        'id_tabela',
        'codigo',
        'municipios'
    ];
}