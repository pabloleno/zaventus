<?php

namespace App\Models;

class ProvisorioReposicaoProdutosPorXmlModel extends PadraoModel
{
    protected $table = 'provisorio_reposicao_produtos_por_xml';
    protected $primaryKey = 'id_produto_provisorio';
    protected $allowedFields = [
        'id_produto_provisorio',
        'nome',
        'quantidade_da_reposicao',
        'id_produto'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
