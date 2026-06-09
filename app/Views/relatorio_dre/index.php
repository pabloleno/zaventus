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
            <div class="card no-print">
                <div class="card-body">
                    <form action="/relatorioDRE" method="post">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Área do negócio</label>
                                    <select class="form-control select2" name="tipo_negocio" style="width: 100%;">
                                        <?php foreach ($tipos_negocio as $valor => $rotulo) : ?>
                                            <option value="<?= $valor ?>" <?= $tipo_negocio === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Data Inicio</label>
                                    <input type="date" class="form-control" name="data_inicio" value="<?= isset($data_inicio) ? $data_inicio : date('Y-m-01') ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Data Inicio</label>
                                    <input type="date" class="form-control" name="data_final" value="<?= isset($data_final) ? $data_final : date('Y-m-31') ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <button type="submit" class="btn btn-success" style="margin-top: 30px">Gerar Relatório</button>
                                <button type="button" class="btn btn-info" onclick="print()" style="margin-top: 30px"><i class="fas fa-print"></i> Imprimir</button>
                                <!-- <a href="/inventarioDoEstoque/listaProdutos/" class="btn btn-primary"><i class="fas fa-edit"></i> Alterar/Excluir Produto</a>
                                <a href="/inventarioDoEstoque/add/" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Adicionar produto</a> -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>EMPRESA: <?= $empresa['nome_fantasia'] ?></p>
                                    <p>CNPJ: <?= $empresa['cnpj'] ?></p>
                                    <p>PERÍODO DO DRE: <?= date('d/m/Y', strtotime($data_inicio)) ?> até <?= date('d/m/Y', strtotime($data_final)) ?></p>
                                    <p>ÁREA DO NEGÓCIO: <?= \App\Libraries\TipoNegocio::rotulo($tipo_negocio) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table id="" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th colspan="2" style="text-align: center">DEMONSTRAÇÃO DE RESULTADO DO EXERCÍCIO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><b>(=) FATURAMENTO</b></td>
                                                <td><b><?= number_format($faturamento, 2, ',', '.') ?></b></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 50px">Vendas de produtos</td>
                                                <td><?= number_format($faturamento_produtos, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 50px">Vendas de serviços</td>
                                                <td><?= number_format($faturamento_servicos, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 50px">Outros lançamentos</td>
                                                <td><?= number_format($faturamento_lancamentos, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 50px">(-) Ipostos</td>
                                                <td><?= number_format($impostos, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td><b>(=) LUCRO BRUTO</b></td>
                                                <td>
                                                    <b>
                                                        <?php
                                                        $lucro_bruto = $faturamento - $impostos;
                                                        echo number_format($lucro_bruto, 2, ',', '.');
                                                        ?>
                                                    </b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 50px">(-) Despesas Variáveis</td>
                                                <td>
                                                    <?= number_format($despesas_variaveis, 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>(=) LUCRO OPERACIONAL</b></td>
                                                <td>
                                                    <b>
                                                        <?php
                                                        $lucro_operacional = $lucro_bruto - $despesas_variaveis;
                                                        echo number_format($lucro_operacional, 2, ',', '.');
                                                        ?>
                                                    </b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 50px">(-) Despesas Fixas</td>
                                                <td>
                                                    <?= number_format($despesas_fixas, 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 50px">(-) Gastos com Pessoas</td>
                                                <td>
                                                    <?= number_format($gastos_com_pessoas, 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 50px">(-) Prolabore</td>
                                                <td>
                                                    <?= number_format($prolabore, 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>(=) LUCRO LÍQUIDO</b></td>
                                                <td>
                                                    <b><?= number_format($lucro_operacional - $despesas_fixas - $gastos_com_pessoas - $prolabore, 2, ',', '.') ?></b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <!-- BAR CHART -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Relatório DRE Gráfico</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chartjs-4" class="chartjs" width="undefined" height="undefined"></canvas>

                            <script>
                                new Chart(document.getElementById("chartjs-4"), {
                                    "type": "doughnut",
                                    "data": {
                                        "labels": ["Faturamento", "Impostos", "Despesas Variáveis", "Despesas Fixas", "Gastos com Pessoas", "Prolabore"],
                                        "datasets": [{
                                            "label": "My First Dataset",
                                            "data": [<?= $faturamento . ", " . $impostos . ", " . $despesas_variaveis . ", " . $despesas_fixas . ", " . $gastos_com_pessoas . ", " . $prolabore ?>],
                                            "backgroundColor": ["rgb(40, 167, 69)", "rgb(255, 99, 132)", "rgb(204,135,20)", "rgb(242,226,5)", "rgb(255,255,82)", "rgb(54, 162, 235)", "rgb(255, 205, 86)"]
                                        }]
                                    }
                                });
                            </script>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <!-- BAR CHART -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Relatório DRE Gráfico</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chartjs-1" class="chartjs" width="undefined" height="undefined"></canvas>

                            <script>
                                new Chart(document.getElementById("chartjs-1"), {
                                    "type": "bar",
                                    "data": {
                                        "labels": ["Faturamento", "Ipostos", "Despesas Variáveis", "Despesas Fixas", "Gastos com Pessoas", "Prolabore"],
                                        "datasets": [{
                                            "label": "Faturamento",
                                            "data": [<?= $faturamento . ", " . $impostos . ", " . $despesas_variaveis . ", " . $despesas_fixas . ", " . $gastos_com_pessoas . ", " . $prolabore ?>],
                                            "fill": false,
                                            "backgroundColor": ["rgb(40, 167, 69)", "rgb(255, 99, 132)", "rgb(204,135,20)", "rgb(242,226,5)", "rgb(255,255,82)", "rgb(54, 162, 235)", "rgb(255, 205, 86)"],
                                            "borderColor": ["rgb(40, 167, 69)", "rgb(255, 99, 132)", "rgb(204,135,20)", "rgb(242,226,5)", "rgb(255,255,82)", "rgb(54, 162, 235)", "rgb(255, 205, 86)"],
                                            "borderWidth": 1
                                        }]
                                    },
                                    "options": {
                                        "scales": {
                                            "yAxes": [{
                                                "ticks": {
                                                    "beginAtZero": true
                                                }
                                            }]
                                        }
                                    }
                                });
                            </script>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Relatório gerado em: <?= date('d/m/Y') ?> às <?= date('H:i') ?></p>
                                </div>
                            </div>
                        </div>
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
            <?php if ($alert == "success_gerar_relatorio_dre") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'DRE gerado com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
