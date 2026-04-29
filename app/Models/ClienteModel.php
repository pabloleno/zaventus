<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $allowedFields = [
        'tipo',
        'nome',
        'data_de_nascimento',
        'rg',
        'cpf',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'ie',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'UF',
        'codigo_do_municipio',
        'celular',
        'comercial',
        'residencial',
        'email',
        'anotacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
