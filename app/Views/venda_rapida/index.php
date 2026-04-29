<!-- Modal Altera Quantidade -->
<div class="modal fade" id="alterar-qtd-do-produto">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alterar QTD</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/vendaRapida/alteraQuantidade" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Nova QTD</label>
                                <input type="hidden" class="form-control" id="id_produto_da_venda_rapida" name="id_produto_da_venda_rapida">
                                <input type="text" class="form-control" name="quantidade">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Continuar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Modal Altera Desconto -->
<div class="modal fade" id="alterar-desconto-do-produto">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alterar Desconto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/vendaRapida/alteraDesconto" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Desconto</label>
                                <input type="hidden" class="form-control" id="id_produto_da_venda_rapida_desconto" name="id_produto_da_venda_rapida">
                                <input type="text" class="form-control" name="desconto">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Continuar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Modal Altera Valor Unitário -->
<div class="modal fade" id="alterar-valor-unitario-do-produto">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alterar Valor Unitário</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/vendaRapida/alteraValorUnitario" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Valor Unitário</label>
                                <input type="hidden" class="form-control" id="id_produto_da_venda_rapida_valor_unitario" name="id_produto_da_venda_rapida">
                                <input type="text" class="form-control" name="valor_unitario">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Continuar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row" style="margin-bottom: 15px">
                <div class="col-sm-6">
                    <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                </div><!-- /.col -->
                <div class="col-sm-6 no-print">
                    <ol class="breadcrumb float-sm-right">
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
            <div class="card">
                <div class="card-body">
                    <form action="/vendaRapida/addProdutoDaVenda" method="post">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label>Produto</label>
                                    <select class="form-control select2" name="id_produto" style="width: 100%;" required="">
                                        <?php if (!empty($produtos_do_estoque)) : ?>
                                            <?php foreach ($produtos_do_estoque as $produto) : ?>
                                                <option value="<?= $produto['id_produto'] ?>"><?= $produto['nome'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Cód. de Barras</label>
                                    <input type="text" class="form-control" name="codigo_de_barras" autofocus>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Qtd</label>
                                    <input type="text" class="form-control" name="quantidade" value="1" required="">
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success btn-block" style="margin-top: 31px"><i class="fas fa-plus-circle"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-lg-12">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Cód</th>
                                        <th>Nome</th>
                                        <th>Qtd</th>
                                        <th>Valor Unit.</th>
                                        <th>Subtotal</th>
                                        <th>Desconto</th>
                                        <th>Valor Final</th>
                                        <th style="width: 10px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($produtos_da_venda_rapida)) : ?>
                                        <?php foreach ($produtos_da_venda_rapida as $produto) : ?>
                                            <tr>
                                                <td><?= $produto['id_produto'] ?></td>
                                                <td><?= $produto['nome'] ?></td>
                                                <!-- <td><?= $produto['quantidade'] ?></td> -->
                                                <td>
                                                    <a href="#" data-toggle="modal" data-target="#alterar-qtd-do-produto" onclick="document.getElementById('id_produto_da_venda_rapida').value = <?= $produto['id_produto_da_venda_rapida'] ?>">
                                                        <?= $produto['quantidade'] ?>
                                                    </a>
                                                </td>
                                                <!-- <td><?= number_format($produto['valor_unitario'], 2, ',', '.') ?></td> -->
                                                <td>
                                                    <a href="#" data-toggle="modal" data-target="#alterar-valor-unitario-do-produto" onclick="document.getElementById('id_produto_da_venda_rapida_valor_unitario').value = <?= $produto['id_produto_da_venda_rapida'] ?>">
                                                        <?= number_format($produto['valor_unitario'], 2, ',', '.') ?>
                                                    </a>
                                                </td>
                                                <td><?= number_format($produto['subtotal'], 2, ',', '.') ?></td>
                                                <!-- <td><?= number_format($produto['desconto'], 2, ',', '.') ?></td> -->
                                                <td>
                                                    <a href="#" data-toggle="modal" data-target="#alterar-desconto-do-produto" onclick="document.getElementById('id_produto_da_venda_rapida_desconto').value = <?= $produto['id_produto_da_venda_rapida'] ?>">
                                                        <?= number_format($produto['desconto'], 2, ',', '.') ?>
                                                    </a>
                                                </td>
                                                <td><?= number_format($produto['valor_final'], 2, ',', '.') ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse produto da venda?', '/vendaRapida/deleteProduto/<?= $produto['id_produto_da_venda_rapida'] ?>')"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <form action="/vendaRapida/store" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Dados Finais</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Valor da Venda</label>
                                    <input type="text" class="form-control" value="<?= (!empty($valor_da_venda['valor_final'])) ? $valor_da_venda['valor_final'] : "0" ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Valor a Pagar</label>
                                    <input type="text" class="form-control" id="valor_a_pagar" name="valor_a_pagar" onkeyup="trocaVirguraPorPonto('valor_a_pagar')" value="<?= (!empty($valor_da_venda['valor_final'])) ? $valor_da_venda['valor_final'] : "0" ?>" required="">
                                </div>
                            </div>
                            <!-- <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Desconto</label>
                                    <input type="text" class="form-control" id="desconto" name="desconto" onkeyup="trocaVirguraPorPonto('desconto')" value="0" required="">
                                </div>
                            </div> -->
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Valor Recebido</label>
                                    <input type="text" class="form-control" id="valor_recebido" name="valor_recebido" onkeyup="trocaVirguraPorPonto('valor_recebido')" value="<?= (!empty($valor_da_venda['valor_final'])) ? $valor_da_venda['valor_final'] : "0" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Troco</label>
                                    <input type="text" class="form-control" id="troco" name="troco" onkeyup="trocaVirguraPorPonto('troco')" value="0" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <select class="form-control select2" name="tipo" style="width: 100%;" required="">
                                        <option value="">-- Selecione o tipo --</option>
                                        <option value="Venda">Venda</option>
                                        <option value="Orçamento">Orçamento</option>
                                        <option value="Pedido">Pedido</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Forma de PGTO</label>
                                    <select class="form-control select2" name="forma_de_pagamento" style="width: 100%;" required="">
                                        <?php foreach ($formas_de_pagamento as $forma) : ?>
                                            <option value="<?= $forma['nome'] ?>"><?= $forma['nome'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select class="form-control select2" name="id_cliente" style="width: 100%;" required="">
                                        <?php if (!empty($clientes)) : ?>
                                            <?php foreach ($clientes as $cliente) : ?>
                                                <?php if ($cliente['tipo'] == 1) : ?>
                                                    <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['nome'] ?></option>
                                                <?php else : ?>
                                                    <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['razao_social'] ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Vendedor</label>
                                    <select class="form-control select2" name="id_vendedor" style="width: 100%;" required="">
                                        <?php if (!empty($vendedores)) : ?>
                                            <?php foreach ($vendedores as $vendedor) : ?>
                                                <option value="<?= $vendedor['id_vendedor'] ?>"><?= $vendedor['nome'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Caixa</label>
                                    <select class="form-control select2" name="id_caixa" style="width: 100%;" required="">
                                        <?php if (!empty($caixas)) : ?>
                                            <?php foreach ($caixas as $caixa) : ?>
                                                <option value="<?= $caixa['id_caixa'] ?>">Data Abert.: <?= $caixa['data_de_abertura'] ?> - Hora Abert.: <?= $caixa['hora_de_abertura'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <?= (empty($caixas)) ? "<p style='color: orange; text-align: right; font-size: 11px; font-weight: bold'>Abra um caixa para continuar!</p>" : "" ?>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Data</label>
                                    <input type="date" class="form-control" name="data" value="<?= date('Y-m-d') ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Hora</label>
                                    <input type="time" class="form-control" name="hora" value="<?= date('H:i:s') ?>" required="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary" <?= (empty($valor_da_venda['valor_final']) || empty($caixas)) ? "disabled" : "" ?>>Finalizar</button>
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
            <?php if ($alert == "success_venda") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Venda realizada com sucesso!'
                })
            <?php elseif ($alert == "success_pedido") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Pedido cadastrado com sucesso!'
                })
            <?php elseif ($alert == "success_orcamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Orçamento cadastrado com sucesso!'
                })
            <?php elseif ($alert == "success_add_produto") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Produto adicionado com sucesso!'
                })
            <?php elseif ($alert == "success_delete_produto") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Produto da Venda Excluido com sucesso!'
                })
            <?php elseif ($alert == "success_update_qtd_produto") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Qtd alterada com sucesso!'
                })
            <?php elseif ($alert == "success_update_desconto_produto") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Desconto adicionado com sucesso!'
                })
            <?php elseif ($alert == "success_update_valor_unitario_produto") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Valor Unitário alterado com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>