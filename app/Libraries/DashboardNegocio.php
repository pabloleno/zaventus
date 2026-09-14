<?php

namespace App\Libraries;

class DashboardNegocio
{
    private $db;
    private $faturamento;
    private $cobrancas;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    public function __construct()
    {
        $this->db = db_connect();
        $this->faturamento = new FaturamentoNegocio();
        $this->cobrancas = new CobrancaRecorrente();
    }

    /**
     * Monta o conjunto completo de indicadores da dashboard.
     */
    public function montar(int $ano, int $mes): array
    {
        $inicio = sprintf('%04d-%02d-01', $ano, $mes);
        $final = date('Y-m-t', strtotime($inicio));
        $faturamentoServicos = $this->faturamento->totalServicos($inicio, $final);
        $quantidadeServicos = $this->quantidadeServicos($inicio, $final);
        $contasPendentes = $this->contasPendentes();

        return [
            'periodo' => [
                'ano' => $ano,
                'mes' => $mes,
                'inicio' => $inicio,
                'final' => $final,
            ],
            'faturamento' => [
                'servicos' => $faturamentoServicos,
                'total' => $faturamentoServicos,
            ],
            'operacao' => [
                'os_concretizadas' => $quantidadeServicos,
                'ticket_servicos' => $this->media($faturamentoServicos, $quantidadeServicos),
                'os_abertas' => $this->quantidadeOsAbertas(),
                'atendimento' => $this->atendimentoAtual(),
            ],
            'mensal' => $this->faturamentoMensal($ano),
            'financeiro' => $this->financeiroPorTipo($contasPendentes),
            'movimentacao' => $this->movimentacaoPorTipo($inicio, $final),
            'agenda' => array_slice($contasPendentes, 0, 10),
            'cobrancas_alerta' => $this->cobrancas->alertas(10),
        ];
    }

    /**
     * Calcula a quantidade de servicos.
     */
    private function quantidadeServicos(string $inicio, string $final): int
    {
        return $this->db->table('ordens_de_servicos')
            ->where('situacao', 'Concretizada')
            ->groupStart()->where('deleted_at', null)->orWhere("CAST(deleted_at AS CHAR) = '0000-00-00 00:00:00'", null, false)->groupEnd()
            ->where('data_de_saida >=', $inicio)
            ->where('data_de_saida <=', $final)
            ->countAllResults();
    }

    /**
     * Calcula a quantidade de ordens de servico em aberto.
     */
    private function quantidadeOsAbertas(): int
    {
        return $this->db->table('ordens_de_servicos')
            ->whereIn('situacao', ['Em aberto', 'Em andamento', 'Aberto'])
            ->groupStart()->where('deleted_at', null)->orWhere("CAST(deleted_at AS CHAR) = '0000-00-00 00:00:00'", null, false)->groupEnd()
            ->countAllResults();
    }

    private function atendimentoAtual(): ?array
    {
        if (! $this->db->fieldExists('status_operacional', 'ordens_de_servicos')) {
            return null;
        }
        $resumo = ['status' => array_fill_keys(array_keys(AtendimentoGrafica::STATUS), 0), 'instalacoes_hoje' => 0, 'atrasados' => 0];
        $hoje = date('Y-m-d');
        foreach ($this->db->table('ordens_de_servicos')->where('deleted_at', null)->get()->getResultArray() as $ordem) {
            $status = AtendimentoGrafica::status($ordem);
            $resumo['status'][$status]++;
            if (! in_array($status, ['concluido', 'cancelado'], true)
                && ! empty($ordem['previsao_conclusao']) && $ordem['previsao_conclusao'] < $hoje) {
                $resumo['atrasados']++;
            }
            if ($status === 'instalacao_agendada' && substr((string) ($ordem['execucao_prevista'] ?? ''), 0, 10) === $hoje) {
                $resumo['instalacoes_hoje']++;
            }
        }
        return $resumo;
    }

    /**
     * Agrupa o faturamento de servicos por mes.
     */
    private function faturamentoMensal(int $ano): array
    {
        $mensal = [];

        for ($mes = 1; $mes <= 12; $mes++) {
            $mensal[$mes] = [
                'mes' => $mes,
                'servicos' => 0.0,
            ];
        }

        foreach ($this->faturamento->ordensServicos(sprintf('%04d-01-01', $ano), sprintf('%04d-12-31', $ano)) as $ordem) {
            $mes = (int) date('n', strtotime($ordem['data_de_saida']));
            $mensal[$mes]['servicos'] += (float) ($ordem['valor_total'] ?? 0);
        }

        return array_values($mensal);
    }

    /**
     * Consulta contas a pagar e receber que ainda exigem liquidacao.
     */
    private function contasPendentes(): array
    {
        $contas = [];

        foreach ([
            'contas_a_receber' => 'receber',
            'contas_a_pagar' => 'pagar',
        ] as $tabela => $natureza) {
            $registros = $this->db->table($tabela)
                ->select('id_conta, status, tipo_negocio, nome, data_de_vencimento, valor')
                ->whereIn('status', ['Aberta', 'Vencida'])
                ->groupStart()->where('deleted_at', null)->orWhere("CAST(deleted_at AS CHAR) = '0000-00-00 00:00:00'", null, false)->groupEnd()
                ->get()
                ->getResultArray();

            foreach ($registros as $registro) {
                $registro['natureza'] = $natureza;
                $registro['tipo_negocio'] = $this->tipoNegocio($registro['tipo_negocio'] ?? null);
                $registro['status_dashboard'] = $this->statusConta($registro);
                $contas[] = $registro;
            }
        }

        usort($contas, static function (array $a, array $b): int {
            return strcmp((string) $a['data_de_vencimento'], (string) $b['data_de_vencimento']);
        });

        return $contas;
    }

    /**
     * Agrupa contas abertas e vencidas por tipo de negocio.
     */
    private function financeiroPorTipo(array $contas): array
    {
        $financeiro = [];

        foreach ([TipoNegocio::PRODUTOS, TipoNegocio::SERVICOS, TipoNegocio::GERAL] as $tipo) {
            $financeiro[$tipo] = [
                'receber_aberta' => 0.0,
                'receber_vencida' => 0.0,
                'pagar_aberta' => 0.0,
                'pagar_vencida' => 0.0,
                'quantidade_receber' => 0,
                'quantidade_pagar' => 0,
                'total_receber' => 0.0,
                'total_pagar' => 0.0,
                'saldo_previsto' => 0.0,
            ];
        }

        foreach ($contas as $conta) {
            $tipo = $conta['tipo_negocio'];
            $natureza = $conta['natureza'];
            $status = strtolower($conta['status_dashboard']);
            $valor = (float) $conta['valor'];

            $financeiro[$tipo]["{$natureza}_{$status}"] += $valor;
            $financeiro[$tipo]["quantidade_{$natureza}"]++;
        }

        foreach ($financeiro as &$dados) {
            $dados['total_receber'] = $dados['receber_aberta'] + $dados['receber_vencida'];
            $dados['total_pagar'] = $dados['pagar_aberta'] + $dados['pagar_vencida'];
            $dados['saldo_previsto'] = $dados['total_receber'] - $dados['total_pagar'];
        }
        unset($dados);

        return $financeiro;
    }

    /**
     * Agrupa lancamentos e despesas do periodo por tipo de negocio.
     */
    private function movimentacaoPorTipo(string $inicio, string $final): array
    {
        $movimentacao = [];

        foreach ([TipoNegocio::PRODUTOS, TipoNegocio::SERVICOS, TipoNegocio::GERAL] as $tipo) {
            $movimentacao[$tipo] = [
                'lancamentos' => 0.0,
                'despesas' => 0.0,
            ];
        }

        foreach ([
            'lancamentos' => 'lancamentos',
            'despesas' => 'despesas',
        ] as $tabela => $campo) {
            $prefixo = $tabela === 'lancamentos' ? 'l.' : '';
            $consulta = $tabela === 'lancamentos' ? $this->faturamento->consultaLancamentos(true) : $this->db->table($tabela);
            $resultados = $consulta
                ->select("{$prefixo}tipo_negocio, SUM({$prefixo}valor) AS total", false)
                ->where("{$prefixo}data >=", $inicio)
                ->where("{$prefixo}data <=", $final)
                ->groupBy("{$prefixo}tipo_negocio")
                ->get()
                ->getResultArray();

            foreach ($resultados as $resultado) {
                $tipo = $this->tipoNegocio($resultado['tipo_negocio'] ?? null);
                $movimentacao[$tipo][$campo] = (float) $resultado['total'];
            }
        }

        return $movimentacao;
    }

    /**
     * Normaliza o tipo de negocio usado nos indicadores financeiros.
     */
    private function tipoNegocio($tipo): string
    {
        $tipo = trim((string) $tipo);

        return in_array($tipo, [TipoNegocio::PRODUTOS, TipoNegocio::SERVICOS], true)
            ? $tipo
            : TipoNegocio::GERAL;
    }

    /**
     * Determina se uma conta deve ser exibida como aberta ou vencida.
     */
    private function statusConta(array $conta): string
    {
        if (($conta['status'] ?? '') === 'Vencida' || ($conta['data_de_vencimento'] ?? '') < date('Y-m-d')) {
            return 'Vencida';
        }

        return 'Aberta';
    }

    /**
     * Calcula uma media protegendo a divisao por zero.
     */
    private function media(float $valor, int $quantidade): float
    {
        return $quantidade > 0 ? round($valor / $quantidade, 2) : 0.0;
    }
}
