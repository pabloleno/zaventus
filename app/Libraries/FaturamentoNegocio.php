<?php

namespace App\Libraries;

class FaturamentoNegocio
{
    private $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

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

    public function ordensServicos(string $dataInicio, string $dataFinal, array $filtros = []): array
    {
        $builder = $this->db->table('ordens_de_servicos')
            ->select(
                "ordens_de_servicos.*, COALESCE(NULLIF(clientes.nome, ''), clientes.razao_social) AS nome_cliente, "
                . 'COALESCE((SELECT SUM(servicos_mao_de_obra_da_os.quantidade * servicos_mao_de_obra_da_os.valor) '
                . 'FROM servicos_mao_de_obra_da_os WHERE servicos_mao_de_obra_da_os.id_ordem = ordens_de_servicos.id_ordem), 0) '
                . '+ ordens_de_servicos.frete + ordens_de_servicos.outros - ordens_de_servicos.desconto AS valor_total',
                false
            )
            ->join('clientes', 'clientes.id_cliente = ordens_de_servicos.id_cliente', 'left')
            ->where('ordens_de_servicos.situacao', 'Concretizada')
            ->where('ordens_de_servicos.data_de_saida >=', $dataInicio)
            ->where('ordens_de_servicos.data_de_saida <=', $dataFinal);

        if (!empty($filtros['id_cliente'])) {
            $builder->where('ordens_de_servicos.id_cliente', $filtros['id_cliente']);
        }

        if (!empty($filtros['id_vendedor'])) {
            $builder->where('ordens_de_servicos.id_vendedor', $filtros['id_vendedor']);
        }

        return $builder
            ->orderBy('ordens_de_servicos.data_de_saida', 'DESC')
            ->orderBy('ordens_de_servicos.hora_de_saida', 'DESC')
            ->get()
            ->getResultArray();
    }

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

    public function totalServicos(string $dataInicio, string $dataFinal): float
    {
        $total = 0.0;

        foreach ($this->ordensServicos($dataInicio, $dataFinal) as $ordem) {
            $total += (float) ($ordem['valor_total'] ?? 0);
        }

        return round($total, 2);
    }

    public function totalLancamentos(string $dataInicio, string $dataFinal, string $tipoNegocio): float
    {
        $builder = $this->db->table('lancamentos')
            ->selectSum('valor')
            ->where('data >=', $dataInicio)
            ->where('data <=', $dataFinal);

        if ($tipoNegocio !== TipoNegocio::TODOS) {
            $builder->where('tipo_negocio', $tipoNegocio);
        }

        $resultado = $builder->get()->getRowArray();

        return (float) ($resultado['valor'] ?? 0);
    }

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

        $lancamentos = $this->db->table('lancamentos')
            ->select('data, SUM(valor) AS total', false)
            ->where('data >=', $inicio)
            ->where('data <=', $final);
        if ($tipoNegocio !== TipoNegocio::TODOS) {
            $lancamentos->where('tipo_negocio', $tipoNegocio);
        }

        foreach ($lancamentos->groupBy('data')->get()->getResultArray() as $lancamento) {
            $dias[(int) date('j', strtotime($lancamento['data']))]['lancamentos'] = (float) $lancamento['total'];
        }

        foreach ($dias as &$dia) {
            $dia['total'] = $dia['produtos'] + $dia['servicos'] + $dia['lancamentos'];
        }
        unset($dia);

        return array_values($dias);
    }
}
