<?php

namespace App\Models;

class FornecedorModel extends PadraoModel
{
    protected $table = 'fornecedores';
    protected $primaryKey = 'id_fornecedor';
    protected $allowedFields = [
        'id_fornecedor',
        'nome_do_representante',
        'nome_da_empresa',
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
        'whatsapp',
        'telefone_fixo',
        'comercial',
        'email',
        'anotacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
