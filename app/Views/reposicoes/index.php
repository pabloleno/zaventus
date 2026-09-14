<?php $alerta = session()->getFlashdata('alert'); ?>
<div class="content-wrapper"><div class="content"><div class="container-fluid">
    <div class="mb-3"><h6 class="m-0">Reposições</h6></div>
    <?php if (in_array($alerta, ['success_create', 'success_estorno', 'success_reposicao_por_xml'], true)): ?><div class="alert alert-success" role="status"><?= $alerta === 'success_estorno' ? 'Movimentação estornada; histórico preservado.' : 'Movimentação registrada com sucesso.' ?></div><?php endif; ?>
    <?php foreach ((array) (session()->getFlashdata('erros_estoque') ?? []) as $erro): ?><div class="alert alert-danger" role="alert"><?= esc((string) $erro) ?></div><?php endforeach; ?>
    <div class="card"><div class="card-header"><a href="/reposicoes/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Nova reposição</a></div><div class="card-body table-responsive">
        <table id="example1" class="table table-bordered table-striped"><thead><tr><th>Cód.</th><th>Matéria-prima</th><th>Quantidade</th><th>Data / hora</th><th>Observações</th><th>Situação</th><th>Ações</th></tr></thead><tbody>
        <?php foreach ($reposicoes ?? [] as $movimento): ?>
            <?php $id = (int) $movimento['id_reposicao']; $estornado = !empty($movimento['estornado_at']) || (!empty($movimento['deleted_at']) && $movimento['deleted_at'] !== '0000-00-00 00:00:00'); ?>
            <tr><td><?= $id ?></td><td><?= esc($movimento['nome']) ?></td><td><?= esc($movimento['qtd_da_reposicao']) ?> <?= esc($movimento['unidade']) ?></td><td><?= esc($movimento['data']) ?><small class="d-block"><?= esc($movimento['hora']) ?></small></td><td><?= esc($movimento['observacoes']) ?><?php if ($estornado): ?><small class="d-block text-muted"><?= esc($movimento['estorno_motivo'] ?? '') ?></small><?php endif; ?></td><td><span class="badge badge-<?= $estornado ? 'secondary' : 'success' ?>"><?= $estornado ? 'Estornada' : 'Registrada' ?></span></td><td>
                <?php if (!empty($movimento['id_ordem'])): ?><a href="/ordensDeServicos/show/<?= (int) $movimento['id_ordem'] ?>" class="btn btn-info btn-sm">Ver atendimento</a>
                <?php elseif (!$estornado): ?><details><summary class="text-primary">Estornar</summary><form action="/reposicoes/delete/<?= $id ?>" method="post" class="mt-2"><?= csrf_field() ?><input class="form-control form-control-sm mb-2" name="motivo" maxlength="512" placeholder="Motivo do estorno" aria-label="Motivo do estorno" required><button type="submit" class="btn btn-outline-danger btn-sm">Confirmar estorno</button></form></details><?php endif; ?>
            </td></tr>
        <?php endforeach; ?>
        </tbody></table>
    </div></div>
</div></div></div>
