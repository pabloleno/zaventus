<?php

namespace App\Libraries;

use App\Models\CobrancaModel;
use App\Models\CobrancaOcorrenciaModel;
use DateTimeImmutable;

class CobrancaRecorrente
{
    public const UNICA = 'Unica';
    public const DIARIA = 'Diaria';
    public const MENSAL = 'Mensal';
    public const ANUAL = 'Anual';
    public const PERSONALIZADA = 'Personalizada';

    private CobrancaModel $cobrancas;
    private CobrancaOcorrenciaModel $ocorrencias;

    /**
     * Inicializa os modelos usados pelo agendamento de cobrancas.
     */
    public function __construct()
    {
        $this->cobrancas = new CobrancaModel();
        $this->ocorrencias = new CobrancaOcorrenciaModel();
    }

    /**
     * Retorna as periodicidades aceitas pelo cadastro.
     */
    public static function opcoesRecorrencia(): array
    {
        return [
            self::UNICA => 'Unica',
            self::DIARIA => 'Diaria',
            self::MENSAL => 'Mensal',
            self::ANUAL => 'Anual',
            self::PERSONALIZADA => 'Personalizada',
        ];
    }

    /**
     * Sincroniza parcelas pendentes sem alterar ocorrencias ja realizadas.
     */
    public function sincronizar(array $cobranca): void
    {
        $idCobranca = (int) $cobranca['id_cobranca'];
        $quantidade = max(1, (int) $cobranca['quantidade_parcelas']);
        $valorTotal = round((float) $cobranca['valor_total'], 2);
        $existentes = [];
        $totalRealizado = 0.0;
        $quantidadeRealizada = 0;

        foreach ($this->ocorrencias->where('id_cobranca', $idCobranca)->findAll() as $ocorrencia) {
            $existentes[(int) $ocorrencia['numero_parcela']] = $ocorrencia;

            if ($ocorrencia['status'] === 'Realizada' && (int) $ocorrencia['numero_parcela'] <= $quantidade) {
                $totalRealizado += (float) $ocorrencia['valor'];
                $quantidadeRealizada++;
            }
        }

        $quantidadePendente = max(1, $quantidade - $quantidadeRealizada);
        $totalPendente = max(0, round($valorTotal - $totalRealizado, 2));
        $valorBase = floor(($totalPendente / $quantidadePendente) * 100) / 100;
        $indicePendente = 0;

        for ($numero = 1; $numero <= $quantidade; $numero++) {
            if (isset($existentes[$numero]) && $existentes[$numero]['status'] === 'Realizada') {
                continue;
            }

            $indicePendente++;
            $valor = $indicePendente === $quantidadePendente
                ? round($totalPendente - ($valorBase * ($quantidadePendente - 1)), 2)
                : $valorBase;
            $dados = [
                'id_cobranca' => $idCobranca,
                'numero_parcela' => $numero,
                'vencimento' => $this->vencimento($cobranca, $numero)->format('Y-m-d H:i:s'),
                'valor' => $valor,
                'status' => 'Pendente',
            ];

            if (isset($existentes[$numero])) {
                $dados['id_ocorrencia'] = $existentes[$numero]['id_ocorrencia'];
                $this->ocorrencias->save($dados);
                continue;
            }

            $this->ocorrencias->insert($dados);
        }

        foreach ($existentes as $numero => $ocorrencia) {
            if ($numero > $quantidade && $ocorrencia['status'] === 'Pendente') {
                $this->ocorrencias->delete($ocorrencia['id_ocorrencia']);
            }
        }
    }

    /**
     * Acrescenta uma parcela seguindo a periodicidade cadastrada.
     */
    public function estender(int $idCobranca): bool
    {
        $cobranca = $this->cobrancas->find($idCobranca);

        if (empty($cobranca) || $cobranca['recorrencia'] === self::UNICA) {
            return false;
        }

        $ultimaOcorrencia = $this->ocorrencias
            ->where('id_cobranca', $idCobranca)
            ->orderBy('numero_parcela', 'DESC')
            ->first();
        $valorAdicional = (float) ($ultimaOcorrencia['valor'] ?? 0);
        $cobranca['quantidade_parcelas'] = (int) $cobranca['quantidade_parcelas'] + 1;
        $cobranca['valor_total'] = round((float) $cobranca['valor_total'] + $valorAdicional, 2);
        $cobranca['status'] = 'Ativa';
        $this->cobrancas->save($cobranca);
        $this->sincronizar($cobranca);

        return true;
    }

    /**
     * Lista a proxima ocorrencia de cada cobranca ativa para a dashboard.
     */
    public function alertas(int $limite = 20): array
    {
        $agora = new DateTimeImmutable();
        $registros = $this->consultaPendentesAtivas()->findAll();
        $resumosParcelas = $this->resumosParcelas($this->idsCobrancas($registros));
        $alertas = [];
        $cobrancasListadas = [];

        foreach ($registros as $registro) {
            $idCobranca = (int) $registro['id_cobranca'];

            if (isset($cobrancasListadas[$idCobranca])) {
                continue;
            }

            $vencimento = new DateTimeImmutable($registro['vencimento']);
            $inicioAlerta = $this->inicioAlerta($registro, $vencimento);

            $registro['cliente'] = $this->nomeCliente($registro);
            $registro['status_alerta'] = $agora < $inicioAlerta ? 'Agendada' : $this->statusAlerta($vencimento, $agora);
            $registro['valor_com_juros'] = $this->valorComJuros($registro, $vencimento, $agora);
            $registro = $this->adicionarResumoParcelas($registro, $resumosParcelas[$idCobranca] ?? []);
            $alertas[] = $registro;
            $cobrancasListadas[$idCobranca] = true;

            if (count($alertas) >= $limite) {
                break;
            }
        }

        return $alertas;
    }

    /**
     * Lista todas as parcelas de hoje ou atrasadas que ainda exigem solucao.
     */
    public function alertasExigiveis(): array
    {
        $agora = new DateTimeImmutable();
        $fimHoje = $agora->setTime(23, 59, 59);
        $registros = $this->consultaPendentesAtivas()
            ->where('cobranca_ocorrencias.vencimento <=', $fimHoje->format('Y-m-d H:i:s'))
            ->findAll();
        $resumosParcelas = $this->resumosParcelas($this->idsCobrancas($registros));
        $alertas = [];

        foreach ($registros as $registro) {
            $vencimento = new DateTimeImmutable($registro['vencimento']);
            $idCobranca = (int) $registro['id_cobranca'];
            $registro['cliente'] = $this->nomeCliente($registro);
            $registro['status_alerta'] = $this->statusAlerta($vencimento, $agora);
            $registro['valor_com_juros'] = $this->valorComJuros($registro, $vencimento, $agora);
            $registro = $this->adicionarResumoParcelas($registro, $resumosParcelas[$idCobranca] ?? []);
            $alertas[] = $registro;
        }

        return $alertas;
    }

    /**
     * Resume o andamento das parcelas de cada cobranca informada.
     */
    public function resumosParcelas(array $idsCobrancas): array
    {
        $idsCobrancas = array_values(array_unique(array_filter(array_map('intval', $idsCobrancas))));

        if (empty($idsCobrancas)) {
            return [];
        }

        $registros = db_connect()
            ->table('cobranca_ocorrencias')
            ->select('id_cobranca')
            ->select('COUNT(*) AS total_ocorrencias', false)
            ->select("SUM(CASE WHEN status = 'Pendente' THEN 1 ELSE 0 END) AS pendentes", false)
            ->select("SUM(CASE WHEN status = 'Realizada' THEN 1 ELSE 0 END) AS realizadas", false)
            ->whereIn('id_cobranca', $idsCobrancas)
            ->where('deleted_at', null)
            ->groupBy('id_cobranca')
            ->get()
            ->getResultArray();

        $resumos = [];

        foreach ($registros as $registro) {
            $idCobranca = (int) $registro['id_cobranca'];
            $total = (int) $registro['total_ocorrencias'];
            $pendentes = (int) $registro['pendentes'];

            $resumos[$idCobranca] = [
                'total' => $total,
                'pendentes' => $pendentes,
                'realizadas' => max(0, $total - $pendentes),
            ];
        }

        return $resumos;
    }

    /**
     * Monta a consulta comum de parcelas pendentes pertencentes a cobrancas ativas.
     */
    private function consultaPendentesAtivas(): CobrancaOcorrenciaModel
    {
        return $this->ocorrencias
            ->select('cobranca_ocorrencias.*, cobrancas.titulo, cobrancas.quantidade_parcelas, cobrancas.juros_atraso, cobrancas.juros_percentual, cobrancas.lembrete_1_hora, cobrancas.lembrete_1_dia, cobrancas.lembrete_1_semana, clientes.nome, clientes.razao_social, clientes.whatsapp, clientes.celular, clientes.email')
            ->join('cobrancas', 'cobrancas.id_cobranca = cobranca_ocorrencias.id_cobranca AND cobrancas.deleted_at IS NULL')
            ->join('clientes', 'clientes.id_cliente = cobrancas.id_cliente', 'left')
            ->where('cobranca_ocorrencias.status', 'Pendente')
            ->where('cobrancas.status', 'Ativa')
            ->orderBy('cobranca_ocorrencias.vencimento', 'ASC');
    }

    /**
     * Define o nome legivel do cliente associado ao alerta.
     */
    private function nomeCliente(array $registro): string
    {
        return trim((string) ($registro['nome'] ?: $registro['razao_social'])) ?: 'Cliente nao informado';
    }

    /**
     * Extrai ids de cobrancas de uma lista de registros.
     */
    private function idsCobrancas(array $registros): array
    {
        return array_map(static fn (array $registro): int => (int) $registro['id_cobranca'], $registros);
    }

    /**
     * Inclui dados de progresso das parcelas no alerta.
     */
    private function adicionarResumoParcelas(array $registro, array $resumo): array
    {
        $totalParcelas = max(1, (int) ($registro['quantidade_parcelas'] ?? 0), (int) ($resumo['total'] ?? 0));
        $realizadas = min($totalParcelas, max(0, (int) ($resumo['realizadas'] ?? 0)));
        $pendentes = min($totalParcelas, max((int) ($resumo['pendentes'] ?? 0), $totalParcelas - $realizadas));

        $registro['total_parcelas'] = $totalParcelas;
        $registro['parcelas_realizadas'] = $realizadas;
        $registro['parcelas_restantes'] = $pendentes;
        $registro['rotulo_parcela'] = (int) $registro['numero_parcela'] . '/' . $totalParcelas;

        return $registro;
    }

    /**
     * Calcula o vencimento de uma parcela conforme a recorrencia.
     */
    private function vencimento(array $cobranca, int $numero): DateTimeImmutable
    {
        $data = new DateTimeImmutable($cobranca['data_inicio'] . ' ' . $cobranca['hora_cobranca']);
        $incremento = $numero - 1;

        if ($incremento === 0) {
            return $data;
        }

        return match ($cobranca['recorrencia']) {
            self::DIARIA => $data->modify("+{$incremento} days"),
            self::MENSAL => $this->adicionarMeses($data, $incremento),
            self::ANUAL => $this->adicionarAnos($data, $incremento),
            self::PERSONALIZADA => $data->modify('+' . ($incremento * max(1, (int) $cobranca['intervalo_personalizado_dias'])) . ' days'),
            default => $data,
        };
    }

    /**
     * Adiciona meses preservando o dia ou usando o ultimo dia valido.
     */
    private function adicionarMeses(DateTimeImmutable $data, int $meses): DateTimeImmutable
    {
        $mesDestino = $data->modify('first day of this month')->modify("+{$meses} months");
        $dia = min((int) $data->format('d'), (int) $mesDestino->format('t'));

        return $data->setDate((int) $mesDestino->format('Y'), (int) $mesDestino->format('m'), $dia);
    }

    /**
     * Adiciona anos preservando datas de fim de mes e anos bissextos.
     */
    private function adicionarAnos(DateTimeImmutable $data, int $anos): DateTimeImmutable
    {
        $ano = (int) $data->format('Y') + $anos;
        $mes = (int) $data->format('m');
        $ultimoDia = (int) $data->setDate($ano, $mes, 1)->format('t');

        return $data->setDate($ano, $mes, min((int) $data->format('d'), $ultimoDia));
    }

    /**
     * Determina quando um lembrete passa a aparecer na dashboard.
     */
    private function inicioAlerta(array $registro, DateTimeImmutable $vencimento): DateTimeImmutable
    {
        if ((int) $registro['lembrete_1_semana'] === 1) {
            return $vencimento->modify('-7 days');
        }
        if ((int) $registro['lembrete_1_dia'] === 1) {
            return $vencimento->modify('-1 day');
        }
        if ((int) $registro['lembrete_1_hora'] === 1) {
            return $vencimento->modify('-1 hour');
        }

        return $vencimento->setTime(0, 0);
    }

    /**
     * Classifica o alerta conforme a proximidade do vencimento.
     */
    private function statusAlerta(DateTimeImmutable $vencimento, DateTimeImmutable $agora): string
    {
        if ($vencimento < $agora) {
            return 'Atrasada';
        }

        return $vencimento->format('Y-m-d') === $agora->format('Y-m-d') ? 'Hoje' : 'Proxima';
    }

    /**
     * Calcula juros simples diarios apenas para exibicao do lembrete.
     */
    private function valorComJuros(array $registro, DateTimeImmutable $vencimento, DateTimeImmutable $agora): float
    {
        $valor = (float) $registro['valor'];

        if ((int) $registro['juros_atraso'] !== 1 || $vencimento >= $agora) {
            return $valor;
        }

        $dias = max(1, (int) $vencimento->setTime(0, 0)->diff($agora->setTime(0, 0))->format('%a'));

        return round($valor * (1 + (((float) $registro['juros_percentual'] / 100) * $dias)), 2);
    }
}
