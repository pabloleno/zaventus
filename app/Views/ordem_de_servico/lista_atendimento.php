<?php
$moeda = static fn ($valor) => 'R$ ' . number_format((float) $valor, 2, ',', '.');
$dataBr = static fn ($data) => empty($data) || str_starts_with($data, '0000') ? '—' : date('d/m/Y', strtotime($data));
$trash = $grupo === 'excluidos';
$url = $trash ? '/ordensDeServicos/excluidos' : ($grupo === 'orcamentos' ? '/ordensDeServicos/orcamentos' : '/ordensDeServicos');
?>
<div class="content-wrapper"><section class="content pt-3"><div class="container-fluid">
<div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
    <h1 class="h4 mb-2"><?= esc($titulo_pagina) ?></h1>
    <div class="mb-2"><a class="btn btn-outline-secondary" href="<?= $trash ? '/ordensDeServicos/orcamentos' : '/ordensDeServicos/excluidos' ?>"><?= $trash ? 'Orçamentos' : 'Excluídos' ?></a> <a class="btn btn-primary" href="/ordensDeServicos/create"><i class="fas fa-plus" aria-hidden="true"></i> Novo orçamento</a></div>
</div>
<?php foreach (['atendimento_sucesso' => 'success', 'atendimento_erro' => 'danger'] as $key => $color): if ($message = session()->getFlashdata($key)): ?><div class="alert alert-<?= $color ?>" role="alert"><?= esc($message) ?></div><?php endif; endforeach ?>
<?php if ($trash): ?><p class="text-muted">Restaure um orçamento em até 30 dias. A exclusão definitiva é manual e preserva registros com vínculos financeiros ou de estoque.</p><?php endif ?>
<div class="card"><div class="card-body">
<form method="get" action="<?= $url ?>" class="row align-items-end">
    <div class="form-group col-md-4"><label for="busca-atendimento">Buscar cliente ou número</label><input class="form-control" id="busca-atendimento" name="term" value="<?= esc($filtros['term'] ?? '', 'attr') ?>"></div>
    <div class="form-group col-md-3"><label for="status-atendimento">Situação</label><select class="form-control" id="status-atendimento" name="status"><option value="">Todas</option><?php foreach ($statuses as $key => $label): ?><option value="<?= esc($key, 'attr') ?>" <?= ($filtros['status'] ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach ?></select></div>
    <div class="form-group col-md-2"><label for="inicio-atendimento">De</label><input type="date" class="form-control" id="inicio-atendimento" name="data_inicio" value="<?= esc($filtros['data_inicio'] ?? '', 'attr') ?>"></div>
    <div class="form-group col-md-2"><label for="fim-atendimento">Até</label><input type="date" class="form-control" id="fim-atendimento" name="data_final" value="<?= esc($filtros['data_final'] ?? '', 'attr') ?>"></div>
    <div class="form-group col-md-1"><button class="btn btn-primary" type="submit">Filtrar</button></div>
    <?php if (! $trash): ?><div class="col-12 mb-2"><label class="font-weight-normal"><input type="checkbox" name="todos" value="1" <?= ! empty($filtros['todos']) ? 'checked' : '' ?>> Incluir todas as etapas do atendimento</label></div><?php endif ?>
</form>
</div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Orçamento</th><th>Cliente</th><th>Situação</th><th>Previsão</th><th class="text-right">Total</th><th class="text-right">Recebido</th><th class="text-right">Saldo</th><th><?= $trash ? 'Exclusão' : 'Ações' ?></th></tr></thead><tbody>
<?php if (! $ordens): ?><tr><td colspan="8" class="text-center text-muted p-4">Nenhum atendimento encontrado.</td></tr><?php endif ?>
<?php foreach ($ordens as $o): $id = (int) $o['id_ordem']; $numero = $o['numero'] ?: 'Atendimento #' . $id; $status = \App\Libraries\AtendimentoGrafica::status($o); ?>
<tr><td><strong><?= esc($numero) ?></strong><br><small class="text-muted"><?= $dataBr($o['data_de_entrada']) ?></small></td><td><?= esc($o['cliente_nome']) ?></td><td><span class="badge badge-<?= $status === 'concluido' ? 'success' : ($status === 'cancelado' ? 'secondary' : 'info') ?>"><?= esc($statuses[$status] ?? $status) ?></span></td><td><?= $dataBr($o['previsao_conclusao']) ?></td><td class="text-right text-nowrap"><?= $moeda($o['total']) ?></td><td class="text-right text-nowrap"><?= $moeda($o['total_pago']) ?></td><td class="text-right text-nowrap"><?= $moeda($o['saldo']) ?></td><td>
<?php if (! $trash): ?><a class="btn btn-sm btn-outline-primary" href="/ordensDeServicos/show/<?= $id ?>">Abrir</a> <a class="btn btn-sm btn-outline-secondary" href="/ordensDeServicos/edit/<?= $id ?>">Editar</a>
<?php else: $dias = max(0, (int) ceil((strtotime($o['purge_at']) - time()) / 86400)); ?>
<small class="d-block text-muted">Excluído em <?= $dataBr($o['deleted_at']) ?> · <?= $dias ?> dia(s) para restaurar</small>
<form class="d-inline" method="post" action="/ordensDeServicos/restaurarAtendimento"><?= csrf_field() ?><input type="hidden" name="id_ordem" value="<?= $id ?>"><button class="btn btn-sm btn-outline-primary" type="submit">Restaurar</button></form>
<details class="mt-1"><summary class="small text-danger">Excluir definitivamente</summary><form class="mt-2" method="post" action="/ordensDeServicos/apagarAtendimento"><?= csrf_field() ?><input type="hidden" name="id_ordem" value="<?= $id ?>"><label class="small" for="confirmar-<?= $id ?>">Para confirmar, digite <?= esc($numero) ?></label><input class="form-control form-control-sm mb-1" id="confirmar-<?= $id ?>" name="confirmacao" required autocomplete="off"><button class="btn btn-sm btn-danger" type="submit">Excluir definitivamente</button></form></details>
<?php endif ?></td></tr>
<?php endforeach ?>
</tbody></table></div>
<div class="card-footer text-muted small"><?= count($ordens) ?> atendimento(s). A consulta mostra até 200 registros; use os filtros para refinar.</div></div>
</div></section></div>
