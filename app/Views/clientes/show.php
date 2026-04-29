<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row" style="margin-bottom: 15px">
                <div class="col-sm-12 no-print">
                    <ol class="breadcrumb float-sm-right">
                        <a href="/clientes" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="/clientes/edit/<?= $cliente['id_cliente'] ?>" class="btn btn-warning"><i class="fa fa-edit"></i> Editar</a>
                            <button type="button" class="btn btn-danger" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse cliente? Essa ação não poderá ser desfeita!', '/clientes/delete/<?= $cliente['id_cliente'] ?>')"><i class="fa fa-trash"></i> Excluir</button>
                            <a href="/pagamentosDoCliente/create/<?= $cliente['id_cliente'] ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Novo Pagamento</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            </div>
            <!-- /.card -->
            <div class="row">
                <div class="col-12">
                    <!-- Custom Tabs -->
                    <div class="card">
                    <div class="card-header d-flex p-0">
                        <h3 class="card-title p-3">
                            <?php
                                if ($cliente['tipo'] == 1) :
                                    echo "<b>Cliente:</b> " . $cliente['nome'];
                                else:
                                    echo "<b>Cliente:</b> " . $cliente['razao_social'];
                                endif;
                            ?>
                        </h3>
                        <ul class="nav nav-pills ml-auto p-2">
                            <li class="nav-item"><a class="nav-link active" href="#dados" data-toggle="tab">Dados do Cliente</a></li>
                            <li class="nav-item"><a class="nav-link" href="#pagamentos" data-toggle="tab">Pagamentos</a></li>
                            <li class="nav-item"><a class="nav-link" href="#vendas" data-toggle="tab">Compras</a></li>
                            <li class="nav-item"><a class="nav-link" href="#orcamentos" data-toggle="tab">Orçamentos</a></li>
                            <li class="nav-item"><a class="nav-link" href="#pedidos" data-toggle="tab">Pedidos</a></li>
                            <li class="nav-item"><a class="nav-link" href="#ordens_de_servicos" data-toggle="tab">Ordens de Serviços</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                        <div class="tab-pane active" id="dados">
                            <div class="row">
                                <?php if ($cliente['tipo'] == 1) : ?>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Tipo</label>
                                            <input type="text" class="form-control" value="Pessoa Física" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-group">
                                            <label for="">Nome</label>
                                            <input type="text" class="form-control" value="<?= $cliente['nome'] ?>" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Data de nascimento</label>
                                            <input type="text" class="form-control" value="<?= $cliente['data_de_nascimento'] ?>" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">RG</label>
                                            <input type="text" class="form-control" value="<?= $cliente['rg'] ?>" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">CPF</label>
                                            <input type="text" class="form-control" value="<?= $cliente['cpf'] ?>" disabled="">
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Tipo</label>
                                            <input type="text" class="form-control" value="Pessoa Jurídica" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-group">
                                            <label for="">Razão social</label>
                                            <input type="text" class="form-control" value="<?= $cliente['razao_social'] ?>" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Nome fantasia</label>
                                            <input type="text" class="form-control" value="<?= $cliente['nome_fantasia'] ?>" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">CNPJ</label>
                                            <input type="text" class="form-control" value="<?= $cliente['cnpj'] ?>" disabled="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">IE</label>
                                            <input type="text" class="form-control" value="<?= $cliente['ie'] ?>" disabled="">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row" style="margin-top: 50px">
                                <div class="col-lg-12">
                                    <h5>Endereço</h5>
                                    <hr>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">CEP</label>
                                        <input type="text" class="form-control" value="<?= $cliente['cep'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Logradouro</label>
                                        <input type="text" class="form-control" value="<?= $cliente['logradouro'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="">Número</label>
                                        <input type="text" class="form-control" value="<?= $cliente['numero'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="">Complemento</label>
                                        <input type="text" class="form-control" value="<?= $cliente['complemento'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Bairro</label>
                                        <input type="text" class="form-control" value="<?= $cliente['bairro'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Município</label>
                                        <input type="text" class="form-control" value="<?= $cliente['municipio'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">UF</label>
                                        <input type="text" class="form-control" value="<?= $cliente['UF'] ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row" style="margin-top: 50px">
                                <div class="col-lg-12">
                                    <h5><i class="fa fa-phone-square"></i> Contato</h5>
                                    <hr>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Celular</label>
                                        <input type="text" class="form-control" value="<?= $cliente['celular'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Comercial</label>
                                        <input type="text" class="form-control" value="<?= $cliente['comercial'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Residencial</label>
                                        <input type="text" class="form-control" value="<?= $cliente['residencial'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">E-mail</label>
                                        <input type="text" class="form-control" value="<?= $cliente['email'] ?>" disabled="">
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 50px">
                                <div class="col-lg-12">
                                    <h5><i class="fa fa-info-circle"></i> Extra</h5>
                                    <hr>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="">Anotações</label>
                                        <textarea class="form-control" rows="10" disabled=""><?= $cliente['anotacoes'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="pagamentos">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cód.</th>
                                                <th>Descrição</th>
                                                <th>Valor</th>
                                                <th>Data</th>
                                                <th>Hora</th>
                                                <th>Obs</th>
                                                <th style="width: 130px">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($pagamentos)): ?>
                                                <?php foreach($pagamentos as $pagamento): ?>
                                                    <tr>
                                                        <td><?= $pagamento['id_pagamento'] ?></td>
                                                        <td><?= $pagamento['descricao'] ?></td>
                                                        <td><?= $pagamento['valor'] ?></td>
                                                        <td><?= $pagamento['data'] ?></td>
                                                        <td><?= $pagamento['hora'] ?></td>
                                                        <td><?= $pagamento['observacoes'] ?></td>
                                                        <td>
                                                            <a href="/pagamentosDoCliente/edit/<?= $pagamento['id_pagamento'] ?>/<?= $pagamento['id_cliente'] ?>" class="btn btn-warning style-action"><i class="fa fa-edit"></i></a>
                                                            <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse pagamento?', '/pagamentosDoCliente/delete/<?= $pagamento['id_pagamento'] ?>/<?= $pagamento['id_cliente'] ?>')"><i class="fa fa-trash"></i></button>
                                                        </td>
                                                    </tr>
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
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="vendas">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cód.</th>
                                                <th>Data</th>
                                                <th>Hora</th>
                                                <th>Valor à Pagar</th>
                                                <th>Desconto</th>
                                                <th>Valor Recebido</th>
                                                <th>Troco</th>
                                                <th>Forma PGTO</th>
                                                <th>Cód. Cliente</th>
                                                <th>Cód. Caixa</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($vendas)): ?>
                                                <?php foreach($vendas as $venda): ?>
                                                    <tr>
                                                        <td><?= $venda['id_venda'] ?></td>
                                                        <td><?= $venda['data'] ?></td>
                                                        <td><?= $venda['hora'] ?></td>
                                                        <td><?= $venda['valor_a_pagar'] ?></td>
                                                        <td><?= $venda['desconto'] ?></td>
                                                        <td><?= $venda['valor_recebido'] ?></td>
                                                        <td><?= $venda['troco'] ?></td>
                                                        <td><?= $venda['forma_de_pagamento'] ?></td>
                                                        <td><?= $venda['id_cliente'] ?></td>
                                                        <td><?= $venda['id_caixa'] ?></td>
                                                        <td>
                                                            <a href="/vendas/show/<?= $venda['id_venda'] ?>" class="btn btn-info style-action"><i class="fa fa-folder-open"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="11">Nenhum registro!</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="orcamentos">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cód.</th>
                                                <th>Data</th>
                                                <th>Hora</th>
                                                <th>Cód. Cliente</th>
                                                <th>Cód. Caixa</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($orcamentos)): ?>
                                                <?php foreach($orcamentos as $orcamento): ?>
                                                    <tr>
                                                        <td><?= $orcamento['id_orcamento'] ?></td>
                                                        <td><?= $orcamento['data'] ?></td>
                                                        <td><?= $orcamento['hora'] ?></td>
                                                        <td><?= $orcamento['id_cliente'] ?></td>
                                                        <td><?= $orcamento['id_caixa'] ?></td>
                                                        <td>
                                                            <a href="/orcamentos/show/<?= $orcamento['id_orcamento'] ?>" class="btn btn-info style-action"><i class="fa fa-folder-open"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6">Nenhum registro!</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="pedidos">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cód.</th>
                                                <th>Data</th>
                                                <th>Hora</th>
                                                <th>Cód. Cliente</th>
                                                <th>Cód. Caixa</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($pedidos)): ?>
                                                <?php foreach($pedidos as $pedido): ?>
                                                    <tr>
                                                        <td><?= $pedido['id_pedido'] ?></td>
                                                        <td><?= $pedido['data'] ?></td>
                                                        <td><?= $pedido['hora'] ?></td>
                                                        <td><?= $pedido['id_cliente'] ?></td>
                                                        <td><?= $pedido['id_caixa'] ?></td>
                                                        <td>
                                                            <a href="/pedidos/show/<?= $pedido['id_pedido'] ?>" class="btn btn-info style-action"><i class="fa fa-folder-open"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6">Nenhum registro!</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="ordens_de_servicos">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cód.</th>
                                                <th>Cliente</th>
                                                <th>Entrada</th>
                                                <th>Saída</th>
                                                <th>Situação</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($ordens_de_servicos)): ?>
                                                <?php foreach($ordens_de_servicos as $ordem): ?>
                                                    <tr>
                                                        <td><?= $ordem['id_ordem'] ?></td>
                                                        <td><?= $ordem['nome_do_cliente'] ?></td>
                                                        <td><?= $ordem['data_de_entrada'] ?> às <?= $ordem['hora_de_entrada'] ?></td>
                                                        <td><?= $ordem['data_de_saida'] ?> às <?= $ordem['hora_de_saida'] ?></td>
                                                        <td><?= $ordem['situacao'] ?></td>
                                                        <td>
                                                            <a href="/ordensDeServicos/show/<?= $ordem['id_ordem'] ?>" class="btn btn-info style-action"><i class="fa fa-folder-open"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6">Nenhum registro!</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                    </div>
                    <!-- ./card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- END CUSTOM TABS -->
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
            <?php if ($alert == "success_create_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Pagamento do Cliente cadastrado com sucesso!'
                })
            <?php elseif ($alert == "success_edit_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Pagamento do Cliente atualizado com sucesso!'
                })
            <?php elseif ($alert == "success_delete_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Pagamento do Cliente excluido com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>