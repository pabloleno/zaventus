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
     * Lista as formas apropriadas para vendas de produtos.
     */
    public function paraProdutos(): array
    {
        return $this->where('disponivel_produtos', 1)->orderBy('nome')->findAll();
    }

    /**
     * Lista as formas apropriadas para vendas de servicos.
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
            ->select('formas_de_pagamento.*, integracoes_pagamento.nome AS integracao_nome')
            ->join('integracoes_pagamento', 'integracoes_pagamento.id_integracao = formas_de_pagamento.id_integracao', 'left')
            ->orderBy('formas_de_pagamento.nome')
            ->findAll();
    }

    /**
     * Confirma se o nome recebido pode ser usado no contexto informado.
     */
    public function disponivelPara(string $nome, string $contexto): bool
    {
        $campo = $contexto === 'servicos' ? 'disponivel_servicos' : 'disponivel_produtos';

        return $this->where('nome', $nome)->where($campo, 1)->first() !== null;
    }
}
