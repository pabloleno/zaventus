<?php
use App\Libraries\ImagemCadastro;

$registro = $servico ?? [];
$campo = static function (string $nome, $padrao = '') use ($registro): string {
    $valor = old($nome, $registro[$nome] ?? $padrao, false);
    return is_scalar($valor) ? (string) $valor : '';
};
$tipos = ['fixo' => 'Preço fixo', 'unidade' => 'Por unidade', 'metro_linear' => 'Por metro linear', 'metro_quadrado' => 'Por metro quadrado', 'quantidade' => 'Por quantidade / lote'];
$execucoes = ['interna' => 'Interna', 'externa' => 'Externa', 'mista' => 'Interna + externa'];
$artes = ['nao_necessita' => 'Não necessita arte', 'cliente' => 'Cliente já possui arte', 'grafica' => 'Gráfica irá produzir a arte'];
$medidas = $campo('possui_medidas', ($registro['largura_padrao'] ?? null) !== null || ($registro['altura_padrao'] ?? null) !== null ? '1' : '0') === '1';
?>
<div class="content-wrapper"><div class="content"><div class="container-fluid">
<form action="/servicosMaoDeObra/store" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php if (isset($registro['id_servico'])): ?><input type="hidden" name="id_servico" value="<?= esc((string) $registro['id_servico']) ?>"><?php endif; ?>
    <div class="card">
        <div class="card-header">
            <a href="/servicosMaoDeObra" class="btn btn-success btn-sm float-right"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
            <h6 class="m-0 pt-1"><i class="fa fa-print mr-1"></i> <?= isset($servico) ? 'Editar serviço' : 'Novo serviço' ?></h6>
        </div>
        <div class="card-body">
            <?php $erros = session()->getFlashdata('erros_catalogo') ?? []; ?>
            <?php if ($erros !== []): ?>
                <div class="alert alert-danger" role="alert">
                    <?php foreach ((array) $erros as $erro): ?><div><?= esc((string) $erro) ?></div><?php endforeach; ?>
                    <small>Os dados preenchidos foram mantidos. Se enviou uma imagem, selecione o arquivo novamente.</small>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-9 form-group"><label for="servico-nome">Nome do serviço</label><input id="servico-nome" class="form-control" name="nome" maxlength="128" value="<?= esc($campo('nome')) ?>" required></div>
                <div class="col-md-3 form-group"><label for="servico-ativo">Situação</label>
                    <select id="servico-ativo" class="form-control" name="ativo">
                        <option value="1" <?= $campo('ativo', '1') === '1' ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= $campo('ativo', '1') === '0' ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
                <div class="col-12 form-group"><label for="servico-descricao">Descrição</label><textarea id="servico-descricao" class="form-control" name="descricao" maxlength="1024" rows="2"><?= esc($campo('descricao')) ?></textarea></div>
                <div class="col-md-4 form-group"><label for="servico-tipo">Como o serviço é cobrado?</label>
                    <select id="servico-tipo" class="form-control" name="tipo_preco">
                        <?php foreach ($tipos as $valor => $rotulo): ?><option value="<?= esc($valor) ?>" <?= $campo('tipo_preco', 'unidade') === $valor ? 'selected' : '' ?>><?= esc($rotulo) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 form-group"><label for="servico-valor">Preço (R$)</label><input id="servico-valor" class="form-control" name="valor" inputmode="decimal" maxlength="32" value="<?= esc($campo('valor', '0,00')) ?>" required></div>
                <div class="col-md-4 form-group"><label for="servico-unidade">Unidade / métrica</label><input id="servico-unidade" class="form-control" name="unidade" list="servico-unidades" maxlength="16" value="<?= esc($campo('unidade', 'un')) ?>" required><datalist id="servico-unidades"><option value="un"><option value="m"><option value="m²"><option value="folha"><option value="lote"></datalist></div>
            </div>
            <p id="servico-ajuda" class="small text-muted">O preço é multiplicado pela quantidade no orçamento.</p>
            <div class="custom-control custom-checkbox mb-3">
                <input type="hidden" name="possui_medidas" value="0">
                <input type="checkbox" class="custom-control-input" id="servico-possui-medidas" name="possui_medidas" value="1" <?= $medidas ? 'checked' : '' ?>>
                <label class="custom-control-label" for="servico-possui-medidas">Possui medidas padrão</label>
            </div>
            <div id="servico-medidas" <?= $medidas ? '' : 'hidden' ?>>
                <div class="row">
                    <div class="col-md-4 form-group"><label for="servico-largura">Largura padrão</label><input id="servico-largura" class="form-control" name="largura_padrao" inputmode="decimal" maxlength="24" value="<?= esc($campo('largura_padrao')) ?>"></div>
                    <div class="col-md-4 form-group"><label for="servico-altura">Altura padrão</label><input id="servico-altura" class="form-control" name="altura_padrao" inputmode="decimal" maxlength="24" value="<?= esc($campo('altura_padrao')) ?>"></div>
                    <div class="col-md-4 form-group"><label for="servico-medida-unidade">Unidade das medidas</label><select id="servico-medida-unidade" class="form-control" name="unidade_dimensao">
                        <?php foreach (['m' => 'Metros (m)', 'cm' => 'Centímetros (cm)', 'mm' => 'Milímetros (mm)'] as $valor => $rotulo): ?><option value="<?= esc($valor) ?>" <?= $campo('unidade_dimensao', 'm') === $valor ? 'selected' : '' ?>><?= esc($rotulo) ?></option><?php endforeach; ?>
                    </select></div>
                </div>
                <p class="small text-muted">Medidas opcionais. Elas poderão ser preenchidas ou ajustadas no orçamento.</p>
            </div>
        </div>
    </div>
    <div class="card"><div class="card-header"><h6 class="m-0">Produção e execução</h6></div><div class="card-body">
        <div class="row">
            <div class="col-md-6 form-group"><label for="servico-execucao">Tipo de execução</label><select id="servico-execucao" class="form-control" name="tipo_execucao">
                <?php foreach ($execucoes as $valor => $rotulo): ?><option value="<?= esc($valor) ?>" <?= $campo('tipo_execucao', 'interna') === $valor ? 'selected' : '' ?>><?= esc($rotulo) ?></option><?php endforeach; ?>
            </select></div>
            <div class="col-md-6 form-group"><label for="servico-arte">Arte</label><select id="servico-arte" class="form-control" name="arte_padrao">
                <?php foreach ($artes as $valor => $rotulo): ?><option value="<?= esc($valor) ?>" <?= $campo('arte_padrao', 'nao_necessita') === $valor ? 'selected' : '' ?>><?= esc($rotulo) ?></option><?php endforeach; ?>
            </select></div>
        </div>
        <div class="custom-control custom-checkbox"><input type="hidden" name="necessita_instalacao" value="0"><input type="checkbox" class="custom-control-input" id="servico-instalacao" name="necessita_instalacao" value="1" <?= $campo('necessita_instalacao', '0') === '1' ? 'checked' : '' ?>><label class="custom-control-label" for="servico-instalacao">Necessita instalação</label></div>
    </div></div>
    <div class="card"><div class="card-header"><h6 class="m-0">Imagem e observações</h6></div><div class="card-body"><div class="row">
        <div class="col-md-4 form-group">
            <?php if (! empty($registro['imagem'])): ?>
                <img src="<?= esc(ImagemCadastro::url($registro['imagem'])) ?>" alt="Imagem do serviço" class="img-thumbnail d-block mb-2" style="max-height: 160px; max-width: 100%">
                <div class="custom-control custom-checkbox mb-2"><input type="checkbox" class="custom-control-input" id="servico-remover-imagem" name="remover_imagem" value="1" <?= $campo('remover_imagem', '0') === '1' ? 'checked' : '' ?>><label class="custom-control-label" for="servico-remover-imagem">Remover imagem atual</label></div>
            <?php endif; ?>
            <label for="servico-imagem">Imagem do serviço</label><input type="file" id="servico-imagem" name="imagem" class="form-control-file" accept="image/jpeg,image/png,image/webp"><small class="text-muted">JPG, PNG ou WEBP. Até 2 MB.</small>
        </div>
        <div class="col-md-8 form-group"><label for="servico-observacoes">Observações</label><textarea id="servico-observacoes" class="form-control" name="observacoes" rows="4" maxlength="2048"><?= esc($campo('observacoes')) ?></textarea></div>
    </div></div><div class="card-footer text-right"><button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Salvar serviço</button></div></div>
</form>
</div></div></div>
<script>
(function () {
    var checkbox = document.getElementById('servico-possui-medidas');
    var tipo = document.getElementById('servico-tipo');
    var textos = {
        fixo: 'Preço por serviço de tamanho fixo, multiplicado pela quantidade.',
        unidade: 'O preço é multiplicado pela quantidade no orçamento.',
        metro_linear: 'Total = largura em metros × quantidade × preço por metro.',
        metro_quadrado: 'Total = largura × altura em metros × quantidade × preço por m².',
        quantidade: 'Para um lote de 500 cartões, use unidade “lote” e o preço do lote; quantidade 2 significa dois lotes.'
    };
    function atualizar() {
        document.getElementById('servico-medidas').hidden = !checkbox.checked;
        document.getElementById('servico-ajuda').textContent = textos[tipo.value] || '';
    }
    checkbox.addEventListener('change', atualizar);
    tipo.addEventListener('change', atualizar);
    atualizar();
}());
</script>
