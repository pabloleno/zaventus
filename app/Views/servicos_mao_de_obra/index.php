<?php
use App\Libraries\ImagemCadastro;

$tipos = ['fixo' => 'Preço fixo', 'unidade' => 'Por unidade', 'metro_linear' => 'Por metro linear', 'metro_quadrado' => 'Por m²', 'quantidade' => 'Por quantidade / lote'];
$execucoes = ['interna' => 'Interna', 'externa' => 'Externa', 'mista' => 'Interna + externa'];
$artes = ['nao_necessita' => 'Não necessita arte', 'cliente' => 'Arte do cliente', 'grafica' => 'Gráfica produz a arte'];
$mensagens = ['success_create' => 'Serviço cadastrado com sucesso.', 'success_edit' => 'Serviço atualizado com sucesso.', 'success_inactivate' => 'Serviço inativado. Para reativá-lo, edite sua situação.'];
$alerta = session()->getFlashdata('alert');
?>
<div class="content-wrapper"><div class="content"><div class="container-fluid">
    <div class="row mb-3"><div class="col-sm-6"><h6 class="m-0"><i class="fa fa-print mr-1"></i> Catálogo de serviços</h6></div><div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="/inicio">Início</a></li><li class="breadcrumb-item active">Catálogo de serviços</li></ol></div></div>
    <?php if (is_string($alerta) && isset($mensagens[$alerta])): ?><div class="alert alert-success" role="status"><?= esc($mensagens[$alerta]) ?></div><?php endif; ?>
    <?php foreach ((array) (session()->getFlashdata('erros_catalogo') ?? []) as $erro): ?><div class="alert alert-danger" role="alert"><?= esc((string) $erro) ?></div><?php endforeach; ?>
    <div class="card"><div class="card-header"><a href="/servicosMaoDeObra/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Novo serviço</a></div><div class="card-body table-responsive">
        <table id="example1" class="table table-bordered table-striped">
            <thead><tr><th>Cód.</th><th>Serviço</th><th>Preço</th><th>Métrica</th><th>Execução</th><th>Situação</th><th>Ações</th></tr></thead>
            <tbody>
            <?php foreach ($servicos ?? [] as $servico): ?>
                <?php $id = (int) $servico['id_servico']; $ativo = (int) ($servico['ativo'] ?? 1) === 1; ?>
                <tr>
                    <td><?= $id ?></td>
                    <td>
                        <?php if (! empty($servico['imagem'])): ?><img src="<?= esc(ImagemCadastro::url($servico['imagem'])) ?>" alt="" class="img-thumbnail float-left mr-2" style="width:56px;height:56px;object-fit:cover"><?php endif; ?>
                        <strong><?= esc($servico['nome']) ?></strong>
                        <?php if (! empty($servico['descricao'])): ?><small class="d-block"><?= esc($servico['descricao']) ?></small><?php endif; ?>
                        <?php if (! empty($servico['observacoes'])): ?><small class="d-block text-muted"><?= esc($servico['observacoes']) ?></small><?php endif; ?>
                    </td>
                    <td class="text-nowrap">R$ <?= esc(moeda($servico['valor'])) ?></td>
                    <td><?= esc($tipos[$servico['tipo_preco'] ?? 'unidade'] ?? 'Por unidade') ?><small class="d-block text-muted"><?= esc($servico['unidade'] ?? 'un') ?></small>
                        <?php if (($servico['largura_padrao'] ?? null) !== null || ($servico['altura_padrao'] ?? null) !== null): ?><small class="d-block"><?= esc((string) ($servico['largura_padrao'] ?? '—')) ?> × <?= esc((string) ($servico['altura_padrao'] ?? '—')) ?> <?= esc($servico['unidade_dimensao'] ?? 'm') ?></small><?php endif; ?>
                    </td>
                    <td><?= esc($execucoes[$servico['tipo_execucao'] ?? 'interna'] ?? 'Interna') ?><small class="d-block text-muted"><?= esc($artes[$servico['arte_padrao'] ?? 'nao_necessita'] ?? '') ?></small><?php if ((int) ($servico['necessita_instalacao'] ?? 0) === 1): ?><small class="d-block">Com instalação</small><?php endif; ?></td>
                    <td><span class="badge badge-<?= $ativo ? 'success' : 'secondary' ?>"><?= $ativo ? 'Ativo' : 'Inativo' ?></span></td>
                    <td class="text-nowrap">
                        <a href="/servicosMaoDeObra/edit/<?= $id ?>" class="btn btn-warning btn-sm" title="Editar serviço" aria-label="Editar serviço"><i class="fa fa-edit"></i></a>
                        <?php if ($ativo): ?><form action="/servicosMaoDeObra/delete/<?= $id ?>" method="post" class="d-inline" onsubmit="return confirm('Inativar este serviço? Os atendimentos existentes serão preservados.');"><?= csrf_field() ?><button type="submit" class="btn btn-outline-secondary btn-sm" title="Inativar serviço" aria-label="Inativar serviço"><i class="fa fa-ban"></i></button></form><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div></div>
</div></div></div>
