<?php
    $agora = time();
    $pendentesHoje = 0;
    $atrasadas = 0;
    $resumosParcelas = $resumos_parcelas ?? [];

    foreach ($pendencias as $pendencia) {
        $vencimento = strtotime($pendencia['vencimento']);
        $pendentesHoje += date('Y-m-d', $vencimento) === date('Y-m-d') ? 1 : 0;
        $atrasadas += $vencimento < $agora ? 1 : 0;
    }
?>

<div class="content-wrapper cobrancas-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h6 class="m-0 text-dark"><i class="<?= esc($titulo['icone']) ?>"></i> <?= esc($titulo['modulo']) ?></h6>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/inicio">Inicio</a></li>
                        <li class="breadcrumb-item active">Cobrancas</li>
                    </ol>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-shield-alt"></i>
                Modulo independente de alertas: concluir, editar, estender ou excluir uma cobranca nao altera o financeiro.
            </div>

            <div class="row">
                <div class="col-md-4"><div class="small-box bg-primary"><div class="inner"><h3><?= count($cobrancas) ?></h3><p>Cobrancas cadastradas</p></div><div class="icon"><i class="fas fa-bell"></i></div></div></div>
                <div class="col-md-4"><div class="small-box bg-warning"><div class="inner"><h3><?= $pendentesHoje ?></h3><p>Cobrancas para hoje</p></div><div class="icon"><i class="fas fa-calendar-day"></i></div></div></div>
                <div class="col-md-4"><div class="small-box bg-danger"><div class="inner"><h3><?= $atrasadas ?></h3><p>Alertas atrasados</p></div><div class="icon"><i class="fas fa-exclamation-circle"></i></div></div></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <a href="/cobrancas/create" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Nova cobranca</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-address-book"></i> Cobrancas recorrentes cadastradas</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped tabela-listagem">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Cobranca</th>
                                <th>Total</th>
                                <th>Parcelas</th>
                                <th>Periodicidade</th>
                                <th>Proximo alerta</th>
                                <th>Status</th>
                                <th style="width: 145px">Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cobrancas as $cobranca) : ?>
                                <?php
                                    $cliente = trim((string) ($cobranca['nome'] ?: $cobranca['razao_social'])) ?: 'Cliente nao informado';
                                    $proxima = $proximas[(int) $cobranca['id_cobranca']] ?? null;
                                    $totalParcelas = max(1, (int) $cobranca['quantidade_parcelas']);
                                    $resumoParcela = $resumosParcelas[(int) $cobranca['id_cobranca']] ?? [
                                        'total' => $totalParcelas,
                                        'pendentes' => $totalParcelas,
                                        'realizadas' => 0,
                                    ];
                                    $totalParcelas = max($totalParcelas, (int) $resumoParcela['total']);
                                    $parcelasPendentes = min($totalParcelas, (int) $resumoParcela['pendentes']);
                                    $parcelasRealizadas = min($totalParcelas, (int) $resumoParcela['realizadas']);
                                ?>
                                <tr>
                                    <td><?= esc($cliente) ?></td>
                                    <td><strong><?= esc($cobranca['titulo']) ?></strong><br><small><?= esc($cobranca['descricao']) ?></small></td>
                                    <td>R$ <?= number_format((float) $cobranca['valor_total'], 2, ',', '.') ?></td>
                                    <td>
                                        <strong><?= $parcelasPendentes ?></strong> faltam
                                        <small class="d-block text-muted"><?= $parcelasRealizadas ?>/<?= $totalParcelas ?> realizadas</small>
                                    </td>
                                    <td><?= esc($cobranca['recorrencia']) ?></td>
                                    <td><?= $proxima ? date('d/m/Y H:i', strtotime($proxima['vencimento'])) : 'Sem pendencias' ?></td>
                                    <td><span class="badge badge-<?= $cobranca['status'] === 'Ativa' ? 'success' : ($cobranca['status'] === 'Pausada' ? 'warning' : 'secondary') ?>"><?= esc($cobranca['status']) ?></span></td>
                                    <td>
                                        <a class="btn btn-warning style-action" href="/cobrancas/edit/<?= $cobranca['id_cobranca'] ?>" title="Editar"><i class="fas fa-edit"></i></a>
                                        <?php if ($cobranca['recorrencia'] !== 'Unica') : ?>
                                            <button class="btn btn-info style-action" type="button" title="Estender com uma nova parcela" onclick="confirmaAcaoExcluir('Adicionar outra parcela mantendo o valor atual e ampliando o total da cobranca?', '/cobrancas/estender/<?= $cobranca['id_cobranca'] ?>')"><i class="fas fa-calendar-plus"></i></button>
                                        <?php endif; ?>
                                        <button class="btn btn-danger style-action" type="button" title="Excluir" onclick="confirmaAcaoExcluir('Excluir esta cobranca e todos os seus alertas?', '/cobrancas/delete/<?= $cobranca['id_cobranca'] ?>')"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-clock"></i> Agenda de cobrancas pendentes</h3></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped tabela-listagem">
                        <thead>
                            <tr>
                                <th>Vencimento</th>
                                <th>Cliente</th>
                                <th>Cobranca</th>
                                <th>Parcela</th>
                                <th>Valor</th>
                                <th>Contato</th>
                                <th style="width: 105px">Acao</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendencias as $pendencia) : ?>
                                <?php
                                    $cliente = trim((string) ($pendencia['nome'] ?: $pendencia['razao_social'])) ?: 'Cliente nao informado';
                                    $whatsappLink = \App\Libraries\ContatoPadrao::whatsappLink($pendencia['whatsapp'] ?: $pendencia['celular']);
                                    $atrasada = strtotime($pendencia['vencimento']) < $agora;
                                    $totalParcelas = max(1, (int) ($pendencia['quantidade_parcelas'] ?? $pendencia['numero_parcela']));
                                    $resumoParcela = $resumosParcelas[(int) $pendencia['id_cobranca']] ?? [
                                        'total' => $totalParcelas,
                                        'pendentes' => $totalParcelas,
                                        'realizadas' => 0,
                                    ];
                                    $totalParcelas = max($totalParcelas, (int) $resumoParcela['total']);
                                    $parcelasPendentes = min($totalParcelas, (int) $resumoParcela['pendentes']);
                                ?>
                                <tr class="<?= $atrasada ? 'table-danger' : '' ?>">
                                    <td><?= date('d/m/Y H:i', strtotime($pendencia['vencimento'])) ?></td>
                                    <td><?= esc($cliente) ?></td>
                                    <td><?= esc($pendencia['titulo']) ?></td>
                                    <td>
                                        <strong><?= (int) $pendencia['numero_parcela'] ?>/<?= $totalParcelas ?></strong>
                                        <small class="d-block text-muted"><?= $parcelasPendentes ?> restantes</small>
                                    </td>
                                    <td>R$ <?= number_format((float) $pendencia['valor'], 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($whatsappLink !== '') : ?>
                                            <a href="<?= esc($whatsappLink) ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                                        <?php else : ?>
                                            Nao informado
                                        <?php endif; ?>
                                    </td>
                                    <td><a href="/cobrancas/concluir/<?= $pendencia['id_ocorrencia'] ?>" class="btn btn-success btn-sm" title="Marcar lembrete como realizado"><i class="fas fa-check"></i> Realizada</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var alert = <?= json_encode(session()->getFlashdata('alert')) ?>;
        var mensagens = {
            success_create: 'Cobranca cadastrada com sucesso.',
            success_edit: 'Cobranca atualizada com sucesso.',
            success_delete: 'Cobranca excluida com sucesso.',
            success_complete: 'Lembrete marcado como realizado.',
            success_extend: 'Cobranca estendida com uma nova parcela.',
            error_extend: 'Cobrancas unicas nao podem ser estendidas.'
        };

        if (mensagens[alert]) {
            Swal.fire({
                type: alert === 'error_extend' ? 'error' : 'success',
                title: mensagens[alert],
                timer: 2500,
                showConfirmButton: false
            });
        }
    });
</script>
