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
                <form id="formFiltrar" action="/despesas" method="get">
                    <div class="row no-print">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Cód.</label>
                                <input type="text" class="form-control" name="id_despesa" value="<?= (isset($id_despesa)) ? $id_despesa : "" ?>">
                            </div>
                        </div>
                        <?php if (isset($tipo)) : ?>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <select class="form-control select2" name="tipo" style="width: 100%;">
                                        <?php if ($tipo == "Todos") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos" selected>Todos</option>
                                            <option value="Prolabore">Prolabore</option>
                                            <option value="Impostos">Impostos</option>
                                            <option value="Despesas variáveis">Despesas variáveis</option>
                                            <option value="Despesas fixas">Despesas fixas</option>
                                            <option value="Gastos com pessoas">Gastos com pessoas</option>
                                        <?php elseif ($tipo == "Prolabore") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos">Todos</option>
                                            <option value="Prolabore" selected>Prolabore</option>
                                            <option value="Impostos">Impostos</option>
                                            <option value="Despesas variáveis">Despesas variáveis</option>
                                            <option value="Despesas fixas">Despesas fixas</option>
                                            <option value="Gastos com pessoas">Gastos com pessoas</option>
                                        <?php elseif ($tipo == "Impostos") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos">Todos</option>
                                            <option value="Prolabore">Prolabore</option>
                                            <option value="Impostos" selected>Impostos</option>
                                            <option value="Despesas variáveis">Despesas variáveis</option>
                                            <option value="Despesas fixas">Despesas fixas</option>
                                            <option value="Gastos com pessoas">Gastos com pessoas</option>
                                        <?php elseif ($tipo == "Despesas variáveis") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos">Todos</option>
                                            <option value="Prolabore">Prolabore</option>
                                            <option value="Impostos">Impostos</option>
                                            <option value="Despesas variáveis" selected>Despesas variáveis</option>
                                            <option value="Despesas fixas">Despesas fixas</option>
                                            <option value="Gastos com pessoas">Gastos com pessoas</option>
                                        <?php elseif ($tipo == "Despesas fixas") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos">Todos</option>
                                            <option value="Prolabore">Prolabore</option>
                                            <option value="Impostos">Impostos</option>
                                            <option value="Despesas variáveis">Despesas variáveis</option>
                                            <option value="Despesas fixas" selected>Despesas fixas</option>
                                            <option value="Gastos com pessoas">Gastos com pessoas</option>
                                        <?php elseif ($tipo == "Gastos com pessoas") : ?>
                                            <option value="">-- Selecione --</option>
                                            <option value="Todos">Todos</option>
                                            <option value="Prolabore">Prolabore</option>
                                            <option value="Impostos">Impostos</option>
                                            <option value="Despesas variáveis">Despesas variáveis</option>
                                            <option value="Despesas fixas">Despesas fixas</option>
                                            <option value="Gastos com pessoas" selected>Gastos com pessoas</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <select class="form-control select2" name="tipo" style="width: 100%;">
                                        <option value="" selected>-- Selecione --</option>
                                        <option value="Todos">Todos</option>
                                        <option value="Prolabore">Prolabore</option>
                                        <option value="Impostos">Impostos</option>
                                        <option value="Despesas variáveis">Despesas variáveis</option>
                                        <option value="Despesas fixas">Despesas fixas</option>
                                        <option value="Gastos com pessoas">Gastos com pessoas</option>
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
                    <a href="/despesas" class="btn btn-danger"><i class="fas fa-filter"></i> Remover Filtro</a>
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
                <div class="card-body no-print">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="/despesas/create" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Nova Despesa</a>
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
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> <?= (isset($ultimos_cinco)) ? "Cinco últimas despesas cadastradas" : "Registros" ?></h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Observações</th>
                                <th class="no-print" style="width: 110px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($despesas)) : ?>
                                <?php foreach ($despesas as $despesa) : ?>
                                    <tr>
                                        <td><?= $despesa['id_despesa'] ?></td>
                                        <td><?= $despesa['tipo'] ?></td>
                                        <td><?= $despesa['descricao'] ?></td>
                                        <td><?= number_format($despesa['valor'], 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($despesa['data'])) ?></td>
                                        <td><?= $despesa['hora'] ?></td>
                                        <td><?= $despesa['observacoes'] ?></td>
                                        <td class="no-print">
                                            <a href="/despesas/edit/<?= $despesa['id_despesa'] ?>" class="btn btn-warning style-action"><i class="fa fa-edit"></i></a>
                                            <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir essa despesa?', '/despesas/delete/<?= $despesa['id_despesa'] ?>')"><i class="fa fa-trash"></i></button>
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
                            <h6><b>Critérios:</b> <?= (isset($id_despesa)) ? "Por cód." : "" ?><?= (isset($tipo)) ? " | Tipo=$tipo" : "" ?><?= (isset($data_inicio)) ? " | Data Inicio=$data_inicio e Data Final=$data_final" : "" ?></h6>
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
            <?php if ($alert == "success_create") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Despesa cadastrada com sucesso!'
                })
            <?php elseif ($alert == "success_edit") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Despesa atualizada com sucesso!'
                })
            <?php elseif ($alert == "success_delete") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Despesa excluida com sucesso!'
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