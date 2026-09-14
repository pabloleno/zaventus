<?php

namespace App\Models;

class FormaDePagamentoModel extends PadraoModel
{
    protected $table = 'formas_de_pagamento';
    protected $primaryKey = 'id_forma';
    protected $allowedFields = [
        'id_forma',
        'nome',
        'codigo_nfce',
        'id_integracao',
        'disponivel_produtos',
        'disponivel_servicos'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * O fluxo de vendas de produtos foi desativado na operacao atual.
     */
    public function paraProdutos(): array
    {
        return [];
    }

    /**
     * Retorna somente as formas habilitadas para o fluxo de servicos.
     */
    public function paraServicos(): array
    {
        return $this->where('disponivel_servicos', 1)->orderBy('nome')->findAll();
    }

    /**
     * Lista formas com o nome do provedor opcionalmente vinculado.
     */
    public function comIntegracao(): array
    {
        return $this
            ->select(
                'formas_de_pagamento.*, ' .
                'integracoes_pagamento.provedor AS integracao_provedor, ' .
                'integracoes_pagamento.nome AS integracao_nome, ' .
                'integracoes_pagamento.ambiente AS integracao_ambiente, ' .
                'integracoes_pagamento.ativo AS integracao_ativo, ' .
                'integracoes_pagamento.api_publica AS integracao_api_publica, ' .
                'integracoes_pagamento.ultimo_teste_em AS integracao_ultimo_teste_em, ' .
                'integracoes_pagamento.ultimo_teste_status AS integracao_ultimo_teste_status'
            )
            ->join('integracoes_pagamento', 'integracoes_pagamento.id_integracao = formas_de_pagamento.id_integracao', 'left')
            ->orderBy('formas_de_pagamento.nome')
            ->findAll();
    }

    /**
     * Confirma se o nome recebido pode ser usado no contexto informado.
     */
    public function disponivelPara(string $nome, string $contexto): bool
    {
        if ($contexto !== 'servicos') {
            return false;
        }

        return $this->where('nome', $nome)->where('disponivel_servicos', 1)->first() !== null;
    }
}
