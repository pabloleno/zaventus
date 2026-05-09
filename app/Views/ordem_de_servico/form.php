<?php
    $decimal_os = static function ($valor) {
        if(is_string($valor) && strpos($valor, ',') !== false)
        {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }

        return number_format((float) $valor, 2, '.', '');
    };
?>

<!-- Modal Adiciona novo Equipamento -->
<div class="modal fade" id="modal-add-equipamento">
    <div class="modal-dialog mw-100 w-75">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-plus-circle"></i> Novo equipamento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/ordensDeServicos/<?= (isset($acao_user)) ? 'addEquipamentoEdit' : 'addEquipamento' ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Equipamento</label>
                                <input type="text" class="form-control" name="equipamento" value="">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="">Marca</label>
                                <input type="text" class="form-control" name="marca" value="">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="">Modelo</label>
                                <input type="text" class="form-control" name="modelo" value="">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="">Série</label>
                                <input type="text" class="form-control" name="serie" value="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Condições</label>
                                <textarea class="form-control" name="condicoes" rows="5" value=""></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Defeitos</label>
                                <textarea class="form-control" name="defeitos" rows="5" value=""></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Acessórios</label>
                                <textarea class="form-control" name="acessorios" rows="5" value=""></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Solução</label>
                                <textarea class="form-control" name="solucao" rows="5" value=""></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Laudo ténico</label>
                                <textarea class="form-control" name="laudo_tecnico" rows="5" value=""></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Termos de garantia</label>
                                <textarea class="form-control" name="termos_de_garantia" rows="5" value=""></textarea>
                            </div>
                        </div>

                        <!-- ------ HIDDEN PARA EDIT ----- -->
                        <?php if(isset($acao_user)): ?>
                            <input type="hidden" name="id_ordem" value="<?= $id_ordem ?>">
                        <?php endif; ?>
                        <!-- ----------------------------- -->
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


<!-- Modal Visualiza dados do Equipamento -->
<div class="modal fade" id="modal-visualiza-dados-do-equipamento">
    <div class="modal-dialog mw-100 w-75">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-plus-circle"></i> Dados do Equipamento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Equipamento</label>
                            <input type="text" class="form-control" id="visualiza_eq_equipamento" disabled>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Marca</label>
                            <input type="text" class="form-control" id="visualiza_eq_marca" disabled>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Modelo</label>
                            <input type="text" class="form-control" id="visualiza_eq_modelo" disabled>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Série</label>
                            <input type="text" class="form-control" id="visualiza_eq_serie" disabled>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Condições</label>
                            <textarea class="form-control" id="visualiza_eq_condicoes" rows="5" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Defeitos</label>
                            <textarea class="form-control" id="visualiza_eq_defeitos" rows="5" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Acessórios</label>
                            <textarea class="form-control" id="visualiza_eq_acessorios" rows="5" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Solução</label>
                            <textarea class="form-control" id="visualiza_eq_solucao" rows="5" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Laudo ténico</label>
                            <textarea class="form-control" id="visualiza_eq_laudo_tecnico" rows="5" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Termos de garantia</label>
                            <textarea class="form-control" id="visualiza_eq_termos_de_garantia" rows="5" disabled></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<!-- Modal Adiciona novo Serviços/Mão de obra -->
<div class="modal fade" id="modal-add-servico-mao-de-obra">
    <div class="modal-dialog mw-100 w-75">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-plus-circle"></i> Novo Serviço/Mão de obra</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/ordensDeServicos/<?= (isset($acao_user)) ? 'addServicoMaoDeObraEdit' : 'addServicoMaoDeObra' ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Serviço</label>
                                <select class="form-control select2" name="id_servico" style="width: 100%;" required>
                                    <option value="">Selecione..</option>
                                    <?php if (!empty($servicos_mao_de_obra)) : ?>
                                        <?php foreach ($servicos_mao_de_obra as $servico) : ?>
                                            <option value="<?= $servico['id_servico'] ?>"><?= $servico['nome'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- ------ HIDDEN PARA EDIT ----- -->
                        <?php if(isset($acao_user)): ?>
                            <input type="hidden" name="id_ordem" value="<?= $id_ordem ?>">
                        <?php endif; ?>
                        <!-- ----------------------------- -->

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


<!-- Modal Edita Dados do SERVIÇO/MÃO DE OBRA -->
<div class="modal fade" id="modal-altera-dados-servico-mao-de-obra">
    <div class="modal-dialog mw-100 w-75">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-plus-circle"></i> Editar dados do Serviço Mão de Obra</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/ordensDeServicos/<?= (isset($acao_user)) ? 'alteraDadosServicoMaoDeObraEdit' : 'alteraDadosServicoMaoDeObra' ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Qtd</label>
                                <input type="text" class="form-control" id="altera_dados_servico_mao_de_obra_quantidade" name="quantidade" value="">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Valor</label>
                                <input type="number" step="0.01" class="form-control" id="altera_dados_servico_mao_de_obra_valor" name="valor" value="">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Desconto</label>
                                <input type="number" step="0.01" class="form-control" id="altera_dados_servico_mao_de_obra_desconto" name="desconto" value="">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="altera_dados_servico_mao_de_obra_id_servico" name="id_servico" value="">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <input type="hidden" class="form-control" name="id_ordem" value="<?= $id_ordem ?>">
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
                        <a href="/ordensDeServicos" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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

            <div id="table-equipamentos" class="card">
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
                        <div class="col-lg-12" style="text-align: right">
                            <button class="btn btn-info" data-toggle="modal" data-target="#modal-add-equipamento" style="margin-bottom: 15px"><i class="fas fa-plus-circle"></i> Novo equipamento</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <table id="" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="5" style="text-align: center">EQUIPAMENTOS DA OS</th>
                                    </tr>
                                    <tr>
                                        <th>Equipamento</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Série</th>
                                        <th style="width: 90px">Ações</th>
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
                                                <td>
                                                    <button type="button" class="btn btn-primary style-action" onclick="montaDadosDoEquipamento('<?= $equipamento['equipamento'] ?>', '<?= $equipamento['marca'] ?>', '<?= $equipamento['modelo'] ?>', '<?= $equipamento['serie'] ?>', '<?= $equipamento['condicoes'] ?>', '<?= $equipamento['defeitos'] ?>', '<?= $equipamento['acessorios'] ?>', '<?= $equipamento['solucao'] ?>', '<?= $equipamento['laudo_tecnico'] ?>', '<?= $equipamento['termos_de_garantia'] ?>')" data-toggle="modal" data-target="#modal-visualiza-dados-do-equipamento"><i class="fas fa-search"></i></button>
                                                    <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse equipamento?', '/ordensDeServicos/<?= (isset($acao_user)) ? 'deleteEquipamentoEdit' : 'deleteEquipamento' ?>/<?= $equipamento['id_equipamento'] ?>/<?= (isset($acao_user)) ? $id_ordem : '' ?>')"><i class="fa fa-trash"></i></button>
                                                </td>
                                            <tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5">Nenhum registro!</td>
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
                <div id="table-servico-mao-de-obra" class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Serviços/Mão de obra</h6>
                        </div><!-- /.col -->
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12" style="text-align: right">
                            <button class="btn btn-info" data-toggle="modal" data-target="#modal-add-servico-mao-de-obra" style="margin-bottom: 15px"><i class="fas fa-plus-circle"></i> Novo Serviço/Mão de obra</button>
                        </div>
                    </div>
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
                                        <th style="width: 90px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_servicos_mao_de_obra = 0; ?>

                                    <?php if(!empty($servicos_mao_de_obra_os_provisorio)): ?>
                                        <?php foreach($servicos_mao_de_obra_os_provisorio as $servico): ?>
                                            <tr>
                                                <td><?= $servico['nome'] ?></td>
                                                <td><?= $servico['quantidade'] ?></td>
                                                <td><?= $decimal_os($servico['valor']) ?></td>
                                                <td><?= $decimal_os($servico['quantidade'] * $servico['valor']) ?></td>
                                                <td><?= $decimal_os($servico['desconto']) ?></td>
                                                <td>
                                                    <?php
                                                        $total = $servico['quantidade'] * $servico['valor'] - $servico['desconto'];
                                                        echo $decimal_os($total);
                                                    ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-warning style-action" onclick="alteraDadosServicoMaoDeObra(<?= $servico['quantidade'] ?>, <?= $decimal_os($servico['valor']) ?>, <?= $decimal_os($servico['desconto']) ?>, <?= $servico['id_servico'] ?>)" data-toggle="modal" data-target="#modal-altera-dados-servico-mao-de-obra"><i class="fas fa-edit"></i></button>
                                                    <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse Serviço/Mão de Obra?', '/ordensDeServicos/<?= (isset($acao_user)) ? 'deleteServicoMaoDeObraEdit' : 'deleteServicoMaoDeObra' ?>/<?= $servico['id_servico'] ?>/<?= (isset($acao_user)) ? $id_ordem : '' ?>')"><i class="fa fa-trash"></i></button>
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
                    <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Servico/Mão de obra</label>
                                    <input type="number" step="0.01" class="form-control" id="total_servicos_mao_de_obra" value="<?= $decimal_os($total_servicos_mao_de_obra) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Frete</label>
                                    <input type="text" class="form-control" id="total_os_frete" name="frete" value="<?= str_replace('.', ',', $decimal_os($dados_ordem_de_servico['frete'])) ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Outros</label>
                                    <input type="text" class="form-control" id="total_os_outros" name="outros" value="<?= str_replace('.', ',', $decimal_os($dados_ordem_de_servico['outros'])) ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Desconto</label>
                                    <input type="text" class="form-control" id="total_os_desconto" name="desconto" value="<?= str_replace('.', ',', $decimal_os($dados_ordem_de_servico['desconto'])) ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <?php
                                        $valor_total_do_pagamento = ($total_servicos_mao_de_obra + $dados_ordem_de_servico['frete'] + $dados_ordem_de_servico['outros']) - $dados_ordem_de_servico['desconto'];
                                    ?>
                                    <input type="text" class="form-control" id="valor_total_do_pagamento" name="valor_total_do_pagamento" value="<?= str_replace('.', ',', $decimal_os($valor_total_do_pagamento)) ?>" disabled>
                                </div>
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

            <form id="form-finaliza-ou-edita-ordem-de-servico" action="/ordensDeServicos/<?= (isset($acao_user)) ? 'editDadosResponsaveis_e_DadosFinaisOrdemDeServico' : 'finalizaOrdemDeServico' ?>" method="post">
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
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Vendedor</label>
                                    <select class="form-control select2" name="id_vendedor" style="width: 100%;" required="">
                                        <?php if (!empty($vendedores)) : ?>
                                            <?php foreach ($vendedores as $vendedor) : ?>
                                                <option value="<?= $vendedor['id_vendedor'] ?>" <?= ($dados_ordem_de_servico['id_vendedor'] == $vendedor['id_vendedor']) ? 'selected' : '' ?>><?= $vendedor['nome'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Técnico</label>
                                    <select class="form-control select2" name="id_tecnico" style="width: 100%;" required="">
                                        <?php if (!empty($tecnicos)) : ?>
                                            <?php foreach ($tecnicos as $tecnico) : ?>
                                                <option value="<?= $tecnico['id_tecnico'] ?>" <?= ($dados_ordem_de_servico['id_tecnico'] == $tecnico['id_tecnico']) ? 'selected' : '' ?>><?= $tecnico['nome'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
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
                                <h6 class="m-0 text-dark"><i class="fas fa-user"></i> Dados Finais</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select class="form-control select2" name="id_cliente" style="width: 100%;" required="">
                                        <?php if (!empty($clientes)) : ?>
                                            <?php foreach ($clientes as $cliente) : ?>
                                                <option value="<?= $cliente['id_cliente'] ?>" <?= ($dados_ordem_de_servico['id_cliente'] == $cliente['id_cliente']) ? 'selected' : '' ?>><?= $cliente['nome'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Situação</label>
                                    <?php if(isset($acao_user)): ?>
                                        <select class="form-control select2" name="situacao" style="width: 100%;" required="">
                                            <?php if($dados_ordem_de_servico['situacao'] == "Em aberto"): ?>
                                                <option value="Em aberto" selected>Em aberto</option>
                                                <option value="Em andamento">Em andamento</option>
                                                <option value="Concretizada">Concretizada</option>
                                                <option value="Cancelada">Cancelada</option>
                                            <?php elseif($dados_ordem_de_servico['situacao'] == "Em andamento"): ?>
                                                <option value="Em aberto">Em aberto</option>
                                                <option value="Em andamento" selected>Em andamento</option>
                                                <option value="Concretizada">Concretizada</option>
                                                <option value="Cancelada">Cancelada</option>
                                            <?php elseif($dados_ordem_de_servico['situacao'] == "Concretizada"): ?>
                                                <option value="Em aberto">Em aberto</option>
                                                <option value="Em andamento">Em andamento</option>
                                                <option value="Concretizada" selected>Concretizada</option>
                                                <option value="Cancelada">Cancelada</option>
                                            <?php elseif($dados_ordem_de_servico['situacao'] == "Cancelada"): ?>
                                                <option value="Em aberto">Em aberto</option>
                                                <option value="Em andamento">Em andamento</option>
                                                <option value="Concretizada">Concretizada</option>
                                                <option value="Cancelada" selected>Cancelada</option>
                                            <?php endif; ?>
                                        </select>
                                    <?php else: ?>
                                        <select class="form-control select2" name="situacao" style="width: 100%;" required="">
                                            <option value="Em aberto" selected>Em aberto</option>
                                            <option value="Em andamento">Em andamento</option>
                                            <option value="Concretizada">Concretizada</option>
                                            <option value="Cancelada">Cancelada</option>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Dt Entrada</label>
                                    <input type="date" class="form-control" name="data_de_entrada" value="<?= (isset($acao_user)) ? $dados_ordem_de_servico['data_de_entrada'] : date('Y-m-d') ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Hr Entrada</label>
                                    <input type="text" class="form-control" name="hora_de_entrada" value="<?= (isset($acao_user)) ? $dados_ordem_de_servico['hora_de_entrada'] : date('H:i:s') ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Dt Saída</label>
                                    <input type="date" class="form-control" name="data_de_saida" value="<?= (isset($acao_user)) ? $dados_ordem_de_servico['data_de_saida'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Hr Saída</label>
                                    <input type="text" class="form-control" name="hora_de_saida" value="<?= (isset($acao_user)) ? $dados_ordem_de_servico['hora_de_saida'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Tipo de Atendimento</label>
                                    <?php $tipo_atendimento = (isset($acao_user) && $dados_ordem_de_servico['canal_de_venda'] == "Remoto") ? "Remoto" : "Presencial"; ?>
                                    <select class="form-control select2" name="canal_de_venda" style="width: 100%;" required="">
                                        <option value="Presencial" <?= ($tipo_atendimento == "Presencial") ? "selected" : "" ?>>Presencial</option>
                                        <option value="Remoto" <?= ($tipo_atendimento == "Remoto") ? "selected" : "" ?>>Remoto</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Observações</label>
                                    <textarea class="form-control" name="observacoes" rows="5"><?= (isset($acao_user)) ? $dados_ordem_de_servico['observacoes'] : '' ?></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Observações internas</label>
                                    <textarea class="form-control" name="observacoes_internas" rows="5"><?= (isset($acao_user)) ? $dados_ordem_de_servico['observacoes_internas'] : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ---------- HIDDENS NECESSÁRIOS PARA FINALIZAR ORDEM DE SERVIÇOS ---------- -->
                    <input type="hidden" name="frete" id="finalizar_os_frete">
                    <input type="hidden" name="outros" id="finalizar_os_outros">
                    <input type="hidden" name="desconto" id="finalizar_os_desconto">
                    <!-- -------------------------------------------------------------------------- -->

                    <!-- ------ HIDDEN PARA EDIT ----- -->
                    <?php if(isset($acao_user)): ?>
                        <input type="hidden" name="id_ordem" value="<?= $id_ordem ?>">
                    <?php endif; ?>
                    <!-- ----------------------------- -->

                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="button" class="btn btn-primary" onclick="finalizarOuEditarOdemDeServicos()"><?= (isset($acao_user)) ? '<i class="fas fa-check-circle"></i> Atualizar Dados' : '<i class="fas fa-check-circle"></i> Finalizar' ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </form>

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
            <?php if ($alert == "success_add_equipamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Equipamento adicionado com sucesso!'
                })
            <?php elseif ($alert == "success_delete_equipamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Equipamento excluido com sucesso!'
                })
            <?php elseif ($alert == "success_add_servico_mao_de_obra") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Serviço/Mão de Obra adicionado com sucesso!'
                })
            <?php elseif ($alert == "success_delete_servico_mao_de_obra") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Serviço/Mão de Obra excluido com sucesso!'
                })
            <?php elseif ($alert == "success_edit_ordem_de_servico") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Ordem de serviço atualizada com sucesso!'
                })
            <?php elseif ($alert == "success_atualiza_dados_servico_mao_de_obra") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Dados do Serviço/Mão de Obra atualizado com sucesso!'
                })
            <?php elseif ($alert == "error_finaliza_os") : ?>
                Toast.fire({
                    type: 'error',
                    title: 'Nao foi possivel finalizar a OS.'
                })
            <?php endif; ?>
        <?php endif; ?>
    });

    function finalizarOuEditarOdemDeServicos()
    {
        ['total_os_frete', 'total_os_outros', 'total_os_desconto'].forEach(formataCampoDecimalOs);
        atualizaTotalOs();

        document.getElementById('finalizar_os_frete').value = document.getElementById('total_os_frete').value;
        document.getElementById('finalizar_os_outros').value = document.getElementById('total_os_outros').value;
        document.getElementById('finalizar_os_desconto').value = document.getElementById('total_os_desconto').value;

        document.getElementById('form-finaliza-ou-edita-ordem-de-servico').submit(); // Aciona o formulário
    }

    function valorDecimalDoCampo(id)
    {
        var valor = document.getElementById(id).value.trim();

        if(valor == '')
        {
            return 0;
        }

        if(valor.indexOf(',') >= 0)
        {
            valor = valor.replace(/\./g, '').replace(',', '.');
        }

        return parseFloat(valor.replace(',', '.')) || 0;
    }

    function formataDecimalOs(valor)
    {
        return valor.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formataCampoDecimalOs(id)
    {
        document.getElementById(id).value = formataDecimalOs(valorDecimalDoCampo(id));
    }

    function atualizaTotalOs()
    {
        var total = valorDecimalDoCampo('total_servicos_mao_de_obra') + valorDecimalDoCampo('total_os_frete') + valorDecimalDoCampo('total_os_outros') - valorDecimalDoCampo('total_os_desconto');
        document.getElementById('valor_total_do_pagamento').value = formataDecimalOs(total);
    }

    ['total_os_frete', 'total_os_outros', 'total_os_desconto'].forEach(function(id) {
        var campo = document.getElementById(id);

        if(campo)
        {
            campo.addEventListener('input', atualizaTotalOs);
            campo.addEventListener('blur', function() {
                formataCampoDecimalOs(id);
                atualizaTotalOs();
            });
        }
    });

    function montaDadosDoEquipamento(equipamento, marca, modelo, serie, condicoes, defeitos, acessorios, solucao, laudo_tecnico, termos_de_garantia)
    {
        document.getElementById('visualiza_eq_equipamento').value        = equipamento;
        document.getElementById('visualiza_eq_marca').value              = marca;
        document.getElementById('visualiza_eq_modelo').value             = modelo;
        document.getElementById('visualiza_eq_serie').value              = serie;
        document.getElementById('visualiza_eq_condicoes').value          = condicoes;
        document.getElementById('visualiza_eq_defeitos').value           = defeitos;
        document.getElementById('visualiza_eq_acessorios').value         = acessorios;
        document.getElementById('visualiza_eq_solucao').value            = solucao;
        document.getElementById('visualiza_eq_laudo_tecnico').value      = laudo_tecnico;
        document.getElementById('visualiza_eq_termos_de_garantia').value = termos_de_garantia;
    }

    function alteraDadosServicoMaoDeObra(quantidade, valor, desconto, id_servico)
    {
        document.getElementById('altera_dados_servico_mao_de_obra_quantidade').value = quantidade;
        document.getElementById('altera_dados_servico_mao_de_obra_valor').value      = valor;
        document.getElementById('altera_dados_servico_mao_de_obra_desconto').value   = desconto;
        document.getElementById('altera_dados_servico_mao_de_obra_id_servico').value = id_servico;
    }

    var url = window.location.href;
    
    var nova = url.split('#');
    if(typeof nova[1] != 'undefined')
    {
        window.location.href = '#'+nova[1];
    }
</script>
