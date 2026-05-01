<?php

namespace App\Models;

class TabelaUFsIBGEModel extends PadraoModel
{
    protected $table = 'tabela_ufs_ibge';
    protected $primaryKey = 'id_tabela';
    protected $allowedFields = [
        'id_tabela',
        'unidade_da_federacao',
        'UF'
    ];
}