<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
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
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Chave</th>
                                <th>Erro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($nfces)) : ?>
                                <?php foreach ($nfces as $nfce) : ?>
                                    <tr>
                                        <td><?= $nfce['id_nfce'] ?></td>
                                        <?php if ($nfce['status'] == "Emitida") : ?>
                                            <td><span class="right badge badge-success"><?= $nfce['status'] ?></span></td>
                                        <?php else : ?>
                                            <td><span class="right badge badge-danger"><?= $nfce['status'] ?></span></td>
                                        <?php endif; ?>
                                        <td><?= $nfce['data'] ?></td>
                                        <td><?= $nfce['hora'] ?></td>
                                        <td><?= $nfce['chave'] ?></td>
                                        <td>
                                            <?php if ($nfce['status'] != "Emitida") : ?>
                                                <a href="/controleFiscal/showErro/<?= $nfce['id_nfce'] ?>" class="btn btn-info">Ver Erro</a>
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
                    title: 'Conta à Receber cadastrada com sucesso!'
                })
            <?php elseif ($alert == "success_update") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Culto atualizado com sucesso!'
                })
            <?php elseif ($alert == "success_delete") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Conta à Receber excluida com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>