<?php
    $filtros = $filtros ?? [];
    $resumo = $resumo ?? ['total' => 0, 'emitidas' => 0, 'canceladas' => 0, 'pendentes' => 0, 'problemas' => 0];
    $valorFiltro = static function (string $campo) use ($filtros): string {
        return esc($filtros[$campo] ?? '');
    };
?>

<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h6 class="m-0 text-dark"><i class="<?= esc($titulo['icone']) ?>"></i> <?= esc($titulo['modulo']) ?></h6>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <?php foreach ($caminhos as $caminho) : ?>
                            <?php if (! $caminho['active']) : ?>
                                <li class="breadcrumb-item"><a href="<?= esc($caminho['rota']) ?>"><?= esc($caminho['titulo']) ?></a></li>
                            <?php else : ?>
                                <li class="breadcrumb-item active"><?= esc($caminho['titulo']) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"><div class="small-box bg-primary"><div class="inner"><h3><?= (int) $resumo['total'] ?></h3><p>Documentos fiscais</p></div><div class="icon"><i class="fas fa-file-invoice"></i></div></div></div>
                <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3><?= (int) $resumo['emitidas'] ?></h3><p>Autorizados</p></div><div class="icon"><i class="fas fa-check-circle"></i></div></div></div>
                <div class="col-md-3"><div class="small-box bg-secondary"><div class="inner"><h3><?= (int) $resumo['canceladas'] ?></h3><p>Cancelados</p></div><div class="icon"><i class="fas fa-ban"></i></div></div></div>
                <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3><?= (int) ($resumo['pendentes'] + $resumo['problemas']) ?></h3><p>Requerem atencao</p></div><div class="icon"><i class="fas fa-exclamation-triangle"></i></div></div></div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="/controleFiscal" method="get">
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <select class="form-control" name="modelo">
                                        <option value="" <?= ($filtros['modelo'] ?? '') === '' ? 'selected' : '' ?>>Todos</option>
                                        <option value="55" <?= ($filtros['modelo'] ?? '') === '55' ? 'selected' : '' ?>>NFe</option>
                                        <option value="65" <?= ($filtros['modelo'] ?? '') === '65' ? 'selected' : '' ?>>NFCe</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">Todos</option>
                                        <?php foreach (['Emitida', 'Cancelada', 'Pendente', 'Nao Emitida', 'Nao localizada', 'Rejeitada', 'Denegada'] as $statusOpcao) : ?>
                                            <option value="<?= esc($statusOpcao) ?>" <?= ($filtros['status'] ?? '') === $statusOpcao ? 'selected' : '' ?>><?= esc($statusOpcao) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Chave</label>
                                    <input type="text" class="form-control" name="chave" value="<?= $valorFiltro('chave') ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Data inicio</label>
                                    <input type="date" class="form-control" name="data_inicio" value="<?= $valorFiltro('data_inicio') ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Data final</label>
                                    <input type="date" class="form-control" name="data_final" value="<?= $valorFiltro('data_final') ?>">
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <button type="submit" class="btn btn-success btn-block" style="margin-top: 30px"><i class="fas fa-filter"></i></button>
                            </div>
                        </div>
                    </form>

                    <div class="btn-group">
                        <a href="/controleFiscal/statusServico/55" class="btn btn-outline-primary"><i class="fas fa-satellite-dish"></i> Status NFe</a>
                        <a href="/controleFiscal/statusServico/65" class="btn btn-outline-primary"><i class="fas fa-satellite-dish"></i> Status NFCe</a>
                        <?php if (! empty($filtros['data_inicio']) && ! empty($filtros['data_final'])) : ?>
                            <a href="/controleFiscal/baixaXMLS/<?= esc($filtros['data_inicio']) ?>/<?= esc($filtros['data_final']) ?>" class="btn btn-outline-info"><i class="fas fa-file-archive"></i> XMLs NFe</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Documentos</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped tabela-listagem">
                        <thead>
                            <tr>
                                <th>Modelo</th>
                                <th>Status</th>
                                <th>Emissao</th>
                                <th>Serie/Numero</th>
                                <th>Chave</th>
                                <th>Protocolo</th>
                                <th>Retorno SEFAZ</th>
                                <th style="width: 230px">Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($documentos)) : ?>
                                <?php foreach ($documentos as $documento) : ?>
                                    <?php
                                        $xmlCancelamento = trim((string) ($documento['xml_cancelamento'] ?? $documento['xml_protocolado_cancelamento'] ?? ''));
                                        $status = (string) ($documento['status'] ?? 'Pendente');
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($documento['modelo_nome']) ?></strong>
                                            <small class="d-block text-muted"><?= esc($documento['ambiente_rotulo']) ?></small>
                                        </td>
                                        <td><span class="badge badge-<?= esc($documento['status_classe']) ?>"><?= esc($status) ?></span></td>
                                        <td>
                                            <?= ! empty($documento['data']) ? date('d/m/Y', strtotime($documento['data'])) : '-' ?>
                                            <small class="d-block text-muted"><?= esc($documento['hora'] ?? '') ?></small>
                                        </td>
                                        <td><?= esc(trim((string) ($documento['serie'] ?? '')) ?: '-') ?> / <?= esc(trim((string) ($documento['numero'] ?? '')) ?: '-') ?></td>
                                        <td><small><?= esc($documento['chave'] ?? '') ?></small></td>
                                        <td><?= esc($documento['nprot'] ?? '') ?: '<span class="text-muted">Sem protocolo</span>' ?></td>
                                        <td>
                                            <?= esc($documento['xmotivo'] ?? '') ?: '<span class="text-muted">Nao consultado</span>' ?>
                                            <?php if (! empty($documento['ultima_consulta_em'])) : ?>
                                                <small class="d-block text-muted">Ultima consulta: <?= date('d/m/Y H:i', strtotime($documento['ultima_consulta_em'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form action="/controleFiscal/consultar/<?= esc($documento['modelo']) ?>/<?= (int) $documento['id_documento'] ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-info style-action" title="Consultar na SEFAZ"><i class="fas fa-sync-alt"></i></button>
                                            </form>

                                            <?php if (! empty($documento['xml'])) : ?>
                                                <a href="/controleFiscal/baixaXML/<?= esc($documento['modelo']) ?>/<?= (int) $documento['id_documento'] ?>" class="btn btn-primary style-action" title="Baixar XML"><i class="fas fa-file-code"></i></a>
                                            <?php endif; ?>

                                            <?php if ($status === 'Emitida') : ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-warning style-action fiscal-btn-cancelar"
                                                    title="Cancelar"
                                                    data-toggle="modal"
                                                    data-target="#modal-cancelar-documento"
                                                    data-modelo="<?= esc($documento['modelo']) ?>"
                                                    data-id="<?= (int) $documento['id_documento'] ?>"
                                                    data-descricao="<?= esc($documento['modelo_nome'] . ' ' . ($documento['numero'] ?? '') . ' - ' . ($documento['chave'] ?? '')) ?>"
                                                ><i class="fas fa-ban"></i></button>
                                            <?php endif; ?>

                                            <?php if ($xmlCancelamento !== '') : ?>
                                                <a href="/controleFiscal/baixaXML/<?= esc($documento['modelo']) ?>/<?= (int) $documento['id_documento'] ?>/cancelamento" class="btn btn-secondary style-action" title="XML de cancelamento"><i class="fas fa-file-signature"></i></a>
                                            <?php endif; ?>

                                            <?php if (! empty($documento['erro'])) : ?>
                                                <a href="/controleFiscal/<?= $documento['modelo'] === '65' ? 'showErroNFCe' : 'showErroNFe' ?>/<?= (int) $documento['id_documento'] ?>" class="btn btn-danger style-action" title="Ver erro"><i class="fas fa-exclamation-circle"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Nenhum documento fiscal encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cancelar-documento">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/controleFiscal/cancelar" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h4 class="modal-title">Cancelar documento fiscal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="modelo" id="fiscal-cancelar-modelo">
                    <input type="hidden" name="id_documento" id="fiscal-cancelar-id">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        O cancelamento sera enviado para a SEFAZ e registrado no historico fiscal.
                    </div>
                    <div class="form-group">
                        <label>Documento</label>
                        <input type="text" id="fiscal-cancelar-descricao" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label>Justificativa</label>
                        <textarea class="form-control" name="justificativa" rows="5" minlength="15" maxlength="255" required></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-ban"></i> Enviar cancelamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.fiscal-btn-cancelar').on('click', function() {
            $('#fiscal-cancelar-modelo').val($(this).data('modelo'));
            $('#fiscal-cancelar-id').val($(this).data('id'));
            $('#fiscal-cancelar-descricao').val($(this).data('descricao'));
        });

        var alert = <?= json_encode(session()->getFlashdata('alert')) ?>;
        var message = <?= json_encode(session()->getFlashdata('fiscal_message')) ?>;

        if (alert && message) {
            Swal.fire({
                type: alert === 'success_fiscal' ? 'success' : (alert === 'warning_fiscal' ? 'warning' : 'error'),
                title: message,
                timer: alert === 'success_fiscal' ? 3500 : undefined,
                showConfirmButton: alert !== 'success_fiscal'
            });
        }
    });
</script>
