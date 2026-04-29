<?php

namespace App\Models;

use CodeIgniter\Model;

class FuncionarioModel extends Model
{
    protected $table = 'funcionarios';
    protected $primaryKey = 'id_funcionario';
    protected $allowedFields = [
        'id_funcionario',
        'status',
        'nome',
        'data_de_nascimento',
        'rg',
        'cpf',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'celular',
        'comercial',
        'residencial',
        'email',
        'cargo',
        'data_de_contratacao',
        'data_inicio_das_atividades',
        'salario',
        'detalhes_da_atividade',
        'anotacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
