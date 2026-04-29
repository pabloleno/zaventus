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
                <!-- <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Dados da OS</h6>
                        </div>
                    </div>
                </div> -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                            <button class="btn btn-primary" onclick="print()"><i class="fas fa-print"></i> Imprimir/Salvar PDF</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Dados da OS</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                            <div class="form-group">
                                <label for="">Cliente</label>
                                <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['nome_do_cliente'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <label for="">Situação</label>
                                <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['situacao'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <label for="">Dt Entrada</label>
                                <input type="date" class="form-control" value="<?= $dados_ordem_de_servico['data_de_entrada'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <label for="">Hr Entrada</label>
                                <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['hora_de_entrada'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <label for="">Dt Saída</label>
                                <input type="date" class="form-control" value="<?= $dados_ordem_de_servico['data_de_saida'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <label for="">Hr Saída</label>
                                <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['hora_de_saida'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="form-group">
                                <label for="">Canal de venda</label>
                                <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['canal_de_venda'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="form-group">
                                <label for="">Centro de custo</label>
                                <input type="text" class="form-control" name="centro_de_custo" value="<?= $dados_ordem_de_servico['centro_de_custo'] ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label for="">Observações</label>
                                <textarea class="form-control" rows="5" disabled><?= $dados_ordem_de_servico['observacoes'] ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label for="">Observações internas</label>
                                <textarea class="form-control" rows="5" disabled><?= $dados_ordem_de_servico['observacoes_internas'] ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Responsáveis</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="form-group">
                                <label for="">Vendedor</label>
                                <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['nome_do_vendedor'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="form-group">
                                <label for="">Técnico</label>
                                <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['nome_do_tecnico'] ?>" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Equipamentos</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="10" style="text-align: center">EQUIPAMENTOS DA OS</th>
                                    </tr>
                                    <tr>
                                        <th>Equipamento</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Série</th>
                                        <th>Condições</th>
                                        <th>Defeitos</th>
                                        <th>Acessórios</th>
                                        <th>Solução</th>
                                        <th>Laudo Técnico</th>
                                        <th>Termos de Garantia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($equipamentos_os_provisorio)): ?>
                                        <?php foreach($equipamentos_os_provisorio as $equipamento): ?>
                                            <tr>
                                                <td><?= $equipamento['equipamento'] ?></td>
                                                <td><?= $equipamento['marca'] ?></td>
                                                <td><?= $equipamento['modelo'] ?></td>
                                                <td><?= $equipamento['serie'] ?></td>
                                                <td><?= $equipamento['condicoes'] ?></td>
                                                <td><?= $equipamento['defeitos'] ?></td>
                                                <td><?= $equipamento['acessorios'] ?></td>
                                                <td><?= $equipamento['solucao'] ?></td>
                                                <td><?= $equipamento['laudo_tecnico'] ?></td>
                                                <td><?= $equipamento['termos_de_garantia'] ?></td>
                                            <tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10">Nenhum registro!</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Produtos/Peças</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="7" style="text-align: center">PRODUTOS/PEÇAS DA OS</th>
                                    </tr>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Qtd</th>
                                        <th>Valor</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_produtos_pecas = 0; ?>

                                    <?php if(!empty($produtos_os_provisorio)): ?>
                                        <?php foreach($produtos_os_provisorio as $produto): ?>
                                            <tr>
                                                <td><?= $produto['nome'] ?></td>
                                                <td><?= $produto['quantidade'] ?></td>
                                                <td><?= $produto['valor_unitario'] ?></td>
                                                <td><?= $produto['quantidade'] * $produto['valor_unitario'] ?></td>
                                                <td><?= $produto['desconto'] ?></td>
                                                <td>
                                                    <?php
                                                        $total = $produto['quantidade'] * $produto['valor_unitario'] - $produto['desconto'];
                                                    ?>
                                                </td>
                                            <tr>

                                            <?php $total_produtos_pecas += $total ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7">Nenhum registro!</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Serviços/Mão de obra</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="7" style="text-align: center">SERVIÇOS/MÃO DE OBRA DA OS</th>
                                    </tr>
                                    <tr>
                                        <th>Serviço</th>
                                        <th>Qtd</th>
                                        <th>Valor</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_servicos_mao_de_obra = 0; ?>

                                    <?php if(!empty($servicos_mao_de_obra_os_provisorio)): ?>
                                        <?php foreach($servicos_mao_de_obra_os_provisorio as $servico): ?>
                                            <tr>
                                                <td><?= $servico['nome'] ?></td>
                                                <td><?= $servico['quantidade'] ?></td>
                                                <td><?= $servico['valor'] ?></td>
                                                <td><?= $servico['valor'] * $servico['valor'] ?></td>
                                                <td><?= $servico['desconto'] ?></td>
                                                <td>
                                                    <?php $total = $servico['valor'] * $servico['valor'] - $servico['desconto']; ?>
                                                </td>
                                            <tr>

                                            <?php $total_servicos_mao_de_obra += $total; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7">Nenhum registro!</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div id="dados-do-total" class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Total</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="/ordensDeServicos/alteraTotal" method="post">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label for="">Produtos/Peças</label>
                                    <input type="text" class="form-control" value="<?= $total_produtos_pecas ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label for="">Servico/Mão de obra</label>
                                    <input type="text" class="form-control" value="<?= $total_servicos_mao_de_obra ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label for="">Frete</label>
                                    <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['frete'] ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label for="">Outros</label>
                                    <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['outros'] ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label for="">Desconto</label>
                                    <input type="text" class="form-control" value="<?= $dados_ordem_de_servico['desconto'] ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <?php
                                        $valor_total_do_pagamento = ($total_produtos_pecas + $total_servicos_mao_de_obra + $dados_ordem_de_servico['frete'] + $dados_ordem_de_servico['outros']) - $dados_ordem_de_servico['desconto'];
                                    ?>
                                    <input type="text" class="form-control" id="valor_total_do_pagamento" name="valor_total_do_pagamento" value="<?= $valor_total_do_pagamento ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div id="pagamento_os" class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Pagamento</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="form-group">
                                <label for="">Tipo</label>
                                <input type="text" class="form-control" value="<?= $pagamento_os['tipo'] ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Ordem</th>
                                        <th>Data de Vencimento</th>
                                        <th>Valor da Parcela</th>
                                        <th>Forma de PGTO</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $ordem = 1 ?>
                                    <?php foreach($parcelas_pagamento_os as $parcela): ?>
                                        <tr>
                                            <td><?= $ordem ?></td>
                                            <td><?= $parcela['data_de_vencimento'] ?></td>
                                            <td><?= $parcela['valor_da_parcela'] ?></td>
                                            <td><?= $parcela['forma_de_pagamento'] ?></td>
                                            <td><?= $parcela['observacoes'] ?></td>
                                        </tr>

                                        <?php $ordem += 1 ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Anexos</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Nome</label>
                                <input type="text" class="form-control" name="produto" value="">
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="exampleInputFile">Arquivo</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="exampleInputFile">
                                        <label class="custom-file-label" for="exampleInputFile">Selecione o arquivo</label>
                                    </div>
                                    <div class="input-group-append">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-group">
                                <button type="buttom" class="btn btn-success btn-block" style="margin-top: 30px">Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Arquivo</th>
                                        <th style="width: 10px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3">Nenhum registro!</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->