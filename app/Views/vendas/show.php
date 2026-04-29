<!-- Modal CUPOM NÃO FISCAL -->
<div class="modal fade" id="modal-cupom-nao-fiscal">
    <div class="modal-dialog modal-md" style="width: 300px"> <!-- 300px = 80mm -->
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title">Cupom não fiscal <button type="button" class="btn btn-success style-action" onclick="print()"><i class="fas fa-print"></i></button></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="cupom-nao-fiscal">
                    <p style='text-align: center'>
                        <b><?= $empresa['nome_fantasia'] ?></b><br>
                        <?= $empresa['razao_social'] ?><br>
                        <?= $empresa['endereco'] ?><br>
                        <?= $empresa['telefone'] ?>
                    </p>

                    <p>
                        <b>CNPJ:</b> <?= $empresa['cnpj'] ?><br>
                        <b>Cliente:</b> <?= $venda['nome_do_cliente'] ?><br>
                        <?= $venda['data'] ?> às <?= $venda['hora'] ?> - <b>Nº <?= $venda['id_venda'] ?></b>
                    </p>

                    <hr>

                    <table width='100%'>
                        <thead>
                            <tr>
                                <th>Cód.</th>
                                <th>Desc.</th>
                                <th>Qtd X Unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($produtos_da_venda as $produto): ?>
                                <tr>
                                    <td><?= $produto['id_produto'] ?></td>
                                    <td><?= $produto['nome'] ?></td>
                                    <td><?= $produto['quantidade'] ?> x <?= $produto['valor_unitario'] ?></td>
                                    <td><?= $produto['valor_final'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>

                    <hr>

                    <p>
                        <b>Total:</b> <?= $venda['valor_a_pagar'] ?><br>
                        <b>Recebido:</b> <?= $venda['valor_recebido'] ?><br>
                        <b>Troco:</b> <?= $venda['troco'] ?><br>
                        <b>Forma de PGTO:</b> <?= $venda['forma_de_pagamento'] ?>
                    </p>
                    
                    <hr>

                    <p><b>Vendedor:</b> <?= $venda['nome_do_vendedor'] ?></p>

                    <hr>

                    <p style='text-align: center'>
                        ____________________________
                        <br>
                        Assinatura do Cliente
                    </p>
                </div>
            </div>
            <div class="modal-footer justify-content-between no-print">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-success" onclick="print()"><i class="fas fa-print"></i> Imprimir Cupom</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Modal CANCELAMENTO -->
<div class="modal fade" id="modal-justificativa-para-cancelamento-da-nfe">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title">Cancelar NFe</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           <form action="/NFe/cancelar" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Justificativa</label>
                                <textarea class="form-control" name="justificativa" rows="10" required=""></textarea>
                            </div>
                        </div>

                        <!-- HIDDENS -->
                        <input type="hidden" id="cancelar_id_nfe" name="id_nfe">
                        <input type="hidden" id="cancelar_id_venda" name="id_venda">
                    </div>
                </div>
                <div class="modal-footer justify-content-between no-print">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Continuar</button>
                </div>
           </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper no-print">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
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
                </div>
                <!-- /.card-header -->
                <div class="card-body no-print">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if (empty($nfe_da_venda)) : ?>
                                <button type="button" id="btnEmitirNFe" class="btn btn-primary" onclick="emiteNFe()"><i class="fa fa-plus-circle"></i> Emitir NFe</button>
                            <?php else : ?>
                                <?php if ($nfe_da_venda['status'] == "Emitida") : ?>
                                    <a href="/ImprimeDanfe/index/<?= $nfe_da_venda['id_nfe'] ?>/1" class="btn btn-success" target="_blank">Imprimir NFe</a>
                                    <button type="button" class="btn btn-warning" onclick="document.getElementById('cancelar_id_nfe').value=<?= $nfe_da_venda['id_nfe'] ?>, document.getElementById('cancelar_id_venda').value=<?= $venda['id_venda'] ?>" data-toggle="modal" data-target="#modal-justificativa-para-cancelamento-da-nfe">Cancelar NFe</button>
                                <?php elseif($nfe_da_venda['status'] == "Cancelada") : ?>
                                    <button type="button" class="btn btn-default" disabled>NFe Cancelada!</button>
                                <?php else : ?>
                                    <button type="button" class="btn btn-default" disabled>A NFe não pode ser emitida!</button>
                                    <a href="/NFe/reemitir/<?= $venda['id_cliente'] ?>/<?= $venda['id_venda'] ?>/<?= $nfe_da_venda['id_nfe'] ?>" class="btn btn-primary">Emitir</a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (empty($nfce_da_venda)) : ?>
                                <a href="/pdv/emiteNFCe/<?= $venda['id_venda'] ?>/2" class="btn btn-primary">Emitir NFCe</a>
                            <?php else : ?>
                                <?php if ($nfce_da_venda['status'] == "Emitida") : ?>
                                    <a href="/ImprimeDanfe/index/<?= $nfce_da_venda['id_nfce'] ?>/2" class="btn btn-success" target="_blank">Imprimir Cupom Fiscal - NFCe</a>
                                <?php else : ?>
                                    <button type="button" class="btn btn-default" disabled>A NFCe não pode ser emitida!</button>
                                    <a href="/Pdv/emiteNFCe/<?= $venda['id_venda'] ?>/2" class="btn btn-primary">Emitir</a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-cupom-nao-fiscal" onclick="preparaImpressao()"><i class="fas fa-print"></i> Cupom NÃO Fiscal</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

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
                                <input type="text" class="form-control" value="<?= $venda['desconto'] ?>" disabled="">
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
    function emiteNFe() {
        document.getElementById('btnEmitirNFe').disabled = true;
        document.getElementById('btnEmitirNFe').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Aguarde..';

        window.location.href = "/NFe/emiteNFe/<?= $venda['id_cliente'] ?>/<?= $venda['id_venda'] ?>";
    }

    function emiteNFCe() {
        document.getElementById('btnEmitirNFCe').disabled = true;
        document.getElementById('btnEmitirNFCe').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Aguarde..';

        window.location.href = "/Pdv/emiteNFCe/<?= $venda['id_venda'] ?>/<?= $venda['valor_a_pagar'] ?>/<?= $venda['troco'] ?>/2";
    }

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
            <?php if ($alert == "error_sem_conexao_com_a_internet") : ?>
                Toast.fire({
                    type: 'error',
                    title: 'Não possui conexão com a internet para continuar!'
                })
            <?php elseif ($alert == "success_emissao_nfe") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'NFe emitida com sucesso!'
                })
            <?php elseif ($alert == "erro_emissao_nfe") : ?>
                Toast.fire({
                    type: 'error',
                    title: 'Não foi possivel emitir NFe!'
                })
            <?php elseif ($alert == "success_cancelamento_nfe") : ?>
                Toast.fire({
                    type: 'error',
                    title: 'NFe cancelada com sucesso!'
                })
            <?php elseif ($alert == "erro_cancelamento_nfe") : ?>
                Toast.fire({
                    type: 'error',
                    title: 'Não foi possível cancelar NFe! Tente mais tarde..'
                })
            <?php endif; ?>
        <?php endif; ?>
    });

    function preparaImpressao()
    {
        document.getElementById('footer').className += " no-print";
    }

    document.getElementById('valor_da_venda').value = document.getElementById('valor_calculado_da_venda').value;
</script>