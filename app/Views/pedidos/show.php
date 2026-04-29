<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card no-print">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" onclick="print()"><i class="fas fa-print"></i> Imprimir Pedido</button>
                    <?php if($pedido['situacao'] == "Pago - Finalizado"): ?>
                        <button type="button" class="btn btn-success" disabled><i class="fas fa-print"></i> Finalizar Pedido</button>
                    <?php else: ?>
                        <a href="/pedidos/finalizarPedido/<?= $pedido['id_pedido'] ?>" class="btn btn-success"><i class="fas fa-print"></i> Finalizar Pedido</a>
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
                                <a href="/pedidos" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                                <label for="">Cód. do Pedido</label>
                                <input type="text" class="form-control" value="<?= $pedido['id_pedido'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Situação</label>
                                <input type="text" class="form-control" value="<?= $pedido['situacao'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Valor da Venda</label>
                                <input type="text" class="form-control" value="<?= number_format($pedido['valor_a_pagar'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Desconto</label>
                                <input type="text" class="form-control" value="<?= number_format($pedido['desconto'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Valor Recebido</label>
                                <input type="text" class="form-control" value="<?= number_format($pedido['valor_recebido'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Troco</label>
                                <input type="text" class="form-control" value="<?= number_format($pedido['troco'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Forma de Pagamento</label>
                                <input type="text" class="form-control" value="<?= $pedido['forma_de_pagamento'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Data</label>
                                <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($pedido['data'])) ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Hora</label>
                                <input type="text" class="form-control" value="<?= $pedido['hora'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Situação</label>
                                <input type="text" class="form-control" value="<?= $pedido['situacao'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Prazo de Entrega</label>
                                <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($pedido['prazo_de_entrega'])) ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Cód. do Cliente</label>
                                <input type="text" class="form-control" value="<?= $pedido['id_cliente'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Cód. do Vendedor</label>
                                <input type="text" class="form-control" value="<?= $pedido['id_vendedor'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Cód. do Caixa</label>
                                <input type="text" class="form-control" value="<?= $pedido['id_caixa'] ?>" disabled="">
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
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Produtos do Pedido</h6>
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
                                    <?php foreach ($produtos_do_pedido as $produto) : ?>
                                        <tr>
                                            <td><?= $produto['id_produto'] ?></td>
                                            <td><?= $produto['nome'] ?></td>
                                            <td><?= $produto['unidade'] ?></td>
                                            <td><?= $produto['codigo_de_barras'] ?></td>
                                            <td><?= $produto['quantidade'] ?></td>
                                            <td><?= number_format($produto['valor_unitario'], 2, ',', '.') ?></td>
                                            <td><?= number_format($produto['subtotal'], 2, ',', '.') ?></td>
                                            <td><?= number_format($produto['desconto'], 2, ',', '.') ?></td>
                                            <td><?= number_format($produto['valor_final'], 2, ',', '.') ?></td>
                                            <td><?= $produto['NCM'] ?></td>
                                            <td><?= $produto['CSOSN'] ?></td>
                                            <td><?= $produto['CFOP'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->