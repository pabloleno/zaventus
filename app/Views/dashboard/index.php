<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <?php $session = session() ?>
                    <h1 class="m-0 text-dark">Seja bem vindo <b><?= $session->get('primeiro_nome') ?></b>!</h1>
                </div><!-- /.col -->
                <!-- <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Starter Page</li>
                    </ol>
                </div> -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-2 col-sm-6 col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?= esc(lang('App.dashboard.sales')) ?> <?= date('m/Y') ?></span>
                                    <span class="info-box-number"><?= $total_de_vendas ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-2 col-sm-6 col-12">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-comments"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?= esc(lang('App.dashboard.productRevenue')) ?> <?= date('m/Y') ?></span>
                                    <span class="info-box-number"><?= number_format($faturamento_produtos, 2, ',', '.') ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-2 col-sm-6 col-12">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-comments"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?= esc(lang('App.dashboard.orders')) ?> <?= date('m/Y') ?></span>
                                    <span class="info-box-number"><?= $total_de_pedidos ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-2 col-sm-6 col-12">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-comments"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?= esc(lang('App.dashboard.quotes')) ?> <?= date('m/Y') ?></span>
                                    <span class="info-box-number"><?= $total_de_orcamentos ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-2 col-sm-6 col-12">
                            <div class="info-box" style="background: #20c997; color: white">
                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?= esc(lang('App.dashboard.completedServiceOrders')) ?> <?= date('m/Y') ?></span>
                                    <span class="info-box-number"><?= $total_de_orcamentos_concretizados ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-2 col-sm-6 col-12">
                            <div class="info-box" style="background: #6f42c1; color: white">
                                <span class="info-box-icon"><i class="fas fa-tools"></i></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?= esc(lang('App.dashboard.serviceRevenue')) ?> <?= date('m/Y') ?></span>
                                    <span class="info-box-number"><?= number_format($faturamento_servicos, 2, ',', '.') ?></span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <div class="col-lg-6">
                    <!-- BAR CHART -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Faturamento de Produtos em <?= date('Y') ?></h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chartjs-0" class="chartjs" width="undefined" height="undefined"></canvas>

                            <script>
                                new Chart(document.getElementById("chartjs-0"), {
                                    "type": "line",
                                    "data": {
                                        "labels": ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                                        "datasets": [{
                                            "label": "Produtos",
                                            "data": [
                                                <?php
                                                if (!empty($faturamentos_produtos)) {
                                                    foreach ($faturamentos_produtos as $faturamento) {
                                                        echo $faturamento . ", ";
                                                    }
                                                }
                                                ?>
                                            ],
                                            "fill": false,
                                            "borderColor": "rgb(75, 192, 192)",
                                            "lineTension": 0.1
                                        }]
                                    },
                                    "options": {}
                                });
                            </script>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-lg-6">
                    <!-- BAR CHART -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Faturamento de Servicos Concretizados em <?= date('Y') ?></h3>

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
                                    "type": "line",
                                    "data": {
                                        "labels": ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                                        "datasets": [{
                                            "label": "Servicos",
                                            "data": [
                                                <?php
                                                if (!empty($faturamentos_servicos)) {
                                                    foreach ($faturamentos_servicos as $faturamento) {
                                                        echo $faturamento . ", ";
                                                    }
                                                }
                                                ?>
                                            ],
                                            "fill": false,
                                            "borderColor": "rgb(255, 99, 132)",
                                            "lineTension": 0.1
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
                    <!-- BAR CHART -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Contas à Receber em <?= date('m/Y') ?> (Abertas e Vencidas)</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cód</th>
                                                <th>Status</th>
                                                <th>Descrição</th>
                                                <th>Vencimento</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($contas_a_receber_do_mes_atual)) : ?>
                                                <?php foreach ($contas_a_receber_do_mes_atual as $conta) : ?>
                                                    <tr>
                                                        <td><?= $conta['id_conta'] ?></td>
                                                        <td><?= $conta['status'] ?></td>
                                                        <td><?= $conta['nome'] ?></td>
                                                        <td><?= date('d/m/Y', strtotime($conta['data_de_vencimento'])) ?></td>
                                                        <td><?= number_format($conta['valor'], 2, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
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
                        <!-- <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-12" style="text-align: right">
                                    <button type="button" class="btn btn-info">Gerar Relatório</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-lg-12">
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Contas à Pagar em <?= date('m/Y') ?> (Abertas e Vencidas)</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cód</th>
                                                <th>Status</th>
                                                <th>Descrição</th>
                                                <th>Vencimento</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($contas_a_pagar_do_mes_atual)) : ?>
                                                <?php foreach ($contas_a_pagar_do_mes_atual as $conta) : ?>
                                                    <tr>
                                                        <td><?= $conta['id_conta'] ?></td>
                                                        <td><?= $conta['status'] ?></td>
                                                        <td><?= $conta['nome'] ?></td>
                                                        <td><?= date('d/m/Y', strtotime($conta['data_de_vencimento'])) ?></td>
                                                        <td><?= number_format($conta['valor'], 2, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
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
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">
    $(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        <?php if (!empty($produtos)) : ?>
            $(document).Toasts('create', {
                class: 'bg-default',
                title: 'Produtos do Estoque',
                // subtitle: 'Atenção',
                body: 'Existem produtos no estoque que precisam de reposição. <a href="/produtos">Acessar Relatório</a>'
            })
        <?php endif; ?>

        <?php if (!empty($caixas)) : ?>
            $(document).Toasts('create', {
                class: 'bg-default',
                title: 'Caixas Abertos',
                // subtitle: 'Atenção',
                body: 'Existem caixas abertos. <a href="/caixas">Ver Caixas</a>'
            })
        <?php endif; ?>

        <?php if (!empty($contas_a_pagar)) : ?>
            // $(document).Toasts('create', {
            //     class: 'bg-default',
            //     title: 'Contas à Pagar',
            //     // subtitle: 'Atenção',
            //     body: 'Existem contas à pagar que necessitam de atenção. <a href="/contasPagar">Ver Contas</a>'
            // })
        <?php endif; ?>

        <?php if (!empty($contas_a_receber)) : ?>
            // $(document).Toasts('create', {
            //     class: 'bg-default',
            //     title: 'Contas à Receber',
            //     // subtitle: 'Atenção',
            //     body: 'Existem contas à receber que necessitam de atenção. <a href="/contasReceber">Ver Contas</a>'
            // })
        <?php endif; ?>
    });

    $(function() {
        //-------------
        //- DONUT CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
        var donutData = {
            labels: [
                'Receitas',
                'Despesas'
            ],
            datasets: [{
                data: [<?= $receitas['valor'] ?>, <?= $despesas['valor'] ?>],
                backgroundColor: ['#00a65a', '#f56954'],
            }]
        }
        var donutOptions = {
            maintainAspectRatio: false,
            responsive: true,
        }
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        var donutChart = new Chart(donutChartCanvas, {
            type: 'doughnut',
            data: donutData,
            options: donutOptions
        })

        //-------------
        //- BAR CHART -
        //-------------
        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        var barChartData = jQuery.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        var temp1 = areaChartData.datasets[1]
        barChartData.datasets[0] = temp1
        barChartData.datasets[1] = temp0

        var barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false
        }

        var barChart = new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        })
    })

    $(function() {
        // -------------- ALERTAS ---------------- //
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
            <?php if ($alert == "success_autentication") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Seja bem vindo <?= $session->get('primeiro_nome') ?>!'
                })
            <?php elseif ($alert == "success_bkp_database") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Backup de <?= $session->get('nome_fantasia') ?> realizado com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
