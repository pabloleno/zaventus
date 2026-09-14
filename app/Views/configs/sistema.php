<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
                        
            <div class="row">
                <?php
                $resumo_pagamentos = $resumo_formas_pagamento ?? [
                    'total' => 0,
                    'manuais' => 0,
                    'vinculadas' => 0,
                    'ativas' => 0,
                    'pendentes' => 0,
                ];
                ?>
                <div class="col-lg-12">
                    <div class="card sistema-pagamentos-card">
                        <div class="card-header sistema-pagamentos-header">
                            <div>
                                <h6 class="m-0 text-dark"><i class="fas fa-credit-card"></i> <?= esc(lang('App.system.payments')) ?></h6>
                                <small class="text-muted">Formas de recebimento e integrações usadas nas vendas de serviços.</small>
                            </div>
                            <div class="sistema-pagamentos-acoes">
                                <a href="/desenvolvedor" class="btn btn-outline-info"><i class="fas fa-plug"></i> Credenciais e testes</a>
                                <a href="/configs/createFormaDePagamento" class="btn btn-primary"><i class="fas fa-plus"></i> <?= esc(lang('App.system.newPayment')) ?></a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="sistema-pagamentos-resumo">
                                <div><span>Total</span><strong><?= (int) $resumo_pagamentos['total'] ?></strong></div>
                                <div><span>Manuais</span><strong><?= (int) $resumo_pagamentos['manuais'] ?></strong></div>
                                <div><span>Vinculadas</span><strong><?= (int) $resumo_pagamentos['vinculadas'] ?></strong></div>
                                <div><span>Ativas</span><strong><?= (int) $resumo_pagamentos['ativas'] ?></strong></div>
                                <div><span>Pendentes</span><strong><?= (int) $resumo_pagamentos['pendentes'] ?></strong></div>
                            </div>

                            <div class="alert alert-light sistema-pagamentos-alerta">
                                <i class="fas fa-info-circle"></i>
                                Cadastre a forma uma vez e use nos orçamentos, vendas e ordens de serviço. PIX, cartão e boleto podem ficar manuais ou vinculados a um provedor configurado.
                            </div>

                            <div class="row">
                                <div class="col-lg-12 table-responsive">
                                    <table id="example1" class="table table-bordered table-striped sistema-pagamentos-tabela">
                                        <thead>
                                            <tr>
                                                <th>Forma</th>
                                                <th>Tipo</th>
                                                <th>Uso</th>
                                                <th>Integracao</th>
                                                <th style="width: 110px"><?= esc(lang('App.common.action')) ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($formas_de_pagamento)) : ?>
                                                <?php foreach ($formas_de_pagamento as $forma) : ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= esc($forma['nome']) ?></strong>
                                                            <small class="d-block text-muted">Cod. <?= (int) $forma['id_forma'] ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-<?= esc($forma['tipo_pagamento_classe'] ?? 'secondary') ?> sistema-pagamento-chip">
                                                                <i class="<?= esc($forma['tipo_pagamento_icone'] ?? 'fas fa-receipt') ?>"></i>
                                                                <?= esc($forma['tipo_pagamento_rotulo'] ?? 'Outros') ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="sistema-pagamentos-badges">
                                                                <?php if ((int) ($forma['disponivel_servicos'] ?? 1) === 1) : ?>
                                                                    <span class="badge badge-primary">Serviços</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php if (! empty($forma['integracao_nome'])) : ?>
                                                                <div class="sistema-pagamentos-integracao-linha">
                                                                    <strong><?= esc($forma['integracao_nome']) ?></strong>
                                                                    <span class="badge badge-<?= esc($forma['status_integracao_classe'] ?? 'secondary') ?>">
                                                                        <?= esc($forma['status_integracao_rotulo'] ?? 'Vinculada') ?>
                                                                    </span>
                                                                </div>
                                                                <small class="text-muted">
                                                                    <?= esc(ucfirst((string) ($forma['integracao_ambiente'] ?? 'sandbox'))) ?>
                                                                    <?= (int) ($forma['integracao_api_publica'] ?? 0) === 1 ? 'com API' : 'manual' ?>
                                                                </small>
                                                            <?php else : ?>
                                                                <span class="badge badge-secondary">Manual / sem API</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="/configs/editFormaDePagamento/<?= $forma['id_forma'] ?>" class="btn btn-warning style-action" title="Editar"><i class="fas fa-edit"></i></a>
                                                            <button type="button" class="btn btn-danger style-action" title="Excluir" onclick="confirmaAcaoExcluir(<?= json_encode(lang('App.system.deletePaymentConfirm')) ?>, '/configs/delete_forma_de_pagamento/<?= $forma['id_forma'] ?>')"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="5"><?= esc(lang('App.common.none')) ?></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <?php if (! empty($integracoes_pagamento)) : ?>
                                <div class="sistema-pagamentos-subtitulo">
                                    <div>
                                        <h6><i class="fas fa-plug"></i> Provedores de pagamento</h6>
                                        <small class="text-muted">Credenciais, diagnostico e vinculos aparecem junto das formas cadastradas.</small>
                                    </div>
                                </div>
                                <div class="sistema-pagamentos-integracoes-grid">
                                    <?php foreach ($integracoes_pagamento as $integracao) : ?>
                                        <div class="sistema-pagamentos-integracao-card">
                                            <div class="sistema-pagamentos-integracao-topo">
                                                <strong><?= esc($integracao['nome']) ?></strong>
                                                <span class="badge badge-<?= esc($integracao['status_classe'] ?? 'secondary') ?>">
                                                    <?= esc($integracao['status_integracao'] ?? 'Inativa') ?>
                                                </span>
                                            </div>
                                            <p><?= esc($integracao['detalhes']['recursos'] ?? $integracao['descricao'] ?? '') ?></p>
                                            <div class="sistema-pagamentos-integracao-resumo">
                                                <span><small>Vinculos</small><strong><?= (int) ($integracao['formas_vinculadas'] ?? 0) ?></strong></span>
                                                <span><small>Serviços</small><strong><?= (int) ($integracao['formas_servicos'] ?? 0) ?></strong></span>
                                            </div>
                                            <div class="sistema-pagamentos-integracao-acoes">
                                                <a class="btn btn-outline-primary btn-sm" href="/desenvolvedor/edit/<?= (int) $integracao['id_integracao'] ?>">
                                                    <i class="fas fa-cog"></i> Configurar
                                                </a>
                                                <?php if (! empty($integracao['documentacao_url'])) : ?>
                                                    <a class="btn btn-outline-info btn-sm" href="<?= esc($integracao['documentacao_url']) ?>" target="_blank" rel="noopener noreferrer">
                                                        <i class="fas fa-external-link-alt"></i> Docs
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- /.card-body -->
                        <!-- <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-12" style="text-align: right">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <!-- /.card -->
                </div>
                
                <div class="col-lg-12">
                    <?php
                    $idioma_selecionado = old('idioma', $config_sistema['idioma'] ?? 'pt-BR');
                    $fuso_horario_selecionado = old('fuso_horario', $config_sistema['fuso_horario'] ?? 'America/Manaus');
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= esc(lang('App.system.globalization')) ?></h6>
                                </div><!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="/configs/store_sistema" method="post">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="idioma"><?= esc(lang('App.system.language')) ?></label>
                                        <select id="idioma" class="form-control select2" name="idioma" required>
                                            <?php foreach ($idiomas as $codigo => $nome) : ?>
                                                <option value="<?= esc($codigo) ?>" <?= $codigo === $idioma_selecionado ? 'selected' : '' ?>>
                                                    <?= esc($nome) ?> (<?= esc($codigo) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="fuso_horario"><?= esc(lang('App.system.timezone')) ?></label>
                                        <select id="fuso_horario" class="form-control select2" name="fuso_horario" required>
                                            <?php foreach ($fusos_horarios as $timezone => $rotulo) : ?>
                                                <option value="<?= esc($timezone) ?>" <?= $timezone === $fuso_horario_selecionado ? 'selected' : '' ?>>
                                                    <?= esc($rotulo) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-12 mt-3" style="text-align: right">
                                        <button class="btn btn-primary"><?= esc(lang('App.common.apply')) ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-5">
                                    <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= esc(lang('App.system.theme')) ?></h6>
                                </div><!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="/configs/alteraTema" method="post">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <div class="col-lg-10">
                                        <select class="form-control select2" name="tema">
                                            <?php
                                            $session = session();
                                            $tema = $session->get('tema');
                                            if($tema == 0):
                                            ?>
                                                <option value="0" selected><?= esc(lang('App.system.webSystem')) ?></option>
                                                <option value="1"><?= esc(lang('App.system.desktopSystem')) ?></option>
                                            <?php else: ?>
                                                <option value="0"><?= esc(lang('App.system.webSystem')) ?></option>
                                                <option value="1" selected><?= esc(lang('App.system.desktopSystem')) ?></option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2">
                                            <button class="btn btn-primary"><?= esc(lang('App.common.save')) ?></button>
                                        </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                        <!-- <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-12" style="text-align: right">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <!-- /.card -->        

                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= esc(lang('App.system.personalization')) ?></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="/configs/store_personalizacao" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="favicon"><?= esc(lang('App.system.favicon')) ?></label>
                                        <div class="mb-2">
                                            <img src="<?= esc(base_url($config_sistema['favicon'])) ?>" alt="<?= esc(lang('App.system.favicon')) ?>" style="max-height: 48px; max-width: 100%;">
                                        </div>
                                        <input id="favicon" class="form-control-file" type="file" name="favicon" accept=".ico,.png">
                                        <small class="form-text text-muted"><?= esc(lang('App.system.faviconHelp')) ?></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="logo_login"><?= esc(lang('App.system.loginLogo')) ?></label>
                                        <div class="mb-2">
                                            <img src="<?= esc(base_url($config_sistema['logo_login'])) ?>" alt="<?= esc(lang('App.system.loginLogo')) ?>" style="max-height: 80px; max-width: 100%;">
                                        </div>
                                        <input id="logo_login" class="form-control-file" type="file" name="logo_login" accept=".png,.jpg,.jpeg,.webp">
                                        <small class="form-text text-muted"><?= esc(lang('App.system.loginLogoHelp')) ?></small>
                                    </div>
                                    <div class="col-lg-12 mt-3" style="text-align: right">
                                        <button class="btn btn-primary"><?= esc(lang('App.common.save')) ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>

        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    $(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000
        });

        <?php
        $session = session();
        $alert = $session->getFlashdata('alert');

        if (isset($alert)) :
        ?>
            <?php if ($alert == "success_edit") : ?>
                Toast.fire({
                    type: 'success',
                    title: <?= json_encode(lang('App.alerts.companySaved')) ?>
                })
            <?php elseif ($alert == "success_config_sistema") : ?>
                Toast.fire({
                    type: 'success',
                    title: <?= json_encode(lang('App.alerts.globalSaved')) ?>
                })
            <?php elseif ($alert == "success_personalizacao") : ?>
                Toast.fire({
                    type: 'success',
                    title: <?= json_encode(lang('App.alerts.personalizationSaved')) ?>
                })
            <?php elseif ($alert == "error_personalizacao") : ?>
                Toast.fire({
                    type: 'error',
                    title: <?= json_encode(implode(' ', (array) $session->getFlashdata('errors'))) ?>
                })
            <?php elseif ($alert == "success_create_forma_de_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: <?= json_encode(lang('App.alerts.paymentCreated')) ?>
                })
            <?php elseif ($alert == "success_edit_forma_de_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: <?= json_encode(lang('App.alerts.paymentUpdated')) ?>
                })
            <?php elseif ($alert == "success_delete_forma_de_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: <?= json_encode(lang('App.alerts.paymentDeleted')) ?>
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
