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
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2 col-sm-6 col-12">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Vendas <?= date('m/Y') ?></span>
                            <span class="info-box-number"><?= $total_de_vendas ?></span>
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
                            <span class="info-box-text">Pedidos <?= date('m/Y') ?></span>
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
                            <span class="info-box-text">Orcam. <?= date('m/Y') ?></span>
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
                            <span class="info-box-text">OS Concret. <?= date('m/Y') ?></span>
                            <span class="info-box-number"><?= $total_de_orcamentos_concretizados ?></span>
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
                            <span class="info-box-text">Fat. Produtos <?= date('m/Y') ?></span>
                            <span class="info-box-number"><?= number_format($faturamento_produtos, 2, ',', '.') ?></span>
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
                            <span class="info-box-text">Fat. Servicos <?= date('m/Y') ?></span>
                            <span class="info-box-number"><?= number_format($faturamento_servicos, 2, ',', '.') ?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-lg-6">
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
                                    "options": {}
                                });
                            </script>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
