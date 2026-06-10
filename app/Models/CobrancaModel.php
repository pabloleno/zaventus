<?php

namespace App\Models;

class CobrancaModel extends PadraoModel
{
    protected $table = 'cobrancas';
    protected $primaryKey = 'id_cobranca';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id_cobranca',
        'id_cliente',
        'titulo',
        'descricao',
        'valor_total',
        'quantidade_parcelas',
        'recorrencia',
        'intervalo_personalizado_dias',
        'data_inicio',
        'hora_cobranca',
        'juros_atraso',
        'juros_percentual',
        'lembrete_1_hora',
        'lembrete_1_dia',
        'lembrete_1_semana',
        'status',
        'observacoes',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}
