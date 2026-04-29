<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/caixas/store" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <?php if (isset($caixa)) : ?>
                                        <a href="/caixas/show/<?= $caixa['id_caixa'] ?>" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
                                    <?php else : ?>
                                        <a href="/caixas" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
                                    <?php endif; ?>
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
                            <?php if (isset($caixa)) : ?>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Status</label>
                                        <input type="text" class="form-control" name="status" value="<?= $caixa['status'] ?>" required="">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Data de Abertura</label>
                                    <input type="date" class="form-control" name="data_de_abertura" value="<?= date('Y-m-d') ?>" required="">
                                </div>
                            </div>

                            <?php if (isset($caixa)) : ?>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Data de Fechamento</label>
                                        <input type="date" class="form-control" name="data_de_fechamento" value="<?= $caixa['data_de_fechamento'] ?>" required="">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Hora de Abertura</label>
                                    <input type="text" class="form-control" name="hora_de_abertura" value="<?= date('H:i:s') ?>" required="">
                                </div>
                            </div>

                            <?php if (isset($caixa)) : ?>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Hora de Fechamento</label>
                                        <input type="text" class="form-control" name="hora_de_fechamento" value="<?= $caixa['hora_de_fechamento'] ?>" required="">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Valor Inicial</label>
                                    <input type="text" class="form-control" id="valor_inicial" name="valor_inicial" onkeyup="trocaVirguraPorPonto('valor_inicial')" value="<?= (isset($caixa)) ? $caixa['valor_inicial'] : "" ?>" required="">
                                </div>
                            </div>

                            <?php if (isset($caixa)) : ?>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Valor Total</label>
                                        <input type="text" class="form-control" id="valor_total" name="valor_total" onkeyup="trocaVirguraPorPonto('valor_total')" value="<?= $caixa['valor_inicial'] ?>" required="">
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Valor de Fechamento</label>
                                        <input type="text" class="form-control" id="valor_total" name="valor_de_fechamento" onkeyup="trocaVirguraPorPonto('valor_de_fechamento')" value="<?= $caixa['valor_de_fechamento'] ?>" required="">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">Observações</label>
                                    <input type="text" class="form-control" name="observacoes" value="<?= (isset($caixa)) ? $caixa['observacoes'] : "" ?>">
                                </div>
                            </div>

                            <?php if (isset($caixa)) : ?>
                                <input type="hidden" class="form-control" name="id_caixa" value="<?= $caixa['id_caixa'] ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary"><?= (isset($caixa)) ? "Atualizar" : "Cadastrar" ?></button>
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