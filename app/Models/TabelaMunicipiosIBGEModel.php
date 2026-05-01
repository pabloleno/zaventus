<?php

namespace App\Models;

class TabelaMunicipiosIBGEModel extends PadraoModel
{
    protected $table = 'tabela_municipios_ibge';
    protected $primaryKey = 'id_tabela';
    protected $allowedFields = [
        'id_tabela',
        'codigo',
        'municipio'
    ];
}
