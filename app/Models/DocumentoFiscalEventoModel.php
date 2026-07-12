<?php

namespace App\Models;

class DocumentoFiscalEventoModel extends PadraoModel
{
    protected $table = 'documentos_fiscais_eventos';
    protected $primaryKey = 'id_evento';
    protected $allowedFields = [
        'id_evento',
        'modelo',
        'id_documento',
        'tipo_evento',
        'chave',
        'protocolo',
        'cstat',
        'xmotivo',
        'justificativa',
        'xml_envio',
        'xml_retorno',
        'status',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}
