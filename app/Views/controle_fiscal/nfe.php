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
                    <form action="/controleFiscal/nfe" method="get">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Data Inicio</label>
                                    <input type="date" class="form-control" name="data_inicio" value="<?= isset($data_inicio) ? $data_inicio : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Data Final</label>
                                    <input type="date" class="form-control" name="data_final" value="<?= isset($data_final) ? $data_final : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <button type="sutmit" class="btn btn-success" style="margin-top: 30px"><i class="fa fa-filter"></i> Filtrar</button>
                                <?php if (isset($data_inicio) && isset($data_final)) : ?>
                                    <a href="/controleFiscal/baixaXMLS/<?= $data_inicio ?>/<?= $data_final ?>" class="btn btn-info" style="margin-top: 30px"><i class="fas fa-print"></i> Baixar XMLs</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> <?= (isset($ultimos_cinco)) ? "Cinco últimas XMLS das NFEs emitidas" : "Registros" ?></h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Chave</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($nfes)) : ?>
                                <?php foreach ($nfes as $nfe) : ?>
                                    <tr>
                                        <td><?= $nfe['id_nfe'] ?></td>
                                        <?php if ($nfe['status'] == "Emitida") : ?>
                                            <td><span class="right badge badge-success"><?= $nfe['status'] ?></span></td>
                                        <?php else : ?>
                                            <td><span class="right badge badge-danger"><?= $nfe['status'] ?></span></td>
                                        <?php endif; ?>
                                        <td><?= $nfe['data'] ?></td>
                                        <td><?= $nfe['hora'] ?></td>
                                        <td><?= $nfe['chave'] ?></td>
                                        <td>
                                            <?php if ($nfe['status'] != "Emitida") : ?>
                                                <a href="/controleFiscal/showErroNFe/<?= $nfe['id_nfe'] ?>" class="btn btn-warning style-action">Ver Erro</a>
                                            <?php endif; ?>

                                            <?php if ($nfe['status'] == "Emitida") : ?>
                                                <a href="/controleFiscal/baixaXML/<?= $nfe['id_nfe'] ?>" class="btn btn-info style-action">Baixar XML</a>
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
            <?php elseif ($alert == "success_filter") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Filtro aplicado!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>