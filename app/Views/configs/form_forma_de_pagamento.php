<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/configs/store_forma_de_pagamento" method="post">
                <?= csrf_field() ?>
                <div class="card">
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
                                    <label for="">Nome</label>
                                    <input type="text" class="form-control" name="nome" value="<?= (isset($forma_de_pagamento)) ? $forma_de_pagamento['nome'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Codigo fiscal/NFCe (tPag)</label>
                                    <input type="text" class="form-control" name="codigo_nfce" maxlength="2" value="<?= (isset($forma_de_pagamento)) ? ($forma_de_pagamento['codigo_nfce'] ?? "99") : "99" ?>" required="">
                                    <small class="form-text text-muted">Ex.: 01 dinheiro, 03 crédito, 04 débito, 17 PIX dinâmico, 20 PIX estático.</small>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label>Provedor/API vinculado</label>
                                    <select class="form-control select2" name="id_integracao" style="width: 100%;">
                                        <option value="">Manual / sem API</option>
                                        <?php foreach ($integracoes_pagamento as $integracao) : ?>
                                            <option
                                                value="<?= $integracao['id_integracao'] ?>"
                                                <?= (string) old('id_integracao', $forma_de_pagamento['id_integracao'] ?? '') === (string) $integracao['id_integracao'] ? 'selected' : '' ?>
                                            >
                                                <?= esc($integracao['nome']) ?> - <?= esc($integracao['ambiente']) ?> - <?= (int) $integracao['ativo'] === 1 && ($integracao['ultimo_teste_status'] ?? '') === 'sucesso' ? 'autenticação validada/ativa' : 'inativa ou pendente' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">O vínculo identifica o provedor. A venda continua manual enquanto não existir um conector transacional homologado.</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        class="custom-control-input"
                                        id="disponivel-produtos"
                                        type="checkbox"
                                        name="disponivel_produtos"
                                        value="1"
                                        <?= (int) old('disponivel_produtos', $forma_de_pagamento['disponivel_produtos'] ?? 1) === 1 ? 'checked' : '' ?>
                                    >
                                    <label class="custom-control-label" for="disponivel-produtos">Disponivel em vendas de produtos e PDV</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        class="custom-control-input"
                                        id="disponivel-servicos"
                                        type="checkbox"
                                        name="disponivel_servicos"
                                        value="1"
                                        <?= (int) old('disponivel_servicos', $forma_de_pagamento['disponivel_servicos'] ?? 1) === 1 ? 'checked' : '' ?>
                                    >
                                    <label class="custom-control-label" for="disponivel-servicos">Disponivel em ordens de servicos</label>
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
