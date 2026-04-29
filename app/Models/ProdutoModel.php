<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';
    protected $allowedFields = [
        'id_produto',
        'nome',
        'unidade',
        'codigo_de_barras',
        'localizacao',
        'quantidade',
        'quantidade_minima',
        'margem_de_lucro',
        'valor_de_custo',
        'valor_de_venda',
        'lucro',
        'NCM',
        'CSOSN',
        'CFOP',
        'arquivo',
        'id_categoria',
        'id_fornecedor',
        'validade'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
