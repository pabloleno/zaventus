<?php

namespace App\Models;

class SaidaDeMercadoriaModel extends PadraoModel
{
    protected $table = 'saida_de_mercadorias';
    protected $primaryKey = 'id_saida';
    protected $allowedFields = [
        'id_saida',
        'data',
        'hora',
        'quantidade',
        'observacoes',
        'id_produto',
        'id_ordem',
        'id_servico_os',
        'created_by',
        'estornado_at',
        'estornado_by',
        'estorno_motivo',
        'chave_operacao',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
