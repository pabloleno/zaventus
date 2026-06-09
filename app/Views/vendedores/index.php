<?php use App\Libraries\ImagemCadastro; ?>
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
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="/funcionarios/create?tipo=Vendedor" class="btn btn-primary"><i class="fa fa-user-plus"></i> Novo Vendedor</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            </div>
            <!-- /.card -->
            <div class="card">
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th style="width: 60px">Foto</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Data Inicio das Vendas</th>
                                <th>Anotações</th>
                                <th style="width: 110px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vendedores)) : ?>
                                <?php foreach ($vendedores as $vendedor) : ?>
                                    <tr>
                                        <td><?= $vendedor['id_vendedor'] ?></td>
                                        <td class="text-center"><img src="<?= esc(ImagemCadastro::url($vendedor['foto'] ?? '')) ?>" alt="Foto" class="foto-cadastro-miniatura"></td>
                                        <td><?= $vendedor['nome'] ?></td>
                                        <td><?= $vendedor['status'] ?></td>
                                        <td><?= $vendedor['data_inicio_das_atividades'] ?></td>
                                        <td><?= $vendedor['anotacoes'] ?></td>
                                        <td>
                                            <a href="/vendedores/edit/<?= $vendedor['id_vendedor'] ?>" class="btn btn-warning style-action"><i class="fa fa-edit"></i></a>
                                            <?php if (strtoupper(trim((string) $vendedor['nome'])) !== 'GERAL') : ?>
                                                <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse Vendedor?', '/vendedores/delete/<?= $vendedor['id_vendedor'] ?>')"><i class="fa fa-trash"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
            <?php if ($alert == "success_create") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Vendedor cadastrado com sucesso!'
                })
            <?php elseif ($alert == "success_edit") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Vendedor atualizado com sucesso!'
                })
            <?php elseif ($alert == "success_delete") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Vendedor excluido com sucesso!'
                })
            <?php elseif ($alert == "error_delete_geral") : ?>
                Toast.fire({
                    type: 'error',
                    title: 'O vendedor GERAL nao pode ser excluido.'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
