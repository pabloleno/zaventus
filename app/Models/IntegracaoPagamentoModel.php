<?php

namespace App\Models;

class IntegracaoPagamentoModel extends PadraoModel
{
    protected $table = 'integracoes_pagamento';
    protected $primaryKey = 'id_integracao';
    protected $allowedFields = [
        'id_integracao',
        'provedor',
        'nome',
        'descricao',
        'documentacao_url',
        'tipo_autenticacao',
        'ambiente',
        'credencial_publica',
        'credencial_secreta',
        'ativo',
        'api_publica',
        'observacoes',
        'ultimo_teste_em',
        'ultimo_teste_status',
        'ultimo_teste_mensagem',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}
