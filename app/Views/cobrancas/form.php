<?php
    $registro = $cobranca ?? [];
    $valorCampo = static function (string $campo, $padrao = '') use ($registro) {
        return old($campo, $registro[$campo] ?? $padrao);
    };
    $marcado = static function (string $campo) use ($registro): bool {
        return (bool) old($campo, $registro[$campo] ?? false);
    };
    $errors = session()->getFlashdata('errors') ?? [];
?>

<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <form action="/cobrancas/store" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= esc($titulo['icone']) ?>"></i> <?= esc($titulo['modulo']) ?></h6>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <a href="/cobrancas" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Esta cobranca funciona somente como agenda de lembretes. Ela nao cria contas a receber, vendas, servicos ou lancamentos financeiros.
                        </div>

                        <?php if (! empty($errors)) : ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $erro) : ?>
                                    <div><?= esc($erro) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label>Cliente a cobrar</label>
                                    <select class="form-control select2" name="id_cliente" style="width: 100%;" required>
                                        <option value="">-- Selecione --</option>
                                        <?php foreach ($clientes as $cliente) : ?>
                                            <?php $nomeCliente = trim((string) ($cliente['nome'] ?: $cliente['razao_social'])); ?>
                                            <option value="<?= $cliente['id_cliente'] ?>" <?= (string) $valorCampo('id_cliente') === (string) $cliente['id_cliente'] ? 'selected' : '' ?>>
                                                <?= esc($nomeCliente) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label>Titulo da cobranca</label>
                                    <input type="text" class="form-control" name="titulo" maxlength="120" value="<?= esc($valorCampo('titulo')) ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option value="Ativa" <?= $valorCampo('status', 'Ativa') === 'Ativa' ? 'selected' : '' ?>>Ativa</option>
                                        <option value="Pausada" <?= $valorCampo('status') === 'Pausada' ? 'selected' : '' ?>>Pausada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Descricao</label>
                                    <textarea class="form-control" name="descricao" rows="2"><?= esc($valorCampo('descricao')) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <h6 class="cobranca-form-secao"><i class="fas fa-calendar-alt"></i> Parcelamento e recorrencia</h6>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Valor total a cobrar</label>
                                    <input id="cobranca-valor-total" type="text" class="form-control" name="valor_total" value="<?= esc($valorCampo('valor_total')) ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Parcelas</label>
                                    <input id="cobranca-parcelas" type="number" class="form-control" name="quantidade_parcelas" min="1" max="999" value="<?= esc($valorCampo('quantidade_parcelas', 1)) ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Periodicidade entre parcelas</label>
                                    <select id="cobranca-recorrencia" class="form-control" name="recorrencia">
                                        <?php foreach ($recorrencias as $valor => $rotulo) : ?>
                                            <option value="<?= esc($valor) ?>" <?= $valorCampo('recorrencia', 'Unica') === $valor ? 'selected' : '' ?>><?= esc($rotulo) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div id="cobranca-intervalo-container" class="col-lg-2">
                                <div class="form-group">
                                    <label>Intervalo em dias</label>
                                    <input id="cobranca-intervalo" type="number" class="form-control" name="intervalo_personalizado_dias" min="1" max="36500" value="<?= esc($valorCampo('intervalo_personalizado_dias', 1)) ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Valor estimado/parcela</label>
                                    <div id="cobranca-valor-parcela" class="form-control bg-light">R$ 0,00</div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Primeiro vencimento</label>
                                    <input type="date" class="form-control" name="data_inicio" value="<?= esc($valorCampo('data_inicio', date('Y-m-d'))) ?>" required>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Horario</label>
                                    <input type="time" class="form-control" name="hora_cobranca" value="<?= esc(substr((string) $valorCampo('hora_cobranca', date('H:i')), 0, 5)) ?>" required>
                                </div>
                            </div>
                        </div>

                        <h6 class="cobranca-form-secao"><i class="fas fa-bell"></i> Alertas opcionais antes do prazo</h6>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="lembrete_1_hora" value="0">
                                    <input id="lembrete-1h" class="custom-control-input" type="checkbox" name="lembrete_1_hora" value="1" <?= $marcado('lembrete_1_hora') ? 'checked' : '' ?>>
                                    <label for="lembrete-1h" class="custom-control-label">1 hora antes</label>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="lembrete_1_dia" value="0">
                                    <input id="lembrete-1d" class="custom-control-input" type="checkbox" name="lembrete_1_dia" value="1" <?= $marcado('lembrete_1_dia') ? 'checked' : '' ?>>
                                    <label for="lembrete-1d" class="custom-control-label">1 dia antes</label>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="hidden" name="lembrete_1_semana" value="0">
                                    <input id="lembrete-1s" class="custom-control-input" type="checkbox" name="lembrete_1_semana" value="1" <?= $marcado('lembrete_1_semana') ? 'checked' : '' ?>>
                                    <label for="lembrete-1s" class="custom-control-label">1 semana antes</label>
                                </div>
                            </div>
                        </div>

                        <h6 class="cobranca-form-secao"><i class="fas fa-percentage"></i> Atraso</h6>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="hidden" name="juros_atraso" value="0">
                                    <input id="cobranca-juros" class="custom-control-input" type="checkbox" name="juros_atraso" value="1" <?= $marcado('juros_atraso') ? 'checked' : '' ?>>
                                    <label for="cobranca-juros" class="custom-control-label">Calcular juros simples por dia</label>
                                </div>
                            </div>
                            <div id="cobranca-juros-container" class="col-lg-2">
                                <div class="form-group">
                                    <label>Juros ao dia (%)</label>
                                    <input type="number" class="form-control" name="juros_percentual" min="0" step="0.0001" value="<?= esc($valorCampo('juros_percentual', 0)) ?>">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Observacoes internas</label>
                                    <textarea class="form-control" name="observacoes" rows="3"><?= esc($valorCampo('observacoes')) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <?php if (! empty($registro['id_cobranca'])) : ?>
                            <input type="hidden" name="id_cobranca" value="<?= $registro['id_cobranca'] ?>">
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= empty($registro) ? 'Cadastrar cobranca' : 'Atualizar cobranca' ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        /**
         * Converte a entrada monetaria para numero usado na estimativa.
         */
        function numeroMonetario(valor) {
            valor = String(valor || '').replace(/[^\d,.-]/g, '');

            if (valor.indexOf(',') >= 0) {
                valor = valor.replace(/\./g, '').replace(',', '.');
            }

            return Number(valor) || 0;
        }

        /**
         * Atualiza campos condicionais e a estimativa por parcela.
         */
        function atualizarFormularioCobranca() {
            var recorrencia = $('#cobranca-recorrencia').val();
            var unica = recorrencia === 'Unica';
            var personalizada = recorrencia === 'Personalizada';
            var parcelas = Math.max(1, Number($('#cobranca-parcelas').val()) || 1);
            var total = numeroMonetario($('#cobranca-valor-total').val());

            $('#cobranca-parcelas').prop('readonly', unica);
            if (unica) {
                parcelas = 1;
                $('#cobranca-parcelas').val(1);
            }

            $('#cobranca-intervalo-container').toggle(personalizada);
            $('#cobranca-intervalo').prop('required', personalizada);
            $('#cobranca-juros-container').toggle($('#cobranca-juros').is(':checked'));
            $('#cobranca-valor-parcela').text(new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(total / parcelas));
        }

        $('#cobranca-recorrencia, #cobranca-parcelas, #cobranca-valor-total, #cobranca-juros').on('change keyup', atualizarFormularioCobranca);
        atualizarFormularioCobranca();
    });
</script>
