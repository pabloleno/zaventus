<?php
$registro = $produto ?? [];
$campo = static function (string $nome, $padrao = '') use ($registro): string { $v = old($nome, $registro[$nome] ?? $padrao, false); return is_scalar($v) ? (string) $v : ''; };
$unidades = ['un', 'folha', 'm', 'm²', 'ml', 'L', 'g', 'kg', 'UN', 'PCT', 'FRD'];
if (!empty($registro['unidade']) && !in_array($registro['unidade'], $unidades, true)) { $unidades[] = $registro['unidade']; }
$arquivo = basename((string) ($registro['arquivo'] ?? ''));
$imagem = $arquivo !== '' && is_file(FCPATH . 'assets/img/produtos/' . $arquivo) ? $arquivo : 'produto-sem-imagem.jpg';
$alerta = session()->getFlashdata('alert');
?>
<div class="content-wrapper"><div class="content"><div class="container-fluid">
<form action="/produtos/store" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if (isset($registro['id_produto'])): ?><input type="hidden" name="id_produto" value="<?= (int) $registro['id_produto'] ?>"><input type="hidden" name="quantidade_original" value="<?= esc($campo('quantidade_original', $registro['quantidade'])) ?>"><?php endif; ?>
    <div class="card"><div class="card-header"><a href="/produtos" class="btn btn-success btn-sm float-right">Voltar</a><h6 class="m-0 pt-1"><?= isset($produto) ? 'Editar matéria-prima' : 'Nova matéria-prima' ?></h6></div><div class="card-body">
        <?php foreach ((array) (session()->getFlashdata('erros_material') ?? []) as $erro): ?><div class="alert alert-danger" role="alert"><?= esc((string) $erro) ?><small class="d-block">Os dados foram mantidos. Se enviou imagem, selecione o arquivo novamente.</small></div><?php endforeach; ?>
        <?php if (in_array($alerta, ['success_edit', 'success_remove_image'], true)): ?><div class="alert alert-success" role="status">Matéria-prima atualizada com sucesso.</div><?php endif; ?>
        <div class="row">
            <div class="col-md-8 form-group"><label for="material-nome">Nome</label><input id="material-nome" class="form-control" name="nome" maxlength="512" value="<?= esc($campo('nome')) ?>" required></div>
            <div class="col-md-2 form-group"><label for="material-unidade">Unidade</label><select id="material-unidade" class="form-control" name="unidade"><?php foreach ($unidades as $unidade): ?><option value="<?= esc($unidade) ?>" <?= $campo('unidade', 'un') === $unidade ? 'selected' : '' ?>><?= esc($unidade) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2 form-group"><label for="material-ativo">Situação</label><select id="material-ativo" class="form-control" name="ativo"><option value="1" <?= $campo('ativo', '1') === '1' ? 'selected' : '' ?>>Ativo</option><option value="0" <?= $campo('ativo', '1') === '0' ? 'selected' : '' ?>>Inativo</option></select></div>
            <div class="col-md-3 form-group"><label for="material-codigo">Código de barras</label><input id="material-codigo" class="form-control" name="codigo_de_barras" maxlength="13" value="<?= esc($campo('codigo_de_barras')) ?>"></div>
            <div class="col-md-5 form-group"><label for="material-local">Localização</label><input id="material-local" class="form-control" name="localizacao" maxlength="128" value="<?= esc($campo('localizacao')) ?>"></div>
            <div class="col-md-2 form-group"><label for="material-quantidade">Quantidade atual</label><input id="material-quantidade" class="form-control" name="quantidade" inputmode="decimal" maxlength="32" value="<?= esc($campo('quantidade', '0')) ?>" required></div>
            <div class="col-md-2 form-group"><label for="material-minimo">Estoque mínimo</label><input id="material-minimo" class="form-control" name="quantidade_minima" inputmode="decimal" maxlength="32" value="<?= esc($campo('quantidade_minima', '0')) ?>" required></div>
        </div>
        <p class="small text-muted">Quantidades aceitam até quatro casas decimais. Alterações no saldo geram uma reposição ou saída para manter o histórico.</p>
        <div class="row">
            <?php foreach (['margem_de_lucro' => ['Margem de lucro (%)', 'calculaMargemDeLucro()'], 'valor_de_custo' => ['Valor de custo (R$)', 'calculaMargemDeLucro()'], 'valor_de_venda' => ['Valor de venda (R$)', 'calculaMargemDeLucroInverso()'], 'lucro' => ['Lucro (R$)', 'calculaMargemDeLucroInversoLucro()']] as $nome => [$rotulo, $calculo]): ?>
                <div class="col-md-3 form-group"><label for="<?= esc($nome) ?>"><?= esc($rotulo) ?></label><input id="<?= esc($nome) ?>" class="form-control" name="<?= esc($nome) ?>" inputmode="decimal" maxlength="32" onkeyup="<?= esc($calculo) ?>" value="<?= esc($campo($nome, '0')) ?>" required></div>
            <?php endforeach; ?>
            <div class="col-md-3 form-group"><label for="material-validade">Validade</label><input id="material-validade" type="date" class="form-control" name="validade" value="<?= esc($campo('validade') === '0000-00-00' ? '' : $campo('validade')) ?>"></div>
            <div class="col-md-4 form-group"><label for="material-categoria">Categoria</label><select id="material-categoria" class="form-control select2" name="id_categoria" required><?php foreach ($categorias ?? [] as $categoria): ?><option value="<?= (int) $categoria['id_categoria'] ?>" <?= $campo('id_categoria') === (string) $categoria['id_categoria'] ? 'selected' : '' ?>><?= esc($categoria['nome']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-5 form-group"><label for="material-fornecedor">Fornecedor</label><select id="material-fornecedor" class="form-control select2" name="id_fornecedor" required><?php foreach ($fornecedores ?? [] as $fornecedor): ?><option value="<?= (int) $fornecedor['id_fornecedor'] ?>" <?= $campo('id_fornecedor') === (string) $fornecedor['id_fornecedor'] ? 'selected' : '' ?>><?= esc($fornecedor['nome_do_representante']) ?> — <?= esc($fornecedor['nome_da_empresa']) ?></option><?php endforeach; ?></select></div>
        </div>
    </div></div>
    <div class="card"><div class="card-header"><h6 class="m-0">Imagem e observações</h6></div><div class="card-body"><div class="row">
        <div class="col-md-4">
            <img src="<?= esc(base_url('assets/img/produtos/' . $imagem)) ?>" alt="Imagem da matéria-prima" class="img-thumbnail d-block mb-2" style="max-height:160px;max-width:100%">
            <label for="material-arquivo">Imagem</label><input type="file" id="material-arquivo" name="arquivo" class="form-control-file" accept="image/jpeg,image/png,image/webp"><small class="d-block text-muted">JPG, PNG ou WEBP. Até 2 MB.</small>
            <?php if (!empty($registro['arquivo'])): ?><button type="submit" form="remover-imagem-material" class="btn btn-outline-danger btn-sm mt-2">Remover imagem</button><?php endif; ?>
        </div>
        <div class="col-md-8 form-group"><label for="material-observacoes">Observações</label><textarea id="material-observacoes" class="form-control" name="observacoes" rows="5" maxlength="4096"><?= esc($campo('observacoes')) ?></textarea></div>
    </div></div><div class="card-footer text-right"><button class="btn btn-primary" type="submit">Salvar matéria-prima</button></div></div>
</form>
<?php if (!empty($registro['arquivo'])): ?><form id="remover-imagem-material" action="/produtos/removerImagem/<?= (int) $registro['id_produto'] ?>" method="post"><?= csrf_field() ?></form><?php endif; ?>
</div></div></div>
<script>
    /**
     * Calcula margem de lucro.
     */
    function calculaMargemDeLucro()
    {
        var margem_de_lucro = numeroMonetario(document.getElementById('margem_de_lucro').value);
        var valor_de_custo = numeroMonetario(document.getElementById('valor_de_custo').value);
        
        var valor_com_margem = (margem_de_lucro * valor_de_custo / 100);

        var valor_de_venda = numeroMonetario(valor_de_custo) + valor_com_margem;
        document.getElementById('valor_de_venda').value = decimalMonetario(valor_de_venda);

        document.getElementById('lucro').value = decimalMonetario(valor_de_venda - numeroMonetario(valor_de_custo));
    }

    /**
     * Calcula margem de lucro inverso.
     */
    function calculaMargemDeLucroInverso()
    {
        var valor_de_venda = numeroMonetario(document.getElementById('valor_de_venda').value);
        var valor_de_custo  = numeroMonetario(document.getElementById('valor_de_custo').value);

        var lucro = valor_de_venda - valor_de_custo;

        document.getElementById('margem_de_lucro').value = decimalMonetario(valor_de_custo ? lucro / valor_de_custo * 100 : 0);
        document.getElementById('lucro').value = decimalMonetario(lucro);
    }

    /**
     * Calcula margem de lucro inverso lucro.
     */
    function calculaMargemDeLucroInversoLucro()
    {
        var valor_de_custo = document.getElementById('valor_de_custo').value;
        var lucro          = document.getElementById('lucro').value;

        document.getElementById('valor_de_venda').value = decimalMonetario(numeroMonetario(valor_de_custo) + numeroMonetario(lucro));

        calculaMargemDeLucroInverso();
    }

</script>
