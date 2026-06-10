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
                                    <?php foreach ($situacoes_alteracao as $indice => $situacao) : ?>
                                        <option value="<?= $situacao ?>" <?= ($indice == 0) ? "selected" : "" ?>><?= $situacao ?></option>
                                    <?php endforeach; ?>
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
                <form id="formFiltrar" action="<?= $rota_listagem ?>" method="get">
                    <div class="row no-print">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Cód. do Serviço</label>
                                <input type="text" class="form-control" name="id_ordem" value="<?= (isset($id_ordem)) ? $id_ordem : "" ?>">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Data Inicio</label>
                                <input type="date" class="form-control" name="data_inicio" value="<?= isset($data_inicio) ? $data_inicio : "" ?>">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Data Final</label>
                                <input type="date" class="form-control" name="data_final" value="<?= isset($data_final) ? $data_final : "" ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Cliente</label>
                                <select class="form-control select2" name="id_cliente" style="width: 100%;">
                                    <?php if (!empty($clientes)) : ?>
                                        <option value="" <?= (!isset($id_cliente)) ? "selected" : "" ?>>-- Selecione --</option>
                                        <option value="Todos" <?= (isset($id_cliente) && $id_cliente == "Todos") ? "selected" : "" ?>>Todos</option>
                                        <?php foreach ($clientes as $cliente) : ?>
                                            <?php $nome_cliente_filtro = (isset($cliente['tipo']) && $cliente['tipo'] != 1 && !empty($cliente['razao_social'])) ? $cliente['razao_social'] : $cliente['nome']; ?>
                                            <option value="<?= $cliente['id_cliente'] ?>" <?= (isset($id_cliente) && $cliente['id_cliente'] == $id_cliente) ? "selected" : "" ?>><?= $nome_cliente_filtro ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
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
                    <a href="<?= $rota_listagem ?>" class="btn btn-danger"><i class="fas fa-filter"></i> Remover Filtro</a>
                    <button type="submit" class="btn btn-success" onclick="document.getElementById('formFiltrar').submit()"><i class="fas fa-filter"></i> Filtrar</button>
                </div>
            </div>
        </div>
    </div>
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
                            <?php if ($exibe_botao_novo_orcamento) : ?>
                                <a href="/ordensDeServicos/create" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Novo Orçamento</a>
                            <?php endif; ?>
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
                            <h6 class="m-0 text-dark"><i class="fas fa-list"></i> <?= $titulo_lista ?></h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered table-striped tabela-listagem">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th>Cliente</th>
                                <th>Valor</th>
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
                                        <td><?= number_format($ordem['valor_servicos'] ?? 0, 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($ordem['data_de_entrada'])) ?> - <?= $ordem['hora_de_entrada'] ?></td>
                                        <?php $saida_em_aguardo = empty($ordem['data_de_saida']) || $ordem['data_de_saida'] == "0000-00-00" || empty($ordem['hora_de_saida']) || $ordem['hora_de_saida'] == "00:00:00"; ?>
                                        <td><?= $saida_em_aguardo ? 'Aguardando' : date('d/m/Y', strtotime($ordem['data_de_saida'])) . ' - ' . $ordem['hora_de_saida'] ?></td>
                                        <td>
                                            <?php if($ordem['situacao'] == "Em aberto" || $ordem['situacao'] == "Aberto"): ?>
                                                <span class="badge badge-primary" style="height: 20px; font-size: 12px; color: white; border-radius: 2px;"><?= ($ordem['situacao'] == "Aberto") ? "Em aberto" : $ordem['situacao'] ?></span>
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h6><b>Data:</b> <?= date('d/m/Y') ?></h6>
                            <h6><b>Hora:</b> <?= date('H:i:s') ?></h6>
                            <h6><b>Critérios:</b> <?= (isset($id_ordem)) ? "Cód. do Serviço=$id_ordem" : "" ?><?= (isset($data_inicio)) ? " | Data Inicio=$data_inicio" : "" ?><?= (isset($data_final)) ? " | Data Final=$data_final" : "" ?><?= (isset($id_cliente)) ? " | Cód. do Cliente=$id_cliente" : "" ?></h6>
                        </div>
                    </div>
                </div>
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
            <?php elseif ($alert == "success_filter") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Filtro aplicado!'
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

    /**
     * Atualiza a situacao da ordem de servico selecionada.
     */
    function alteraSituacaoDaOS(id_ordem)
    {
        document.getElementById('altera_situacao_id_ordem').value = id_ordem;
    }
</script>
