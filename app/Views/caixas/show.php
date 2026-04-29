<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if ($caixa['status'] == "Aberto") : ?>
                                <button type="button" class="btn btn-primary" onclick="verificaValorDeFechamento()"><i class="fa fa-plus-circle"></i> Fechar Caixa</button>
                                <a href="/caixas/edit/<?= $caixa['id_caixa'] ?>" class="btn btn-warning"><i class="fa fa-edit"></i> Editar</a>
                                <button type="button" class="btn btn-danger" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse caixa? Essa ação não poderá ser desfeita!', '/caixas/delete/<?= $caixa['id_caixa'] ?>')"><i class="fa fa-trash"></i> Excluir</button>
                            <?php else : ?>
                                <a href="/caixas/reabrir/<?= $caixa['id_caixa'] ?>" class="btn btn-primary">Reabrir Caixa</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
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
                                <a href="/caixas" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                    <form id="form-fechar-caixa" action="/caixas/fechar/<?= $caixa['id_caixa'] ?>" method="post">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <input type="text" class="form-control" value="<?= $caixa['status'] ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Data de Abertura</label>
                                    <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($caixa['data_de_abertura'])) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Data de Fechamento</label>
                                    <input type="text" class="form-control" value="<?= ($caixa['data_de_fechamento'] == "0000-00-00") ? "Não definida!" : date('d/m/Y', strtotime($caixa['data_de_fechamento'])) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Hora de Abertura</label>
                                    <input type="text" class="form-control" value="<?= $caixa['hora_de_abertura'] ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Hora de Fechamento</label>
                                    <input type="text" class="form-control" value="<?= ($caixa['hora_de_fechamento'] == "00:00:00") ? "Não definida!" : $caixa['hora_de_fechamento'] ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Valor Inicial</label>
                                    <input type="text" class="form-control" value="<?= number_format($caixa['valor_inicial'], 2, ',', '.') ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Valor Total</label>
                                    <input type="text" class="form-control" value="<?= number_format($somatorio, 2, ',', '.') ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Valor de Fechamento</label>
                                    <input type="text" id="valor_de_fechamento" class="form-control" id="valor_de_fechamento" name="valor_de_fechamento" onkeyup="trocaVirguraPorPonto('valor_de_fechamento')" value="<?= $caixa['valor_de_fechamento'] ?>" <?= ($caixa['status'] == "Fechado") ? "disabled" : "" ?>>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">Observações</label>
                                    <input type="text" id="observacoes" class="form-control" name="observacoes" value="<?= $caixa['observacoes'] ?>" <?= ($caixa['status'] == "Fechado") ? "disabled" : "" ?>>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Lançamentos</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1-2" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $qtd_de_lancamentos = 0;
                                $valor_total_de_lancamentos = 0;
                            ?>
                            <?php if (!empty($lancamentos)) : ?>
                                <?php foreach ($lancamentos as $lancamento) : ?>
                                    <tr>
                                        <td><?= $lancamento['id_lancamento'] ?></td>
                                        <td><?= $lancamento['descricao'] ?></td>
                                        <td><?= number_format($lancamento['valor'], 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($lancamento['data'])) ?></td>
                                        <td><?= $lancamento['hora'] ?></td>
                                        <td><?= $lancamento['observacoes'] ?></td>
                                    </tr>
                                <?php
                                    $qtd_de_lancamentos += 1;
                                    $valor_total_de_lancamentos += $lancamento['valor'];
                                ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <br>
                    <br>
                    <p>
                        <b>Qtd. de Lançamentos:</b> <?= $qtd_de_lancamentos ?> <br>
                        <b>Valor Total de Lançamentos:</b> R$ <?= number_format($valor_total_de_lancamentos, 2, ',', '.') ?>
                    </p>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Vendas</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1-3" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th>Valor</th>
                                <th>Desconto</th>
                                <th>Recebido</th>
                                <th>Troco</th>
                                <th>PGTO</th>
                                <th>Data</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $qtd_de_vendas = 0;
                                $valor_total_de_vendas = 0;
                            ?>
                            <?php if (!empty($vendas)) : ?>
                                <?php foreach ($vendas as $venda) : ?>
                                    <tr>
                                        <td><?= $venda['id_venda'] ?></td>
                                        <td><?= number_format($venda['valor_a_pagar'], 2, ',', '.') ?></td>
                                        <td><?= $venda['desconto'] ?></td>
                                        <td><?= number_format($venda['valor_recebido'], 2, ',', '.') ?></td>
                                        <td><?= number_format($venda['troco'], 2, ',', '.') ?></td>
                                        <td><?= $venda['forma_de_pagamento'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($venda['data'])) ?></td>
                                        <td><?= $venda['hora'] ?></td>
                                    </tr>
                                    <?php
                                        $qtd_de_vendas += 1;
                                        $valor_total_de_vendas += $venda['valor_a_pagar'];
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <br>
                    <br>
                    <p>
                        <b>Qtd. de Vendas:</b> <?= $qtd_de_vendas ?> <br>
                        <b>Valor Total de Vendas:</b> R$ <?= number_format($valor_total_de_vendas, 2, ',', '.') ?>
                    </p>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Retiradas</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1-4" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Obs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $qtd_de_retiradas = 0;
                                $valor_total_de_retiradas = 0;
                            ?>
                            <?php if (!empty($retiradas)) : ?>
                                <?php foreach ($retiradas as $retirada) : ?>
                                    <tr>
                                        <td><?= $retirada['id_retirada'] ?></td>
                                        <td><?= $retirada['tipo'] ?></td>
                                        <td><?= $retirada['descricao'] ?></td>
                                        <td><?= number_format($retirada['valor'], 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($retirada['data'])) ?></td>
                                        <td><?= $retirada['hora'] ?></td>
                                        <td><?= $retirada['observacoes'] ?></td>
                                    </tr>
                                    <?php
                                        $qtd_de_retiradas += 1;
                                        $valor_total_de_retiradas += $retirada['valor'];
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <br>
                    <br>
                    <p>
                        <b>Qtd. de Retiradas:</b> <?= $qtd_de_retiradas ?> <br>
                        <b>Valor Total de Retiradas:</b> R$ <?= number_format($valor_total_de_retiradas, 2, ',', '.') ?>
                    </p>
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
            <?php if ($alert == "success_fechar") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Caixa fechado com sucesso!'
                })
            <?php elseif ($alert == "success_reabrir") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Caixa reaberto com sucesso!'
                })
            <?php elseif ($alert == "success_edit") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Caixa editado com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });

    function verificaValorDeFechamento() {
        var valor_de_fechamento = document.getElementById('valor_de_fechamento').value;

        if (valor_de_fechamento != "" && valor_de_fechamento != 0) {
            document.getElementById('form-fechar-caixa').submit();
        } else {
            alert('Informe o valor de fechamento para continuar!');
        }
    }
</script>