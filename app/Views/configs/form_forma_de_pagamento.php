<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/configs/store_forma_de_pagamento" method="post">
                <?= csrf_field() ?>
                <?php
                $forma_atual = $forma_de_pagamento ?? [];
                $nome_atual = old('nome', $forma_atual['nome'] ?? '');
                $id_integracao_atual = old('id_integracao', $forma_atual['id_integracao'] ?? '');
                ?>
                <div class="card sistema-pagamento-form-card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <a href="/configs/sistema" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
                                    <?php foreach ($caminhos as $caminho) : ?>
                                        <?php if (!$caminho['active']) : ?>
                                            <li class="breadcrumb-item"><a href="<?= $caminho['rota'] ?>"><?= $caminho['titulo'] ?></a></li>
                                        <?php else : ?>
                                            <li class="breadcrumb-item active"><?= $caminho['titulo'] ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ol>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="nome">Nome da forma</label>
                                    <input id="nome" type="text" class="form-control" name="nome" value="<?= esc($nome_atual) ?>" placeholder="Ex.: PIX, Cartao de credito, Boleto" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="id_integracao">Integracao de pagamento</label>
                                    <select id="id_integracao" class="form-control select2" name="id_integracao" style="width: 100%;">
                                        <option value="">Manual / sem API</option>
                                        <?php foreach ($integracoes_pagamento as $integracao) : ?>
                                            <option
                                                value="<?= $integracao['id_integracao'] ?>"
                                                <?= (string) $id_integracao_atual === (string) $integracao['id_integracao'] ? 'selected' : '' ?>
                                            >
                                                <?= esc($integracao['nome']) ?> - <?= esc(ucfirst((string) $integracao['ambiente'])) ?> - <?= esc($integracao['status_integracao'] ?? 'Inativa') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Dinheiro e recebimentos simples podem ficar manuais. PIX, cartao, boleto e carteira digital podem usar um provedor quando houver credencial validada.</small>
                                </div>
                                <?php if (! empty($integracoes_pagamento)) : ?>
                                    <div class="sistema-pagamento-form-integracoes">
                                        <?php foreach ($integracoes_pagamento as $integracao) : ?>
                                            <span class="badge badge-<?= esc($integracao['status_classe'] ?? 'secondary') ?>">
                                                <?= esc($integracao['nome']) ?>: <?= esc($integracao['status_integracao'] ?? 'Inativa') ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-12">
                                <div class="alert alert-light mb-0">
                                    <i class="fas fa-info-circle"></i>
                                    Esta forma ficará disponível para orçamentos, vendas e ordens de serviço.
                                </div>
                            </div>

                            <?php if (isset($forma_de_pagamento)) : ?>
                                <input type="hidden" class="form-control" name="id_forma" value="<?= $forma_de_pagamento['id_forma'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary"><?= (isset($forma_de_pagamento)) ? "Atualizar" : "Cadastrar" ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </form>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
