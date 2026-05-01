<?php

namespace App\Models;

class CaixaModel extends PadraoModel
{
    protected $table = 'caixas';
    protected $primaryKey = 'id_caixa';
    protected $allowedFields = [
        'id_caixa',
        'data_de_abertura',
        'data_de_fechamento',
        'hora_de_abertura',
        'hora_de_fechamento',
        'valor_inicial',
        'valor_total',
        'valor_de_fechamento',
        'observacoes',
        'status'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
