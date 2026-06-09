<div class="content-wrapper">
    <div class="content">
        <div class="container">
            <div class="card no-print">
                <div class="card-body">
                    <form action="/relatorios/faturamentoDiario" method="post">
                        <div class="row">
                            <div class="col-lg-3">
                                <label>Área do negócio</label>
                                <select class="form-control select2" name="tipo_negocio" style="width: 100%;">
                                    <?php foreach ($tipos_negocio as $valor => $rotulo) : ?>
                                        <option value="<?= $valor ?>" <?= $tipo_negocio === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label>Mês</label>
                                <select class="form-control select2" name="mes" style="width: 100%;">
                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <option value="<?= $i ?>" <?= (int) $mes === $i ? 'selected' : '' ?>><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label>Ano</label>
                                <select class="form-control select2" name="ano" style="width: 100%;">
                                    <?php for ($i = 2000; $i <= date('Y'); $i++) : ?>
                                        <option value="<?= $i ?>" <?= (int) $ano === $i ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-lg-5">
                                <button type="submit" class="btn btn-success" style="margin-top: 30px">Gerar Relatório</button>
                                <button type="button" class="btn btn-info" onclick="print()" style="margin-top: 30px"><i class="fas fa-print"></i> Imprimir</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h6 class="m-0"><i class="fas fa-list"></i> Faturamento Diário - <?= \App\Libraries\TipoNegocio::rotulo($tipo_negocio) ?></h6></div>
                <div class="card-body">
                    <?php $totalMes = array_sum($faturamentos); ?>
                    <p><b>Total do período:</b> R$ <?= number_format($totalMes, 2, ',', '.') ?></p>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th>Dia</th><th>Produtos</th><th>Serviços</th><th>Outros lançamentos</th><th>Total</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados_fat as $dados) : ?>
                                <tr>
                                    <td><?= $dados['dia'] ?></td>
                                    <td>R$ <?= number_format($dados['produtos'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($dados['servicos'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($dados['lancamentos'], 2, ',', '.') ?></td>
                                    <td><b>R$ <?= number_format($dados['total'], 2, ',', '.') ?></b></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
