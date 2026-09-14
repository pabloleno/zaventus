<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;

class FaturamentoNegocio
{
    private $db;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    /**
     * Consulta as vendas de produtos do periodo, respeitando os filtros informados.
     */
    public function vendasProdutos(string $dataInicio, string $dataFinal, array $filtros = []): array
    {
        $builder = $this->db->table('vendas')
            ->select("vendas.*, COALESCE(NULLIF(clientes.nome, ''), clientes.razao_social) AS nome_cliente", false)
            ->join('clientes', 'clientes.id_cliente = vendas.id_cliente', 'left')
            ->where('vendas.deleted_at', null)
            ->where('vendas.data >=', $dataInicio)
            ->where('vendas.data <=', $dataFinal);

        if (!empty($filtros['id_cliente'])) {
            $builder->where('vendas.id_cliente', $filtros['id_cliente']);
        }

        if (!empty($filtros['id_vendedor'])) {
            $builder->where('vendas.id_vendedor', $filtros['id_vendedor']);
        }

        return $builder
            ->orderBy('vendas.data', 'DESC')
            ->orderBy('vendas.hora', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Consulta ordens concretizadas e calcula o faturamento de servicos do periodo.
     */
    public function ordensServicos(string $dataInicio, string $dataFinal, array $filtros = []): array
    {
        $builder = $this->db->table('ordens_de_servicos')
            ->select("ordens_de_servicos.*, COALESCE(NULLIF(clientes.nome, ''), clientes.razao_social) AS nome_cliente", false)
            ->join('clientes', 'clientes.id_cliente = ordens_de_servicos.id_cliente', 'left')
            ->where('ordens_de_servicos.situacao', 'Concretizada')
            ->where('ordens_de_servicos.data_de_saida >=', $dataInicio)
            ->where('ordens_de_servicos.data_de_saida <=', $dataFinal);
        $this->semExcluidos($builder, 'ordens_de_servicos.deleted_at');

        if (!empty($filtros['id_cliente'])) {
            $builder->where('ordens_de_servicos.id_cliente', $filtros['id_cliente']);
        }

        if (!empty($filtros['id_vendedor'])) {
            $builder->where('ordens_de_servicos.id_vendedor', $filtros['id_vendedor']);
        }

        $ordens = $builder
            ->orderBy('ordens_de_servicos.data_de_saida', 'DESC')
            ->orderBy('ordens_de_servicos.hora_de_saida', 'DESC')
            ->get()
            ->getResultArray();
        if ($ordens === []) {
            return [];
        }
        $consultaItens = $this->db->table('servicos_mao_de_obra_da_os')->whereIn('id_ordem', array_column($ordens, 'id_ordem'));
        if ($this->db->fieldExists('removido_at', 'servicos_mao_de_obra_da_os')) {
            $consultaItens->where('removido_at', null);
        }
        $itensPorOrdem = [];
        foreach ($consultaItens->get()->getResultArray() as $item) {
            $itensPorOrdem[$item['id_ordem']][] = $item;
        }
        foreach ($ordens as &$ordem) {
            $ordem['valor_total'] = AtendimentoGrafica::totais($ordem, $itensPorOrdem[$ordem['id_ordem']] ?? [])['total'];
        }
        unset($ordem);
        return $ordens;
    }

    /**
     * Calcula o total de produtos.
     */
    public function totalProdutos(string $dataInicio, string $dataFinal): float
    {
        $resultado = $this->db->table('vendas')
            ->selectSum('valor_a_pagar')
            ->where('deleted_at', null)
            ->where('data >=', $dataInicio)
            ->where('data <=', $dataFinal)
            ->get()
            ->getRowArray();

        return (float) ($resultado['valor_a_pagar'] ?? 0);
    }

    /**
     * Calcula o total de servicos.
     */
    public function totalServicos(string $dataInicio, string $dataFinal): float
    {
        $total = 0.0;

        foreach ($this->ordensServicos($dataInicio, $dataFinal) as $ordem) {
            $total += (float) ($ordem['valor_total'] ?? 0);
        }

        return round($total, 2);
    }

    /**
     * Calcula o total de lancamentos.
     */
    public function totalLancamentos(string $dataInicio, string $dataFinal, string $tipoNegocio): float
    {
        $builder = $this->consultaLancamentos(true)
            ->select('SUM(l.valor) AS valor', false)
            ->where('l.data >=', $dataInicio)
            ->where('l.data <=', $dataFinal);

        if ($tipoNegocio !== TipoNegocio::TODOS) {
            $builder->where('l.tipo_negocio', $tipoNegocio);
        }

        $resultado = $builder->get()->getRowArray();

        return (float) ($resultado['valor'] ?? 0);
    }

    /**
     * Monta o resumo consolidado de faturamento para o periodo e tipo de negocio.
     */
    public function resumo(string $dataInicio, string $dataFinal, string $tipoNegocio): array
    {
        $produtos = in_array($tipoNegocio, [TipoNegocio::TODOS, TipoNegocio::PRODUTOS], true)
            ? $this->totalProdutos($dataInicio, $dataFinal)
            : 0.0;
        $servicos = in_array($tipoNegocio, [TipoNegocio::TODOS, TipoNegocio::SERVICOS], true)
            ? $this->totalServicos($dataInicio, $dataFinal)
            : 0.0;
        $lancamentos = $this->totalLancamentos($dataInicio, $dataFinal, $tipoNegocio);

        return [
            'produtos' => $produtos,
            'servicos' => $servicos,
            'lancamentos' => $lancamentos,
            'total' => $produtos + $servicos + $lancamentos,
        ];
    }

    /**
     * Agrupa diariamente o faturamento do mes informado.
     */
    public function resumoDiarioMes(int $ano, int $mes, string $tipoNegocio): array
    {
        $inicio = sprintf('%04d-%02d-01', $ano, $mes);
        $final = date('Y-m-t', strtotime($inicio));
        $dias = [];

        for ($dia = 1; $dia <= 31; $dia++) {
            $dias[$dia] = [
                'dia' => $dia,
                'produtos' => 0.0,
                'servicos' => 0.0,
                'lancamentos' => 0.0,
                'total' => 0.0,
            ];
        }

        if (in_array($tipoNegocio, [TipoNegocio::TODOS, TipoNegocio::PRODUTOS], true)) {
            $vendas = $this->db->table('vendas')
                ->select('data, SUM(valor_a_pagar) AS total', false)
                ->where('deleted_at', null)
                ->where('data >=', $inicio)
                ->where('data <=', $final)
                ->groupBy('data')
                ->get()
                ->getResultArray();

            foreach ($vendas as $venda) {
                $dias[(int) date('j', strtotime($venda['data']))]['produtos'] = (float) $venda['total'];
            }
        }

        if (in_array($tipoNegocio, [TipoNegocio::TODOS, TipoNegocio::SERVICOS], true)) {
            foreach ($this->ordensServicos($inicio, $final) as $ordem) {
                $dia = (int) date('j', strtotime($ordem['data_de_saida']));
                $dias[$dia]['servicos'] += (float) $ordem['valor_total'];
            }
        }

        $lancamentos = $this->consultaLancamentos(true)
            ->select('l.data, SUM(l.valor) AS total', false)
            ->where('l.data >=', $inicio)
            ->where('l.data <=', $final);
        if ($tipoNegocio !== TipoNegocio::TODOS) {
            $lancamentos->where('l.tipo_negocio', $tipoNegocio);
        }

        foreach ($lancamentos->groupBy('l.data')->get()->getResultArray() as $lancamento) {
            $dias[(int) date('j', strtotime($lancamento['data']))]['lancamentos'] = (float) $lancamento['total'];
        }

        foreach ($dias as &$dia) {
            $dia['total'] = $dia['produtos'] + $dia['servicos'] + $dia['lancamentos'];
        }
        unset($dia);

        return array_values($dias);
    }

    /**
     * Consulta unica para caixa e relatorios. Recebimento liquida a OS; nao fatura novamente.
     * O chamador escolhe select l.* para linhas ou SUM(l.valor) para agregados.
     */
    public function consultaLancamentos(bool $somenteReceitas = false): BaseBuilder
    {
        $consulta = $this->db->table('lancamentos l');
        $this->semExcluidos($consulta, 'l.deleted_at');
        if ($this->db->fieldExists('id_recebimento', 'lancamentos')) {
            $consulta->join('pagamentos_do_cliente r', 'r.id_pagamento = l.id_recebimento', 'left')
                ->groupStart()->where('l.id_recebimento', null)->orGroupStart()
                ->where('r.id_pagamento IS NOT NULL', null, false)->where('r.estornado_at', null)->where('r.deleted_at', null)
                ->groupEnd()->groupEnd();
        }
        if ($somenteReceitas && $this->db->fieldExists('natureza', 'lancamentos')) {
            $consulta->where('l.natureza', 'receita');
        }
        return $consulta;
    }

    private function semExcluidos(BaseBuilder $consulta, string $campo): void
    {
        $consulta->groupStart()->where($campo, null)->orWhere("CAST({$campo} AS CHAR) = '0000-00-00 00:00:00'", null, false)->groupEnd();
    }
}
