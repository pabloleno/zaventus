<?php

namespace App\Models;

class ConfigEmpresaModel extends PadraoModel
{
    protected $table = 'config_empresa';
    protected $primaryKey = 'id_config';
    protected $allowedFields = [
        'id_config',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'celular',
        'whatsapp',
        'telefone_fixo',
        'telefone',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'UF',
        'codigo_do_municipio',
        'endereco',
        'idioma',
        'fuso_horario'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
