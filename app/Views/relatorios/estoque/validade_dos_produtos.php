<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container">
            <div class="card no-print">
                <div class="card-body">
                    <form action="/relatorios/validadeDosProdutos" method="post">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">Critério</label>
                                    <select class="form-control select2" name="criterio">
                                        <?php if($criterio == 0): ?>
                                            <option value="0" selected>Vencidos</option>
                                            <option value="1">Vence hoje</option>
                                            <option value="2">Vence nos próximos 3 dias</option>
                                            <option value="3">Vence nos próximos 7 dias</option>
                                            <option value="4">Vence nos próximos 15 dias</option>
                                            <option value="5">Vence nos próximos 31 dias</option>
                                        <?php elseif($criterio == 1): ?>
                                            <option value="0">Vencidos</option>
                                            <option value="1" selected>Vence hoje</option>
                                            <option value="2">Vence nos próximos 3 dias</option>
                                            <option value="3">Vence nos próximos 7 dias</option>
                                            <option value="4">Vence nos próximos 15 dias</option>
                                            <option value="5">Vence nos próximos 31 dias</option>
                                        <?php elseif($criterio == 2): ?>
                                            <option value="0">Vencidos</option>
                                            <option value="1">Vence hoje</option>
                                            <option value="2" selected>Vence nos próximos 3 dias</option>
                                            <option value="3">Vence nos próximos 7 dias</option>
                                            <option value="4">Vence nos próximos 15 dias</option>
                                            <option value="5">Vence nos próximos 31 dias</option>
                                        <?php elseif($criterio == 3): ?>
                                            <option value="0">Vencidos</option>
                                            <option value="1">Vence hoje</option>
                                            <option value="2">Vence nos próximos 3 dias</option>
                                            <option value="3" selected>Vence nos próximos 7 dias</option>
                                            <option value="4">Vence nos próximos 15 dias</option>
                                            <option value="5">Vence nos próximos 31 dias</option>
                                        <?php elseif($criterio == 4): ?>
                                            <option value="0">Vencidos</option>
                                            <option value="1">Vence hoje</option>
                                            <option value="2">Vence nos próximos 3 dias</option>
                                            <option value="3">Vence nos próximos 7 dias</option>
                                            <option value="4" selected>Vence nos próximos 15 dias</option>
                                            <option value="5">Vence nos próximos 31 dias</option>
                                        <?php elseif($criterio == 5): ?>
                                            <option value="0">Vencidos</option>
                                            <option value="1">Vence hoje</option>
                                            <option value="2">Vence nos próximos 3 dias</option>
                                            <option value="3">Vence nos próximos 7 dias</option>
                                            <option value="4">Vence nos próximos 15 dias</option>
                                            <option value="5" selected>Vence nos próximos 31 dias</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <button type="submit" class="btn btn-success" style="margin-top: 30px">Gerar Relatório</button>
                                <button type="button" class="btn btn-info" onclick="print()" style="margin-top: 30px"><i class="fas fa-print"></i> Imprimir / Salvar PDF</button>
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
                                    <h6 class="m-0 text-dark" style="text-align: center"><b><?= $titulo['modulo'] ?></b></h6>
                                    <hr>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 30px">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <p><b>EMPRESA:</b> <?= $empresa['nome_fantasia'] ?></p>
                                    <p><b>CNPJ:</b> <?= $empresa['cnpj'] ?></p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <p><b>CONTATO:</b> <?= $empresa['telefone'] ?></p>
                                    <p><b>ENDEREÇO:</b> <?= $empresa['endereco'] ?></p>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 50px">
                                <div class="col-lg-12">
                                    <table id="" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cód.</th>
                                                <th>Nome</th>
                                                <th>Un</th>
                                                <th>Cód. Barras</th>
                                                <th>Qtd</th>
                                                <th>Qtd M.</th>
                                                <th>Localização</th>
                                                <th>Validade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($produtos)): ?>
                                                <?php foreach($produtos as $produto): ?>
                                                    <tr>
                                                        <td><?= $produto['id_produto'] ?></td>
                                                        <td><?= $produto['nome'] ?></td>
                                                        <td><?= $produto['unidade'] ?></td>
                                                        <td><?= ($produto['codigo_de_barras'] != 0) ? $produto['codigo_de_barras'] : "S/N" ?></td>
                                                        <td><?= $produto['quantidade'] ?></td>
                                                        <td><?= $produto['quantidade_minima'] ?></td>
                                                        <td><?= ($produto['localizacao'] != "") ? $produto['localizacao'] : "Não Cad." ?></td>
                                                        <td><?= $produto['validade'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="10">Nenhum registro!</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <p>Relatório gerado em: <?= date('d/m/Y') ?> às <?= date('H:i') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
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
            <?php if ($alert == "success_gerar_relatorio") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Relatório gerado com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>