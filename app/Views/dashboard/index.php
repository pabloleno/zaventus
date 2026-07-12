<?php
    $session = session();
    $periodo = $dashboard['periodo'];
    $faturamento = $dashboard['faturamento'];
    $operacao = $dashboard['operacao'];
    $financeiro = $dashboard['financeiro'];
    $movimentacao = $dashboard['movimentacao'];
    $agenda = $dashboard['agenda'];
    $cobrancasAlerta = $dashboard['cobrancas_alerta'] ?? [];
    $periodoAutomatico = $periodo_automatico ?? true;
    $meses = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro',
    ];
    $mesesCurtos = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $moeda = static fn ($valor): string => 'R$ ' . number_format((float) $valor, 2, ',', '.');
    $segmentos = [
        [
            'tipo' => \App\Libraries\TipoNegocio::PRODUTOS,
            'slug' => 'produtos',
            'titulo' => 'Produtos',
            'subtitulo' => 'Vendas de produtos, inclusive peças vendidas em OS',
            'icone' => 'fas fa-boxes',
            'cor' => '#0f766e',
            'cor_clara' => '#ccfbf1',
            'faturamento' => $faturamento['produtos'],
            'quantidade' => $operacao['vendas_produtos'],
            'rotulo_quantidade' => 'vendas no período',
            'ticket' => $operacao['ticket_produtos'],
        ],
        [
            'tipo' => \App\Libraries\TipoNegocio::SERVICOS,
            'slug' => 'servicos',
            'titulo' => 'Serviços',
            'subtitulo' => 'Mão de obra, frete e adicionais de OS concretizadas',
            'icone' => 'fas fa-tools',
            'cor' => '#6d28d9',
            'cor_clara' => '#ede9fe',
            'faturamento' => $faturamento['servicos'],
            'quantidade' => $operacao['os_concretizadas'],
            'rotulo_quantidade' => 'OS concretizadas',
            'ticket' => $operacao['ticket_servicos'],
        ],
    ];
    $financeiroOperacionalReceber = $financeiro['Produtos']['total_receber'] + $financeiro['Servicos']['total_receber'];
    $financeiroOperacionalPagar = $financeiro['Produtos']['total_pagar'] + $financeiro['Servicos']['total_pagar'];
?>

<div class="content-wrapper dashboard-profissional">
    <div class="content-header">
        <div class="container-fluid">
            <div class="dashboard-hero">
                <div class="dashboard-hero-conteudo">
                    <span class="dashboard-eyebrow">Visão executiva</span>
                    <h1>Olá, <?= esc($session->get('primeiro_nome')) ?>.</h1>
                    <p>
                        Produtos e serviços analisados separadamente em
                        <strong><?= esc($meses[$periodo['mes']]) ?> de <?= esc($periodo['ano']) ?></strong>.
                    </p>
                    <div class="dashboard-periodo-contexto">
                        <?php if ($periodoAutomatico) : ?>
                            <i class="fas fa-calendar-check"></i>
                            <span>
                                <strong>Período automático.</strong>
                                A dashboard acompanha o mês atual e muda sozinha na virada do mês ou do ano.
                            </span>
                        <?php else : ?>
                            <i class="fas fa-history"></i>
                            <span>
                                <strong>Consulta manual.</strong>
                                Este período permanecerá selecionado até você voltar ao mês atual.
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <form class="dashboard-periodo" action="/inicio" method="get">
                    <div class="dashboard-periodo-campo">
                        <label for="dashboard-mes">Mês</label>
                        <select id="dashboard-mes" class="form-control" name="mes">
                            <?php foreach ($meses as $numero => $nome) : ?>
                                <option value="<?= $numero ?>" <?= $numero === $periodo['mes'] ? 'selected' : '' ?>>
                                    <?= esc($nome) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="dashboard-periodo-campo">
                        <label for="dashboard-ano">Ano</label>
                        <input
                            id="dashboard-ano"
                            class="form-control dashboard-ano"
                            type="number"
                            name="ano"
                            min="2000"
                            max="2100"
                            step="1"
                            value="<?= esc($periodo['ano']) ?>"
                        >
                    </div>
                    <div class="dashboard-periodo-acoes">
                        <button class="btn btn-light" type="submit"><i class="fas fa-search"></i> Consultar</button>
                        <?php if (! $periodoAutomatico) : ?>
                            <a class="btn btn-outline-light" href="/inicio" title="Retomar atualização automática">
                                <i class="fas fa-calendar-day"></i> Voltar ao mês atual
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-kpi dashboard-kpi-total">
                        <span class="dashboard-kpi-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="dashboard-kpi-label">Faturamento do período</span>
                        <strong><?= $moeda($faturamento['total']) ?></strong>
                        <small>Produtos + serviços concretizados</small>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-kpi dashboard-kpi-produtos">
                        <span class="dashboard-kpi-icon"><i class="fas fa-boxes"></i></span>
                        <span class="dashboard-kpi-label">Vendas de produtos</span>
                        <strong><?= $moeda($faturamento['produtos']) ?></strong>
                        <small><?= $operacao['vendas_produtos'] ?> vendas · Ticket <?= $moeda($operacao['ticket_produtos']) ?></small>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-kpi dashboard-kpi-servicos">
                        <span class="dashboard-kpi-icon"><i class="fas fa-tools"></i></span>
                        <span class="dashboard-kpi-label">Vendas de serviços</span>
                        <strong><?= $moeda($faturamento['servicos']) ?></strong>
                        <small><?= $operacao['os_concretizadas'] ?> OS · Ticket <?= $moeda($operacao['ticket_servicos']) ?></small>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="dashboard-kpi dashboard-kpi-financeiro">
                        <span class="dashboard-kpi-icon"><i class="fas fa-balance-scale"></i></span>
                        <span class="dashboard-kpi-label">Compromissos operacionais</span>
                        <strong><?= $moeda($financeiroOperacionalReceber - $financeiroOperacionalPagar) ?></strong>
                        <small>A receber <?= $moeda($financeiroOperacionalReceber) ?> · A pagar <?= $moeda($financeiroOperacionalPagar) ?></small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <div>
                                <span class="dashboard-card-kicker">Evolução anual</span>
                                <h3 class="card-title">Faturamento mensal por tipo de venda</h3>
                            </div>
                            <span class="dashboard-card-note"><?= esc($periodo['ano']) ?></span>
                        </div>
                        <div class="card-body dashboard-chart-lg">
                            <canvas id="dashboard-faturamento-anual"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <div>
                                <span class="dashboard-card-kicker">Composição do período</span>
                                <h3 class="card-title">Participação no faturamento</h3>
                            </div>
                        </div>
                        <div class="card-body dashboard-chart-lg dashboard-chart-center">
                            <canvas id="dashboard-composicao"></canvas>
                            <div class="dashboard-chart-summary">
                                <span><i class="dashboard-dot dashboard-dot-produtos"></i> Produtos <?= number_format($faturamento['percentual_produtos'], 1, ',', '.') ?>%</span>
                                <span><i class="dashboard-dot dashboard-dot-servicos"></i> Serviços <?= number_format($faturamento['percentual_servicos'], 1, ',', '.') ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-section-heading">
                <div>
                    <span class="dashboard-eyebrow">Leitura por segmento</span>
                    <h2>Produtos e serviços sem mistura</h2>
                </div>
                <p>Contas a receber e a pagar consideram todos os vencimentos ainda não liquidados.</p>
            </div>

            <div class="row">
                <?php foreach ($segmentos as $segmento) : ?>
                    <?php
                        $dadosFinanceiros = $financeiro[$segmento['tipo']];
                        $dadosMovimentacao = $movimentacao[$segmento['tipo']];
                    ?>
                    <div class="col-xl-6">
                        <div class="card dashboard-segment-card dashboard-segment-<?= esc($segmento['slug']) ?>">
                            <div class="card-header">
                                <div class="dashboard-segment-title">
                                    <span class="dashboard-segment-icon"><i class="<?= esc($segmento['icone']) ?>"></i></span>
                                    <div>
                                        <span class="dashboard-card-kicker">Área do negócio</span>
                                        <h3><?= esc($segmento['titulo']) ?></h3>
                                        <p><?= esc($segmento['subtitulo']) ?></p>
                                    </div>
                                </div>
                                <a class="btn btn-sm btn-outline-secondary" href="/relatorios/faturamentoDetalhado?tipo_negocio=<?= esc($segmento['tipo']) ?>&amp;data_inicio=<?= esc($periodo['inicio']) ?>&amp;data_final=<?= esc($periodo['final']) ?>">
                                    Ver relatório
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="dashboard-segment-metrics">
                                    <div>
                                        <span>Faturamento</span>
                                        <strong><?= $moeda($segmento['faturamento']) ?></strong>
                                    </div>
                                    <div>
                                        <span>Volume</span>
                                        <strong><?= $segmento['quantidade'] ?></strong>
                                        <small><?= esc($segmento['rotulo_quantidade']) ?></small>
                                    </div>
                                    <div>
                                        <span>Ticket médio</span>
                                        <strong><?= $moeda($segmento['ticket']) ?></strong>
                                    </div>
                                </div>

                                <div class="row dashboard-segment-finance">
                                    <div class="col-lg-7">
                                        <div class="dashboard-finance-grid">
                                            <a href="/contasReceber?tipo_negocio=<?= esc($segmento['tipo']) ?>">
                                                <span>A receber em aberto</span>
                                                <strong><?= $moeda($dadosFinanceiros['receber_aberta']) ?></strong>
                                            </a>
                                            <a class="is-overdue" href="/contasReceber?tipo_negocio=<?= esc($segmento['tipo']) ?>">
                                                <span>A receber vencido</span>
                                                <strong><?= $moeda($dadosFinanceiros['receber_vencida']) ?></strong>
                                            </a>
                                            <a href="/contasPagar?tipo_negocio=<?= esc($segmento['tipo']) ?>">
                                                <span>A pagar em aberto</span>
                                                <strong><?= $moeda($dadosFinanceiros['pagar_aberta']) ?></strong>
                                            </a>
                                            <a class="is-overdue" href="/contasPagar?tipo_negocio=<?= esc($segmento['tipo']) ?>">
                                                <span>A pagar vencido</span>
                                                <strong><?= $moeda($dadosFinanceiros['pagar_vencida']) ?></strong>
                                            </a>
                                        </div>
                                        <div class="dashboard-segment-movement">
                                            <span>Outros lançamentos no período <strong><?= $moeda($dadosMovimentacao['lancamentos']) ?></strong></span>
                                            <span>Despesas no período <strong><?= $moeda($dadosMovimentacao['despesas']) ?></strong></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 dashboard-chart-sm">
                                        <canvas id="dashboard-financeiro-<?= esc($segmento['slug']) ?>"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="card dashboard-card dashboard-general-card">
                        <div class="card-header">
                            <div>
                                <span class="dashboard-card-kicker">Administrativo</span>
                                <h3 class="card-title">Movimentação geral</h3>
                            </div>
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="card-body">
                            <p>
                                <strong>Geral</strong> reúne valores administrativos que não pertencem exclusivamente
                                a Produtos ou Serviços.
                            </p>
                            <div class="dashboard-general-grid">
                                <div><span>A receber</span><strong><?= $moeda($financeiro['Geral']['total_receber']) ?></strong></div>
                                <div><span>A pagar</span><strong><?= $moeda($financeiro['Geral']['total_pagar']) ?></strong></div>
                                <div><span>Outros lançamentos</span><strong><?= $moeda($movimentacao['Geral']['lancamentos']) ?></strong></div>
                                <div><span>Despesas</span><strong><?= $moeda($movimentacao['Geral']['despesas']) ?></strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="card dashboard-card dashboard-operation-card">
                        <div class="card-header">
                            <div>
                                <span class="dashboard-card-kicker">Operação atual</span>
                                <h3 class="card-title">Pontos de atenção</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <a href="/pedidos">
                                <i class="fas fa-shopping-bag"></i>
                                <span><strong><?= $operacao['pedidos_abertos'] ?></strong> pedidos em andamento</span>
                            </a>
                            <a href="/ordensDeServicos">
                                <i class="fas fa-clipboard-list"></i>
                                <span><strong><?= $operacao['os_abertas'] ?></strong> ordens de serviço abertas</span>
                            </a>
                            <a href="/produtos">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><strong><?= count($produtos_estoque_baixo) ?></strong> produtos no estoque mínimo</span>
                            </a>
                            <a href="/caixas">
                                <i class="fas fa-cash-register"></i>
                                <span><strong><?= count($caixas_abertos) ?></strong> caixas abertos</span>
                            </a>
                            <a href="/cobrancas">
                                <i class="fas fa-bell"></i>
                                <span><strong><?= count($cobrancasAlerta) ?></strong> cobrancas monitoradas</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <div>
                                <span class="dashboard-card-kicker">Agenda financeira</span>
                                <h3 class="card-title">Próximas contas e valores vencidos</h3>
                            </div>
                            <span class="dashboard-card-note">Produtos, Serviços e Geral identificados</span>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table dashboard-agenda-table">
                                <thead>
                                    <tr>
                                        <th>Vencimento</th>
                                        <th>Natureza</th>
                                        <th>Área</th>
                                        <th>Descrição</th>
                                        <th>Status</th>
                                        <th class="text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($agenda)) : ?>
                                        <?php foreach ($agenda as $conta) : ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($conta['data_de_vencimento'])) ?></td>
                                                <td>
                                                    <span class="dashboard-badge dashboard-badge-<?= esc($conta['natureza']) ?>">
                                                        <?= $conta['natureza'] === 'receber' ? 'A receber' : 'A pagar' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="dashboard-badge dashboard-badge-<?= strtolower(esc($conta['tipo_negocio'])) ?>">
                                                        <?= esc(\App\Libraries\TipoNegocio::rotulo($conta['tipo_negocio'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= esc($conta['nome']) ?></td>
                                                <td>
                                                    <span class="dashboard-status dashboard-status-<?= strtolower(esc($conta['status_dashboard'])) ?>">
                                                        <?= esc($conta['status_dashboard']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-right font-weight-bold"><?= $moeda($conta['valor']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td class="dashboard-empty" colspan="6">
                                                <i class="far fa-check-circle"></i>
                                                Nenhuma conta pendente ou vencida.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card dashboard-card dashboard-cobrancas-card">
                        <div class="card-header">
                            <div>
                                <span class="dashboard-card-kicker">Agenda independente</span>
                                <h3 class="card-title">Alertas de cobranca</h3>
                            </div>
                            <a href="/cobrancas" class="dashboard-card-note"><i class="fas fa-external-link-alt"></i> Gerenciar cobrancas</a>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table dashboard-agenda-table">
                                <thead>
                                    <tr>
                                        <th>Prazo</th>
                                        <th>Cliente</th>
                                        <th>Cobranca</th>
                                        <th>Parcela</th>
                                        <th>Status</th>
                                        <th>Contato</th>
                                        <th class="text-right">Valor do alerta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (! empty($cobrancasAlerta)) : ?>
                                        <?php foreach ($cobrancasAlerta as $alertaCobranca) : ?>
                                            <?php
                                                $whatsappLink = \App\Libraries\ContatoPadrao::whatsappLink($alertaCobranca['whatsapp'] ?: $alertaCobranca['celular']);
                                                $totalParcelas = (int) ($alertaCobranca['total_parcelas'] ?? $alertaCobranca['numero_parcela']);
                                                $rotuloParcela = $alertaCobranca['rotulo_parcela'] ?? ((int) $alertaCobranca['numero_parcela'] . '/' . $totalParcelas);
                                                $parcelasRestantes = (int) ($alertaCobranca['parcelas_restantes'] ?? 1);
                                            ?>
                                            <tr>
                                                <td><?= date('d/m/Y H:i', strtotime($alertaCobranca['vencimento'])) ?></td>
                                                <td><?= esc($alertaCobranca['cliente']) ?></td>
                                                <td><?= esc($alertaCobranca['titulo']) ?></td>
                                                <td>
                                                    <strong><?= esc($rotuloParcela) ?></strong>
                                                    <small class="d-block text-muted"><?= $parcelasRestantes ?> restantes</small>
                                                </td>
                                                <td><span class="dashboard-status dashboard-status-<?= strtolower(esc($alertaCobranca['status_alerta'])) ?>"><?= esc($alertaCobranca['status_alerta']) ?></span></td>
                                                <td>
                                                    <?php if ($whatsappLink !== '') : ?>
                                                        <a href="<?= esc($whatsappLink) ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                                                    <?php elseif (! empty($alertaCobranca['email'])) : ?>
                                                        <a href="mailto:<?= esc($alertaCobranca['email']) ?>"><i class="far fa-envelope"></i> E-mail</a>
                                                    <?php else : ?>
                                                        Nao informado
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right font-weight-bold">
                                                    <?= $moeda($alertaCobranca['valor_com_juros']) ?>
                                                    <?php if ((float) $alertaCobranca['valor_com_juros'] > (float) $alertaCobranca['valor']) : ?>
                                                        <small class="d-block text-danger">inclui juros estimados</small>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td class="dashboard-empty" colspan="7">
                                                <i class="far fa-bell-slash"></i>
                                                Nenhuma cobranca ativa para monitorar.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        /**
         * Atualiza os alertas para acompanhar o horario configurado.
         */
        function agendaAtualizacaoAlertasCobranca() {
            window.setTimeout(function() {
                window.location.reload();
            }, 60000);
        }

        agendaAtualizacaoAlertasCobranca();

        <?php if ($periodoAutomatico) : ?>
        /**
         * Agenda a recarga da dashboard quando o período automático virar.
         */
        function agendaViradaDoPeriodo() {
            var limiteTimeout = 2147483647;
            var intervalo = Math.min(esperaViradaPeriodo, limiteTimeout);

            if (esperaViradaPeriodo <= 0) {
                window.location.replace('/inicio');
                return;
            }

            window.setTimeout(function() {
                esperaViradaPeriodo -= intervalo;

                if (esperaViradaPeriodo > 0) {
                    agendaViradaDoPeriodo();
                    return;
                }

                window.location.replace('/inicio');
            }, intervalo);
        }

        var esperaViradaPeriodo = <?= (int) ($segundos_ate_proxima_virada ?? max(1, strtotime('first day of next month 00:00:05') - time())) ?> * 1000;
        agendaViradaDoPeriodo();
        <?php endif; ?>

        var moeda = new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        var labelsMeses = <?= json_encode($mesesCurtos, JSON_UNESCAPED_UNICODE) ?>;
        var faturamentoMensal = <?= json_encode($dashboard['mensal'], JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE) ?>;
        var chartColors = {
            produtos: '#0f766e',
            servicos: '#6d28d9',
            receberAberta: '#14b8a6',
            receberVencida: '#f59e0b',
            pagarAberta: '#64748b',
            pagarVencida: '#dc2626'
        };
        var graficosDashboard = [];

        /**
         * Informa se a dashboard esta usando a paleta escura.
         */
        function modoEscuroDashboardAtivo() {
            return window.TemaCor ? window.TemaCor.escuroAtivo() : false;
        }

        /**
         * Registra o grafico para que suas cores acompanhem a alternancia visual.
         */
        function registraGraficoDashboard(grafico) {
            graficosDashboard.push(grafico);

            return grafico;
        }

        /**
         * Atualiza textos, eixos e grades dos graficos para manter contraste adequado.
         */
        function aplicaTemaGraficosDashboard() {
            var escuro = modoEscuroDashboardAtivo();
            var texto = escuro ? '#dbe5f1' : '#475569';
            var grade = escuro ? 'rgba(148, 163, 184, .22)' : 'rgba(148, 163, 184, .16)';

            Chart.defaults.global.defaultFontColor = texto;

            graficosDashboard.forEach(function(grafico) {
                if (grafico.options.legend && grafico.options.legend.labels) {
                    grafico.options.legend.labels.fontColor = texto;
                }

                if (grafico.options.scales) {
                    (grafico.options.scales.xAxes || []).forEach(function(eixo) {
                        eixo.ticks.fontColor = texto;
                        eixo.gridLines.color = grade;
                        eixo.gridLines.zeroLineColor = grade;
                    });
                    (grafico.options.scales.yAxes || []).forEach(function(eixo) {
                        eixo.ticks.fontColor = texto;
                        eixo.gridLines.color = grade;
                        eixo.gridLines.zeroLineColor = grade;
                    });
                }

                grafico.update();
            });
        }

        /**
         * Formata valores monetarios exibidos nas dicas dos graficos.
         */
        function tooltipMoeda(tooltipItem, data) {
            var dataset = data.datasets[tooltipItem.datasetIndex];
            var valor = dataset.data[tooltipItem.index];

            return dataset.label + ': ' + moeda.format(valor || 0);
        }

        /**
         * Monta um grafico de composicao financeira para o conjunto informado.
         */
        function doughnutFinanceiro(id, dados) {
            registraGraficoDashboard(new Chart(document.getElementById(id), {
                type: 'doughnut',
                data: {
                    labels: ['Receber aberto', 'Receber vencido', 'Pagar aberto', 'Pagar vencido'],
                    datasets: [{
                        label: 'Financeiro',
                        data: dados,
                        backgroundColor: [
                            chartColors.receberAberta,
                            chartColors.receberVencida,
                            chartColors.pagarAberta,
                            chartColors.pagarVencida
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutoutPercentage: 66,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return data.labels[tooltipItem.index] + ': ' + moeda.format(data.datasets[0].data[tooltipItem.index] || 0);
                            }
                        }
                    }
                }
            }));
        }

        registraGraficoDashboard(new Chart(document.getElementById('dashboard-faturamento-anual'), {
            type: 'bar',
            data: {
                labels: labelsMeses,
                datasets: [{
                    label: 'Produtos',
                    data: faturamentoMensal.map(function(item) { return item.produtos; }),
                    backgroundColor: chartColors.produtos,
                    borderWidth: 0
                }, {
                    label: 'Serviços',
                    data: faturamentoMensal.map(function(item) { return item.servicos; }),
                    backgroundColor: chartColors.servicos,
                    borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(valor) { return moeda.format(valor); }
                        },
                        gridLines: {
                            color: 'rgba(148, 163, 184, .16)'
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: tooltipMoeda
                    }
                }
            }
        }));

        registraGraficoDashboard(new Chart(document.getElementById('dashboard-composicao'), {
            type: 'doughnut',
            data: {
                labels: ['Produtos', 'Serviços'],
                datasets: [{
                    label: 'Faturamento',
                    data: <?= json_encode([$faturamento['produtos'], $faturamento['servicos']], JSON_NUMERIC_CHECK) ?>,
                    backgroundColor: [chartColors.produtos, chartColors.servicos],
                    borderWidth: 0
                }]
            },
            options: {
                cutoutPercentage: 72,
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return data.labels[tooltipItem.index] + ': ' + moeda.format(data.datasets[0].data[tooltipItem.index] || 0);
                        }
                    }
                }
            }
        }));

        doughnutFinanceiro('dashboard-financeiro-produtos', <?= json_encode([
            $financeiro['Produtos']['receber_aberta'],
            $financeiro['Produtos']['receber_vencida'],
            $financeiro['Produtos']['pagar_aberta'],
            $financeiro['Produtos']['pagar_vencida'],
        ], JSON_NUMERIC_CHECK) ?>);

        doughnutFinanceiro('dashboard-financeiro-servicos', <?= json_encode([
            $financeiro['Servicos']['receber_aberta'],
            $financeiro['Servicos']['receber_vencida'],
            $financeiro['Servicos']['pagar_aberta'],
            $financeiro['Servicos']['pagar_vencida'],
        ], JSON_NUMERIC_CHECK) ?>);

        document.addEventListener('sistema:tema-cor-alterado', aplicaTemaGraficosDashboard);
        aplicaTemaGraficosDashboard();

        <?php $alert = $session->getFlashdata('alert'); ?>
        <?php if ($alert === 'success_autentication') : ?>
            Swal.fire({
                type: 'success',
                title: 'Bem-vindo, <?= esc($session->get('primeiro_nome')) ?>!',
                timer: 2200,
                showConfirmButton: false
            });
        <?php elseif ($alert === 'success_bkp_database') : ?>
            Swal.fire({
                type: 'success',
                title: 'Backup realizado com sucesso!',
                timer: 2200,
                showConfirmButton: false
            });
        <?php endif; ?>
    });
</script>
