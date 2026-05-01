<?php

namespace App\Models;

class ServicoMaoDeObraModel extends PadraoModel
{
    protected $table = 'servicos_mao_de_obra';
    protected $primaryKey = 'id_servico';
    protected $allowedFields = [
        'id_servico',
        'nome',
        'descricao',
        'valor',
        'observacoes',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}