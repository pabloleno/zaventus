<?php
$registro = $produto ?? [];
$campo = static function (string $nome, $padrao = '') use ($registro): string { $valor = old($nome, $registro[$nome] ?? $padrao, false); return is_scalar($valor) ? (string) $valor : ''; };
?>
<div class="content-wrapper"><div class="content"><div class="container-fluid">
<form action="/inventarioDoEstoque/store_produto" method="post">
    <?= csrf_field() ?><input type="hidden" name="id_inventario" value="<?= (int) $id_inventario ?>">
    <?php if (isset($registro['id_produto_do_inventario'])): ?><input type="hidden" name="id_produto_do_inventario" value="<?= (int) $registro['id_produto_do_inventario'] ?>"><?php endif; ?>
    <div class="card"><div class="card-header"><a href="/inventarioDoEstoque/listaProdutos/<?= (int) $id_inventario ?>" class="btn btn-success btn-sm float-right">Voltar</a><h6 class="m-0 pt-1">Item do inventário</h6></div><div class="card-body">
        <?php foreach ((array) (session()->getFlashdata('erros_inventario') ?? []) as $erro): ?><div class="alert alert-danger" role="alert"><?= esc((string) $erro) ?></div><?php endforeach; ?>
        <div class="row">
            <div class="col-md-8 form-group"><label for="inv-discriminacao">Discriminação</label><input id="inv-discriminacao" class="form-control" name="discriminacao" maxlength="512" value="<?= esc($campo('discriminacao')) ?>" required></div>
            <div class="col-md-4 form-group"><label for="inv-unidade">Unidade</label><input id="inv-unidade" class="form-control" name="unidade" maxlength="16" list="inv-unidades" value="<?= esc($campo('unidade', 'un')) ?>" required><datalist id="inv-unidades"><?php foreach (['un', 'folha', 'm', 'm²', 'ml', 'L', 'g', 'kg', 'UN', 'PCT', 'FRD'] as $unidade): ?><option value="<?= esc($unidade) ?>"><?php endforeach; ?></datalist></div>
            <div class="col-md-4 form-group"><label for="inv-quantidade">Quantidade</label><input id="inv-quantidade" class="form-control" name="quantidade" inputmode="decimal" maxlength="32" value="<?= esc($campo('quantidade')) ?>" required><small class="text-muted">Até quatro casas decimais.</small></div>
            <div class="col-md-4 form-group"><label for="inv-valor">Valor unitário (R$)</label><input id="inv-valor" class="form-control" name="valor_unitario" inputmode="decimal" maxlength="32" value="<?= esc($campo('valor_unitario')) ?>" required></div>
        </div>
    </div><div class="card-footer text-right"><button class="btn btn-primary" type="submit">Salvar item</button></div></div>
</form>
</div></div></div>
