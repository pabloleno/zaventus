<?php

namespace App\Models;

class TecnicoModel extends PadraoModel
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
        'celular',
        'whatsapp',
        'telefone_fixo',
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
