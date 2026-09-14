<?php
$campo = static function (string $nome, $padrao = ''): string { $v = old($nome, $padrao, false); return is_scalar($v) ? (string) $v : ''; };
?>
<div class="content-wrapper"><div class="content"><div class="container-fluid">
<form action="/reposicoes/store" method="post">
    <?= csrf_field() ?><input type="hidden" name="chave_operacao" value="<?= esc($campo('chave_operacao', bin2hex(random_bytes(16)))) ?>">
    <div class="card"><div class="card-header"><a href="/reposicoes" class="btn btn-success btn-sm float-right">Voltar</a><h6 class="m-0 pt-1">Nova reposição</h6></div><div class="card-body">
        <?php foreach ((array) (session()->getFlashdata('erros_estoque') ?? []) as $erro): ?><div class="alert alert-danger" role="alert"><?= esc((string) $erro) ?></div><?php endforeach; ?>
        <div class="row"><div class="col-md-8 form-group"><label for="movimento-produto">Matéria-prima</label>
            <select id="movimento-produto" class="form-control select2" name="id_produto" required>
                <option value="">Selecione um material</option>
                <?php foreach ($produtos ?? [] as $produto): ?><option value="<?= (int) $produto['id_produto'] ?>" <?= $campo('id_produto') === (string) $produto['id_produto'] ? 'selected' : '' ?>><?= esc($produto['nome']) ?> — saldo <?= esc($produto['quantidade']) ?> <?= esc($produto['unidade']) ?></option><?php endforeach; ?>
            </select>
        </div><div class="col-md-4 form-group"><label for="movimento-quantidade">Quantidade</label><input id="movimento-quantidade" class="form-control" name="quantidade" inputmode="decimal" maxlength="32" value="<?= esc($campo('quantidade')) ?>" required><small class="text-muted">Use a unidade cadastrada no material. Até quatro casas decimais.</small></div></div>
        <div class="form-group"><label for="movimento-observacoes">Observações</label><textarea id="movimento-observacoes" class="form-control" name="observacoes" rows="3" maxlength="512"><?= esc($campo('observacoes')) ?></textarea></div>
        <p class="small text-muted">Data, hora e responsável são registrados ao salvar.</p>
    </div><div class="card-footer text-right"><button class="btn btn-primary" type="submit">Registrar reposição</button></div></div>
</form>
</div></div></div>
