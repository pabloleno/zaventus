<?php

namespace App\Models;

use CodeIgniter\Model;

class TecnicoModel extends Model
{
    protected $table = 'tecnicos';
    protected $primaryKey = 'id_tecnico';
    protected $allowedFields = [
        'id_tecnico',
        'nome',
        'cpf',
        'rg',
        'data_de_nascimento',
        'sexo',
        'email',
        'comissao',
        'observacoes',
        'foto',
        'fixo',
        'celular_1',
        'celular_2',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}