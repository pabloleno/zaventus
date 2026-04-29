<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
                        
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> Formas de Pagamento</h6>
                                </div><!-- /.col -->
                                <div class="col-lg-6">
                                    <a href="/configs/createFormaDePagamento" class="btn btn-primary style-action"><i class="fas fa-plus"></i> Nova Forma de Pagamento</a>
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
                                                <th>Forma de Pagamento</th>
                                                <th style="width: 110px">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($formas_de_pagamento)) : ?>
                                                <?php foreach ($formas_de_pagamento as $forma) : ?>
                                                    <tr>
                                                        <td><?= $forma['id_forma'] ?></td>
                                                        <td><?= $forma['nome'] ?></td>
                                                        <td>
                                                            <a href="/configs/editFormaDePagamento/<?= $forma['id_forma'] ?>" class="btn btn-warning style-action"><i class="fas fa-edit"></i></a>
                                                            <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir essa forma de pagamento?', '/configs/delete_forma_de_pagamento/<?= $forma['id_forma'] ?>')"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td colspan="3">Nenhum registro!</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <!-- <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-12" style="text-align: right">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <!-- /.card -->
                </div>
                
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-5">
                                    <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> Tema</h6>
                                </div><!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="/configs/alteraTema" method="post">
                                <div class="row">
                                    <div class="col-lg-10">
                                        <select class="form-control select2" name="tema">
                                            <?php
                                            $session = session();
                                            $tema = $session->get('tema');
                                            if($tema == 0):
                                            ?>
                                                <option value="0" selected>Sistema Web</option>
                                                <option value="1">Sistema DeskTop</option>
                                            <?php else: ?>
                                                <option value="0">Sistema Web</option>
                                                <option value="1" selected>Sistema DeskTop</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2">
                                            <button class="btn btn-primary">Salvar</button>
                                        </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                        <!-- <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-12" style="text-align: right">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <!-- /.card -->        
                </div>
                
            </div>

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
            <?php if ($alert == "success_edit") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Dados da Empresa atualizados com sucesso!'
                })
            <?php elseif ($alert == "success_create_forma_de_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Forma de Pagamento cadastrada com sucesso!'
                })
            <?php elseif ($alert == "success_edit_forma_de_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Forma de Pagamento etualizada com sucesso!'
                })
            <?php elseif ($alert == "success_delete_forma_de_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Forma de Pagamento excluida com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
