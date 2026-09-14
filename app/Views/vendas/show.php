<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper no-print">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="alert alert-secondary no-print" role="status">
                Venda legada disponível somente para consulta histórica.
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <a href="/vendas" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Cód. da Venda</label>
                                <input type="text" class="form-control" value="<?= $venda['id_venda'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Valor da Venda</label>
                                <input type="text" class="form-control" id="valor_da_venda" value="" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Valor a Pagar</label>
                                <input type="text" class="form-control" value="<?= number_format($venda['valor_a_pagar'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <!-- <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Desconto</label>
                                <input type="text" class="form-control" value="<?= decimal_monetario($venda['desconto']) ?>" disabled="">
                            </div>
                        </div> -->
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Valor Recebido</label>
                                <input type="text" class="form-control" value="<?= number_format($venda['valor_recebido'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Troco</label>
                                <input type="text" class="form-control" value="<?= number_format($venda['troco'], 2, ',', '.') ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Forma de Pagamento</label>
                                <input type="text" class="form-control" value="<?= $venda['forma_de_pagamento'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Data</label>
                                <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($venda['data'])) ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Hora</label>
                                <input type="text" class="form-control" value="<?= $venda['hora'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Cliente</label>
                                <input type="text" class="form-control" value="<?= $venda['nome_do_cliente'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Vendedor</label>
                                <input type="text" class="form-control" value="<?= $venda['nome_do_vendedor'] ?>" disabled="">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Cód. do Caixa</label>
                                <input type="text" class="form-control" value="<?= $venda['id_caixa'] ?>" disabled="">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <!-- <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12" style="text-align: right">
                            <button type="submit" class="btn btn-primary"><?= (isset($despesa)) ? "Atualizar" : "Cadastrar" ?></button>
                        </div>
                    </div>
                </div> -->
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Produtos da Venda</h6>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="example1" class="table table-bordered table-striped">
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
                                    <?php $valor_calculado_da_venda = 0 ?>

                                    <?php foreach ($produtos_da_venda as $produto) : ?>
                                        <tr>
                                            <td><?= $produto['id_produto_da_venda'] ?></td>
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

                                        <?php $valor_calculado_da_venda += $produto['valor_final'] ?>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <input type="hidden" id="valor_calculado_da_venda" value="<?= $valor_calculado_da_venda ?>">

                </div>
                <!-- /.card-body -->
                <!-- <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12" style="text-align: right">
                            <button type="submit" class="btn btn-primary"><?= (isset($despesa)) ? "Atualizar" : "Cadastrar" ?></button>
                        </div>
                    </div>
                </div> -->
            </div>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    var campoValorVenda = document.getElementById('valor_da_venda');
    var campoValorCalculado = document.getElementById('valor_calculado_da_venda');

    if (campoValorVenda && campoValorCalculado) {
        campoValorVenda.value = campoValorCalculado.value;
    }
</script>
