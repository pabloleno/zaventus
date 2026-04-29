<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card no-print">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" onclick="print()"><i class="fas fa-print"></i> Imprimir Orçamento</button>
                    <?php if($orcamento['status'] == "Finalizado"): ?>
                        <button type="button" class="btn btn-success" disabled><i class="fas fa-print"></i> Finalizar Venda</button>
                    <?php else: ?>
                        <a href="/orcamentos/finalizarVenda/<?= $orcamento['id_orcamento'] ?>" class="btn btn-success"><i class="fas fa-print"></i> Finalizar Venda</a>
                    <?php endif;?>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                        </div><!-- /.col -->
                        <div class="col-sm-6 no-print">
                            <ol class="breadcrumb float-sm-right">
                                <a href="/orcamentos" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Cód. do Orçamento</label>
                                <input type="text" class="form-control" value="<?= $orcamento['id_orcamento'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Status</label>
                                <input type="text" class="form-control" value="<?= $orcamento['status'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Valor da Venda</label>
                                <input type="text" class="form-control" value="<?= number_format($orcamento['valor_a_pagar'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Desconto</label>
                                <input type="text" class="form-control" value="<?= number_format($orcamento['desconto'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Valor Recebido</label>
                                <input type="text" class="form-control" value="<?= number_format($orcamento['valor_recebido'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Troco</label>
                                <input type="text" class="form-control" value="<?= number_format($orcamento['troco'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Forma de Pagamento</label>
                                <input type="text" class="form-control" value="<?= $orcamento['forma_de_pagamento'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Data</label>
                                <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($orcamento['data'])) ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Hora</label>
                                <input type="text" class="form-control" value="<?= $orcamento['hora'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Cód. do Cliente</label>
                                <input type="text" class="form-control" value="<?= $orcamento['id_cliente'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Cód. do Vendedor</label>
                                <input type="text" class="form-control" value="<?= $orcamento['id_vendedor'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Cód. do Caixa</label>
                                <input type="text" class="form-control" value="<?= $orcamento['id_caixa'] ?>" disabled="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Produtos do Orçamento</h6>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Cód.</th>
                                        <th>Nome</th>
                                        <th>UN</th>
                                        <th>Cod. Barras</th>
                                        <th>Qtd</th>
                                        <th>Valor Unit.</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Valor Final</th>
                                        <th>NCM</th>
                                        <th>CSOSN</th>
                                        <th>CFOP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($produtos_do_orcamento as $orcamento) : ?>
                                        <tr>
                                            <td><?= $orcamento['id_orcamento'] ?></td>
                                            <td><?= $orcamento['nome'] ?></td>
                                            <td><?= $orcamento['unidade'] ?></td>
                                            <td><?= $orcamento['codigo_de_barras'] ?></td>
                                            <td><?= $orcamento['quantidade'] ?></td>
                                            <td><?= number_format($orcamento['valor_unitario'], 2, ',', '.') ?></td>
                                            <td><?= number_format($orcamento['subtotal'], 2, ',', '.') ?></td>
                                            <td><?= number_format($orcamento['desconto'], 2, ',', '.') ?></td>
                                            <td><?= number_format($orcamento['valor_final'], 2, ',', '.') ?></td>
                                            <td><?= $orcamento['NCM'] ?></td>
                                            <td><?= $orcamento['CSOSN'] ?></td>
                                            <td><?= $orcamento['CFOP'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Detalhes</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h6><b>Data:</b> <?= date('d/m/Y') ?></h6>
                            <h6><b>Hora:</b> <?= date('H:i:s') ?></h6>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
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
            <?php if ($alert == "success_finaliza_venda") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Orçamento finalizado com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>