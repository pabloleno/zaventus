<!-- FILTRAR -->
<div class="modal fade" id="modal-filtrar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-filter"></i> Filtrar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formFiltrar" action="/caixas" method="get">
                    <div class="row no-print">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Cód.</label>
                                <input type="text" class="form-control" name="id_caixa" value="<?= (isset($id_caixa)) ? $id_caixa : "" ?>">
                            </div>
                        </div>
                        <?php if (isset($status)) : ?>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control select2" name="status" style="width: 100%;">
                                        <?php if ($status == "Todos") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos" selected>Todos</option>
                                            <option value="Aberto">Aberto</option>
                                            <option value="Fechado">Fechado</option>
                                        <?php elseif ($status == "Aberto") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos">Todos</option>
                                            <option value="Aberto" selected>Aberto</option>
                                            <option value="Fechado">Fechado</option>
                                        <?php elseif ($status == "Fechado") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos">Todos</option>
                                            <option value="Aberto">Aberto</option>
                                            <option value="Fechado" selected>Fechado</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control select2" name="status" style="width: 100%;">
                                        <option value="" selected>-- Selecione --</option>
                                        <option value="Todos">Todos</option>
                                        <option value="Aberto">Aberto</option>
                                        <option value="Fechado">Fechado</option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Data Abert. Inicio</label>
                                <input type="date" class="form-control" name="data_inicio" value="<?= isset($data_inicio) ? $data_inicio : "" ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Data Abert. Final</label>
                                <input type="date" class="form-control" name="data_final" value="<?= isset($data_final) ? $data_final : "" ?>">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="col-lg-4">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
                <div class="col-lg-8" style="text-align: right">
                    <a href="/caixas" class="btn btn-danger"><i class="fas fa-filter"></i> Remover Filtro</a>
                    <button type="submit" class="btn btn-success" onclick="document.getElementById('formFiltrar').submit()"><i class="fas fa-filter"></i> Filtrar</button>
                </div>
            </div>
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
                <!-- /.card-header -->
                <div class="card-body no-print">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="/caixas/abrir" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Abrir Caixa</a>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-filtrar"><i class="fa fa-filter"></i> Filtrar</button>
                            <button type="button" class="btn btn-info" onclick="print()"><i class="fas fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> <?= (isset($ultimos_cinco)) ? "Cinco últimos caixas cadastrados" : "Registros" ?></h6>
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
                                <th>Data de abert.</th>
                                <th>Hora de abert.</th>
                                <th>Data de fecham.</th>
                                <th>Hora de fecham.</th>
                                <th class="no-print" style="width: 130px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($caixas)) : ?>
                                <?php foreach ($caixas as $caixa) : ?>
                                    <tr>
                                        <td><?= $caixa['id_caixa'] ?></td>
                                        <td><?= $caixa['status'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($caixa['data_de_abertura'])) ?></td>
                                        <td><?= $caixa['hora_de_abertura'] ?></td>
                                        <td><?= ($caixa['data_de_fechamento'] == "0000-00-00" ? "Não definida!" : date('d/m/Y', strtotime($caixa['data_de_fechamento']))) ?></td>
                                        <td><?= ($caixa['hora_de_fechamento'] == "00:00:00" ? "Não definida!" : $caixa['hora_de_fechamento']) ?></td>
                                        <td class="no-print">
                                            <a href="/caixas/show/<?= $caixa['id_caixa'] ?>" class="btn btn-info style-action"><i class="fa fa-folder-open"></i></a>
                                            <!-- <button type="button" class="btn btn-warning style-action"><i class="fa fa-edit"></i></button>
                                            <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse caixa?', '/caixas/delete/<?= $caixa['id_caixa'] ?>')"><i class="fa fa-trash"></i></button> -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7">Nenhum registro!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> Detalhes</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h6><b>Data:</b> <?= date('d/m/Y') ?></h6>
                            <h6><b>Hora:</b> <?= date('H:i:s') ?></h6>
                            <h6><b>Critérios:</b> <?= (isset($id_caixa)) ? "Por cód." : "" ?><?= (isset($status)) ? " | Status=$status" : "" ?><?= (isset($data_inicio)) ? " | Data Inicio=$data_inicio e Data Final=$data_final" : "" ?></h6>
                        </div>
                    </div>
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
            <?php if ($alert == "success_open") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Caixa aberto com sucesso!'
                })
            <?php elseif ($alert == "success_update") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Culto atualizado com sucesso!'
                })
            <?php elseif ($alert == "success_delete") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Caixa excluido com sucesso!'
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