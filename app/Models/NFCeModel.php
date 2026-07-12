<?php

namespace App\Models;

class NFCeModel extends PadraoModel
{
    protected $table = 'nfces';
    protected $primaryKey = 'id_nfce';
    protected $allowedFields = [
        'id_nfce',
        'data',
        'hora',
        'chave',
        'xml',
        'protocolo',
        'arquivo_xml',
        'status',
        'ambiente',
        'serie',
        'numero',
        'recibo',
        'nprot',
        'cstat',
        'xmotivo',
        'data_autorizacao',
        'data_cancelamento',
        'xml_cancelamento',
        'xml_protocolado_cancelamento',
        'ultimo_retorno_sefaz',
        'ultima_consulta_em',
        'erro',
        'id_venda'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
