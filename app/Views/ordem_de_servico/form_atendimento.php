<?php
$v = static fn ($campo, $padrao = '') => esc($ordem[$campo] ?? $padrao, 'attr');
$id = (int) ($ordem['id_ordem'] ?? 0);
$tipos = ['fixo' => 'Preço fixo', 'unidade' => 'Por unidade', 'metro_linear' => 'Por metro linear', 'metro_quadrado' => 'Por m²', 'quantidade' => 'Por quantidade / lote'];
?>
<div class="content-wrapper"><section class="content pt-3"><div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div><h1 class="h4 mb-1"><?= esc($titulo_pagina) ?></h1><span class="text-muted"><?= $id ? esc($ordem['numero'] ?: 'Atendimento #' . $id) : 'Cliente, serviços e pagamento em um só lugar.' ?></span></div>
        <a class="btn btn-outline-secondary" href="<?= $id ? '/ordensDeServicos/show/' . $id : '/ordensDeServicos/orcamentos' ?>">Voltar</a>
    </div>
    <?php if ($erro = session()->getFlashdata('atendimento_erro')): ?><div class="alert alert-danger" role="alert"><?= esc($erro) ?></div><?php endif ?>
    <form id="form-atendimento" action="/ordensDeServicos/salvarAtendimento" method="post">
        <?= csrf_field() ?>
        <?php if ($id): ?><input type="hidden" name="id_ordem" value="<?= $id ?>"><?php endif ?>
        <input type="hidden" name="versao" value="<?= $v('versao', 0) ?>">
        <input type="hidden" name="chave_criacao" value="<?= $v('chave_criacao') ?>">
        <input type="hidden" name="itens_json" id="at-itens-json">
        <input type="hidden" name="parcelas_json" id="at-parcelas-json">
        <div class="card"><div class="card-header"><h2 class="card-title">Cliente e responsáveis</h2></div><div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group"><label for="at-cliente">Cliente</label>
                    <select id="at-cliente" name="id_cliente" class="form-control select2" required>
                        <option value="">Selecione o cliente</option>
                        <?php foreach ($clientes as $c): $nome = ($c['tipo'] ?? 1) != 1 && ! empty($c['razao_social']) ? $c['razao_social'] : $c['nome']; ?>
                        <option value="<?= (int) $c['id_cliente'] ?>" <?= (int) ($ordem['id_cliente'] ?? 0) === (int) $c['id_cliente'] ? 'selected' : '' ?>><?= esc($nome) ?></option>
                        <?php endforeach ?>
                    </select><a class="small" href="/clientes/create" target="_blank" rel="noopener">Cadastrar cliente</a>
                </div>
                <div class="col-md-3 form-group"><label for="at-vendedor">Vendedor</label><select id="at-vendedor" name="id_vendedor" class="form-control">
                    <option value="">Geral</option><?php foreach ($vendedores as $p): ?><option value="<?= (int) $p['id_vendedor'] ?>" <?= (int) ($ordem['id_vendedor'] ?? 0) === (int) $p['id_vendedor'] ? 'selected' : '' ?>><?= esc($p['nome']) ?><?= ! empty($p['inativo_no_atendimento']) ? ' (inativo — responsável anterior)' : '' ?></option><?php endforeach ?>
                </select></div>
                <div class="col-md-3 form-group"><label for="at-tecnico">Técnico <small class="text-muted">(opcional)</small></label><select id="at-tecnico" name="id_tecnico" class="form-control">
                    <option value="">A definir</option><?php foreach ($tecnicos as $p): ?><option value="<?= (int) $p['id_tecnico'] ?>" <?= (int) ($ordem['id_tecnico'] ?? 0) === (int) $p['id_tecnico'] ? 'selected' : '' ?>><?= esc($p['nome']) ?><?= ! empty($p['inativo_no_atendimento']) ? ' (inativo — responsável anterior)' : '' ?></option><?php endforeach ?>
                </select></div>
            </div>
            <div id="at-cliente-resumo" class="small text-muted mb-2"></div>
            <div class="small">Atendimento por <strong><?= esc($ordem['atendente']['primeiro_nome'] ?? session('primeiro_nome') ?? 'Usuário atual') ?></strong><?= $id ? ' · Criado em ' . esc(date('d/m/Y H:i', strtotime($ordem['created_at']))) : ' · Data e hora registradas ao salvar.' ?></div>
        </div></div>
        <div class="card"><div class="card-header"><h2 class="card-title">Serviços e cortesias</h2></div><div class="card-body">
            <div class="row align-items-end mb-3"><div class="col-md-7 form-group mb-md-0">
                <label for="at-catalogo">Catálogo de serviços</label><select id="at-catalogo" class="form-control select2"><option value="">Selecione um serviço</option><?php foreach ($catalogo as $s): ?><option value="<?= (int) $s['id_servico'] ?>"><?= esc($s['nome']) ?> · <?= moeda($s['valor']) ?></option><?php endforeach ?></select>
            </div><div class="col-md-5"><button type="button" id="at-add-servico" class="btn btn-primary mr-1">+ Adicionar serviço</button><button type="button" id="at-add-brinde" class="btn btn-outline-success">+ Adicionar brinde</button></div></div>
            <div id="at-itens" aria-live="polite"></div>
            <div id="at-vazio" class="text-muted text-center border rounded p-4">Selecione os serviços que serão realizados.</div>
        </div></div>
        <div class="row">
            <div class="col-lg-7">
                <div class="card"><div class="card-header"><h2 class="card-title">Pagamento combinado</h2></div><div class="card-body">
                    <p class="small text-muted">Informe a forma e os vencimentos combinados. Registre os valores recebidos na ficha após salvar.</p>
                    <div id="at-parcelas"></div><button type="button" id="at-add-parcela" class="btn btn-outline-primary btn-sm">+ Adicionar outra forma / parcela</button>
                    <div id="at-plano-resumo" class="small mt-2" aria-live="polite"></div>
                    <div class="form-group mt-3"><label for="at-condicao">Condição de pagamento</label><input id="at-condicao" class="form-control" name="condicao_pagamento" maxlength="8000" value="<?= $v('condicao_pagamento') ?>" placeholder="Ex.: 50% de entrada e 50% na conclusão"></div>
                    <div class="custom-control custom-checkbox mb-2"><input type="checkbox" id="at-entrada" class="custom-control-input" <?= ! empty($ordem['entrada_necessaria']) ? 'checked' : '' ?>><label for="at-entrada" class="custom-control-label">Exigir entrada</label></div>
                    <input type="hidden" id="at-entrada-necessaria" name="entrada_necessaria" value="<?= ! empty($ordem['entrada_necessaria']) ? 1 : 0 ?>">
                    <div id="at-entrada-campo" class="form-group"><label for="at-entrada-valor">Valor da entrada (R$)</label><input id="at-entrada-valor" name="valor_entrada" class="form-control" inputmode="decimal" value="<?= $v('valor_entrada', '0.00') ?>"><div id="at-entrada-saldo" class="small text-muted mt-1"></div></div>
                </div></div>
            </div>
            <div class="col-lg-5">
                <div class="card"><div class="card-header"><h2 class="card-title">Valores</h2></div><div class="card-body">
                    <div class="row"><div class="col-6 form-group"><label for="at-desconto-tipo">Desconto</label><select id="at-desconto-tipo" name="desconto_tipo" class="form-control"><?php foreach (['nenhum'=>'Sem desconto','valor'=>'Em reais (R$)','percentual'=>'Em percentual (%)'] as $key=>$label): ?><option value="<?= $key ?>" <?= ($ordem['desconto_tipo'] ?? 'nenhum') === $key ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach ?></select></div>
                    <div class="col-6 form-group" id="at-desconto-campo"><label for="at-desconto-valor">Valor do desconto</label><input id="at-desconto-valor" name="desconto_informado" class="form-control" inputmode="decimal" value="<?= $v('desconto_informado', '0.00') ?>"></div></div>
                    <div class="form-group" id="at-desconto-motivo-campo"><label for="at-desconto-motivo">Motivo <small>(opcional)</small></label><input id="at-desconto-motivo" name="desconto_motivo" class="form-control" maxlength="255" value="<?= $v('desconto_motivo') ?>"></div>
                    <details><summary class="small text-muted mb-2">Frete e outros valores</summary><div class="row"><div class="col-6 form-group"><label for="at-frete">Frete (R$)</label><input id="at-frete" name="frete" class="form-control" inputmode="decimal" value="<?= $v('frete', '0.00') ?>"></div><div class="col-6 form-group"><label for="at-outros">Outros (R$)</label><input id="at-outros" name="outros" class="form-control" inputmode="decimal" value="<?= $v('outros', '0.00') ?>"></div></div></details>
                    <div class="d-flex justify-content-between"><span>Subtotal cobrado</span><strong id="at-subtotal">R$ 0,00</strong></div>
                    <div class="d-flex justify-content-between mt-2"><span>Desconto</span><span id="at-desconto">R$ 0,00</span></div>
                    <hr><div class="d-flex justify-content-between h4"><span>Total</span><strong id="at-total">R$ 0,00</strong></div>
                    <div class="small text-muted">As cortesias não aumentam o valor cobrado.</div>
                </div></div>
            </div>
        </div>
        <div id="at-externa" class="card"><div class="card-header"><h2 class="card-title">Execução externa / instalação</h2></div><div class="card-body">
            <button type="button" id="at-usar-endereco" class="btn btn-outline-secondary btn-sm mb-3">Usar endereço do cliente</button>
            <div class="form-group"><label for="at-endereco">Endereço da execução</label><textarea id="at-endereco" name="execucao_endereco" class="form-control" rows="2" maxlength="8000"><?= esc($ordem['execucao_endereco'] ?? '') ?></textarea></div>
            <div class="row"><div class="col-md-6 form-group"><label for="at-referencia">Referência</label><input id="at-referencia" class="form-control" name="execucao_referencia" maxlength="255" value="<?= $v('execucao_referencia') ?>"></div><div class="col-md-6 form-group"><label for="at-exec-prevista">Data e horário previstos</label><input id="at-exec-prevista" type="datetime-local" class="form-control" name="execucao_prevista" value="<?= esc(! empty($ordem['execucao_prevista']) ? date('Y-m-d\TH:i', strtotime($ordem['execucao_prevista'])) : '', 'attr') ?>"></div></div>
            <div class="row"><div class="col-md-6 form-group"><label for="at-responsavel">Responsável no local</label><input id="at-responsavel" class="form-control" name="execucao_responsavel" maxlength="128" value="<?= $v('execucao_responsavel') ?>"></div><div class="col-md-6 form-group"><label for="at-telefone">Telefone</label><input id="at-telefone" class="form-control" name="execucao_telefone" maxlength="32" value="<?= $v('execucao_telefone') ?>"></div></div>
            <div class="form-group"><label for="at-externa-obs">Observações da execução</label><textarea id="at-externa-obs" class="form-control" name="execucao_observacoes" rows="2" maxlength="8000"><?= esc($ordem['execucao_observacoes'] ?? '') ?></textarea></div>
            <small class="text-muted">Fotos do local e arquivos de arte podem ser anexados na ficha após salvar.</small>
        </div></div>
        <div class="card"><div class="card-header"><h2 class="card-title">Prazos e observações</h2></div><div class="card-body">
            <div class="row"><div class="col-md-4 form-group"><label for="at-validade">Validade do orçamento</label><input id="at-validade" type="date" name="validade_orcamento" class="form-control" value="<?= $v('validade_orcamento') ?>"></div><div class="col-md-4 form-group"><label for="at-conclusao">Previsão de conclusão</label><input id="at-conclusao" type="date" name="previsao_conclusao" class="form-control" value="<?= $v('previsao_conclusao') ?>"></div><div class="col-md-4 form-group"><label for="at-status">Situação</label><select id="at-status" name="status_operacional" class="form-control"><?php foreach ($statuses as $key=>$label): if ($key === 'cancelado') continue; ?><option value="<?= $key ?>" <?= ($ordem['status_operacional'] ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach ?></select></div></div>
            <div class="form-group"><label for="at-obs">Observações para o cliente</label><textarea id="at-obs" name="observacoes" class="form-control" rows="3" maxlength="2048"><?= esc($ordem['observacoes'] ?? '') ?></textarea></div>
            <details><summary class="small mb-2">Informações internas</summary><div class="form-group"><label for="at-obs-internas">Observações internas</label><textarea id="at-obs-internas" name="observacoes_internas" class="form-control" rows="2" maxlength="2048"><?= esc($ordem['observacoes_internas'] ?? '') ?></textarea></div><div class="row"><div class="col-md-6 form-group"><label>Canal de atendimento</label><select name="canal_de_venda" class="form-control"><option <?= ($ordem['canal_de_venda'] ?? 'Presencial') === 'Presencial' ? 'selected' : '' ?>>Presencial</option><option <?= ($ordem['canal_de_venda'] ?? '') === 'Remoto' ? 'selected' : '' ?>>Remoto</option></select></div><div class="col-md-6 form-group"><label>Centro de custo</label><input class="form-control" name="centro_de_custo" maxlength="128" value="<?= $v('centro_de_custo') ?>"></div></div></details>
        </div></div>
        <div id="at-erro" class="alert alert-danger" role="alert" hidden></div>
        <div class="card"><div class="card-body d-flex justify-content-between align-items-center flex-wrap"><span id="at-salvando" class="text-muted small">Confira os dados antes de salvar.</span><button type="submit" id="at-salvar" class="btn btn-primary btn-lg" disabled>Salvar orçamento</button></div></div>
    </form>
</div></section></div>
<script>
window.atendimentoDados = <?= json_encode(['ordem'=>$ordem,'catalogo'=>$catalogo,'clientes'=>$clientes,'formas'=>array_column($formas,'nome'),'tipos'=>$tipos,'hoje'=>date('Y-m-d')], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE) ?>;
</script>
<script src="<?= base_url('assets/js/atendimento-form.js') ?>"></script>
