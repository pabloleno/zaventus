<?php

namespace App\Models;

use App\Libraries\OrcamentoCalculo;

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
        'tipo_preco',
        'unidade',
        'largura_padrao',
        'altura_padrao',
        'unidade_dimensao',
        'tipo_execucao',
        'arte_padrao',
        'necessita_instalacao',
        'ativo',
        'imagem',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected function aplicaPadraoCampos(array $eventData): array
    {
        $originais = $eventData['data'] ?? [];
        $eventData = parent::aplicaPadraoCampos($eventData);

        // O catálogo comporta nomes de 128 caracteres e preços decimais exatos.
        if (isset($originais['nome'])) {
            $eventData['data']['nome'] = trim((string) $originais['nome']);
        }
        if (isset($originais['valor'])) {
            $eventData['data']['valor'] = OrcamentoCalculo::decimal($originais['valor'], 2);
        }

        return $eventData;
    }

    protected function aplicaPadraoCamposBatch(array $eventData): array
    {
        foreach ($eventData['data'] ?? [] as $indice => $linha) {
            $eventData['data'][$indice] = $this->aplicaPadraoCampos(['data' => $linha])['data'];
        }

        return $eventData;
    }
}
