<?php

namespace App\Models;

class CobrancaOcorrenciaModel extends PadraoModel
{
    protected $table = 'cobranca_ocorrencias';
    protected $primaryKey = 'id_ocorrencia';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id_ocorrencia',
        'id_cobranca',
        'numero_parcela',
        'vencimento',
        'valor',
        'status',
        'concluida_em',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}
