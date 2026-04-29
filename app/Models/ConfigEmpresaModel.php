<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigEmpresaModel extends Model
{
    protected $table = 'config_empresa';
    protected $primaryKey = 'id_config';
    protected $allowedFields = [
        'id_config',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'telefone',
        'endereco'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}