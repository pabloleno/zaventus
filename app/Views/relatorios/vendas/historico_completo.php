<div class="content-wrapper">
    <div class="content">
        <div class="container">
            <div class="card no-print">
                <div class="card-body">
                    <form action="/relatorios/historicoCompleto" method="post">
                        <div class="row">
                            <div class="col-lg-3">
                                <label>Área do negócio</label>
                                <select class="form-control select2" name="tipo_negocio" style="width: 100%;">
                                    <?php foreach ($tipos_negocio as $valor => $rotulo) : ?>
                                        <option value="<?= $valor ?>" <?= $tipo_negocio === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Data Início</label>
                                <input type="date" class="form-control" name="data_inicio" value="<?= $data_inicio ?>">
                            </div>
                            <div class="col-lg-3">
                                <label>Data Final</label>
                                <input type="date" class="form-control" name="data_final" value="<?= $data_final ?>">
                            </div>
                            <div class="col-lg-3">
                                <button type="submit" class="btn btn-success" style="margin-top: 30px">Gerar Relatório</button>
                                <button type="button" class="btn btn-info" onclick="print()" style="margin-top: 30px"><i class="fas fa-print"></i> Imprimir</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="text-center"><b><?= $titulo['modulo'] ?></b></h6>
                    <p class="text-center">Área: <b><?= \App\Libraries\TipoNegocio::rotulo($tipo_negocio) ?></b> | Período: <?= $data_inicio ?> até <?= $data_final ?></p>

                    <?php if (in_array($tipo_negocio, ['Todos', 'Produtos'], true)) : ?>
                        <h6 style="margin-top: 30px"><b>Vendas de Produtos</b></h6>
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>Cód.</th><th>Data</th><th>Hora</th><th>Cliente</th><th>Valor</th><th>Caixa</th></tr></thead>
                            <tbody>
                                <?php if (!empty($vendas)) : foreach ($vendas as $venda) : ?>
                                    <tr>
                                        <td><?= $venda['id_venda'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($venda['data'])) ?></td>
                                        <td><?= $venda['hora'] ?></td>
                                        <td><?= $venda['nome_cliente'] ?? $venda['id_cliente'] ?></td>
                                        <td>R$ <?= number_format($venda['valor_a_pagar'], 2, ',', '.') ?></td>
                                        <td><?= $venda['id_caixa'] ?></td>
                                    </tr>
                                <?php endforeach; else : ?>
                                    <tr><td colspan="6">Nenhuma venda de produto encontrada.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <?php if (in_array($tipo_negocio, ['Todos', 'Servicos'], true)) : ?>
                        <h6 style="margin-top: 30px"><b>Vendas de Serviços</b></h6>
                        <table class="table table-bordered table-striped">
                            <thead><tr><th>OS</th><th>Saída</th><th>Hora</th><th>Cliente</th><th>Valor</th><th>Situação</th></tr></thead>
                            <tbody>
                                <?php if (!empty($ordens_servicos)) : foreach ($ordens_servicos as $ordem) : ?>
                                    <tr>
                                        <td><?= $ordem['id_ordem'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($ordem['data_de_saida'])) ?></td>
                                        <td><?= $ordem['hora_de_saida'] ?></td>
                                        <td><?= $ordem['nome_cliente'] ?? $ordem['id_cliente'] ?></td>
                                        <td>R$ <?= number_format($ordem['valor_total'], 2, ',', '.') ?></td>
                                        <td><?= $ordem['situacao'] ?></td>
                                    </tr>
                                <?php endforeach; else : ?>
                                    <tr><td colspan="6">Nenhuma venda de serviço encontrada.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
