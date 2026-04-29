<!-- Modal Altera Situação -->
<div class="modal fade" id="modal-altera-situacao">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-plus-circle"></i> Alterar Situação da OS</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/ordensDeServicos/alteraSituacaoDaOrdemDeServicos" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Situação</label>
                                <select class="form-control select2" name="situacao" style="width: 100%;" required="">
                                    <option value="Em aberto" selected>Em aberto</option>
                                    <option value="Em andamento">Em andamento</option>
                                    <option value="Concretizada">Concretizada</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="altera_situacao_id_ordem" name="id_ordem">
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
                <div class="card-body no-print">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="/ordensDeServicos/create" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Nova Ordem de Servico</a>
                            <!-- <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-filtrar"><i class="fa fa-filter"></i> Filtrar</button> -->
                            <!-- <button type="button" class="btn btn-info" onclick="print()"><i class="fas fa-print"></i> Imprimir</button> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> 15 últimas ordens de serviços cadastradas</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th>Cliente</th>
                                <th>Entrada</th>
                                <th>Saída</th>
                                <th>Situação</th>
                                <th class="no-print" style="width: 160px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ordens_de_servicos)) : ?>
                                <?php foreach ($ordens_de_servicos as $ordem) : ?>
                                    <tr>
                                        <td><?= $ordem['id_ordem'] ?></td>
                                        <td><?= $ordem['nome'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($ordem['data_de_entrada'])) ?> - <?= $ordem['hora_de_entrada'] ?></td>
                                        <td><?= ($ordem['data_de_saida'] == "0000-00-00") ? '00-00-0000' : date('d/m/Y', strtotime($ordem['data_de_saida'])) ?> - <?= $ordem['hora_de_saida'] ?></td>
                                        <td>
                                            <?php if($ordem['situacao'] == "Em aberto"): ?>
                                                <span class="badge badge-primary" style="height: 20px; font-size: 12px; color: white; border-radius: 2px;"><?= $ordem['situacao'] ?></span>
                                            <?php elseif($ordem['situacao'] == "Em andamento"): ?>
                                                <span class="badge badge-warning" style="height: 20px; font-size: 12px; color: white; border-radius: 2px;"><?= $ordem['situacao'] ?></span>
                                            <?php elseif($ordem['situacao'] == "Concretizada"): ?>
                                                <span class="badge badge-success" style="height: 20px; font-size: 12px; color: white; border-radius: 2px;"><?= $ordem['situacao'] ?></span>
                                            <?php elseif($ordem['situacao'] == "Cancelada"): ?>
                                                <span class="badge badge-danger" style="height: 20px; font-size: 12px; color: white; border-radius: 2px;"><?= $ordem['situacao'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="no-print">
                                            <a href="/ordensDeServicos/show/<?= $ordem['id_ordem'] ?>" class="btn btn-info style-action"><i class="fa fa-folder-open"></i></a>
                                            <button type="button" class="btn btn-success style-action" onclick="alteraSituacaoDaOS(<?= $ordem['id_ordem'] ?>)" data-toggle="modal" data-target="#modal-altera-situacao"><i class="fas fa-check-circle"></i></button>
                                            <a href="/ordensDeServicos/edit/<?= $ordem['id_ordem'] ?>" class="btn btn-warning style-action"><i class="fa fa-edit"></i></a>
                                            <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir essa ordem de serviço?', '/ordensDeServicos/delete/<?= $ordem['id_ordem'] ?>')"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8">Nenhum registro!</td>
                                </tr>
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
            <?php if ($alert == "success_finaliza_ordem_de_servico") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Ordem de serviço cadastrada com sucesso!'
                })
            <?php elseif ($alert == "success_filtro") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Filtro realizado com sucesso!'
                })
            <?php elseif ($alert == "success_delete_ordem_de_servico") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Ordem de serviço excluida com sucesso!'
                })
            <?php elseif ($alert == "success_altera_situacao_ordem_de_servivo") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Situação da Ordem de Serviço alterada com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });

    function alteraSituacaoDaOS(id_ordem)
    {
        document.getElementById('altera_situacao_id_ordem').value = id_ordem;
    }
</script>