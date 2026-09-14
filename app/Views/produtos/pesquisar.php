<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row" style="margin-bottom: 15px">
                <div class="col-sm-6">
                    <h6 class="m-0 text-dark"><i class="<?= esc($titulo['icone']) ?>"></i> <?= esc($titulo['modulo']) ?></h6>
                </div><!-- /.col -->
                <div class="col-sm-6 no-print">
                    <ol class="breadcrumb float-sm-right">
                        <?php foreach ($caminhos as $caminho) : ?>
                            <?php if (!$caminho['active']) : ?>
                                <li class="breadcrumb-item"><a href="<?= esc($caminho['rota']) ?>"><?= esc($caminho['titulo']) ?></a></li>
                            <?php else : ?>
                                <li class="breadcrumb-item active"><?= esc($caminho['titulo']) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </div><!-- /.col -->
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="/produtos/pesquisar" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-10">
                                <div class="form-group">
                                    <label for="">Nome do Produto</label>
                                    <select class="form-control select2" id="inputNome" name="id_produto" style="width: 100%">
                                        <?php if (!empty($produtos)) : ?>
                                            <?php foreach ($produtos as $produto) : ?>
                                                <option value="<?= esc($produto['id_produto']) ?>" <?= (isset($produto_pesq) && $produto_pesq['id_produto'] == $produto['id_produto']) ? "selected" : "" ?>><?= esc($produto['nome']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success btn-block" style="margin-top: 30px">Pesquisar</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form action="/produtos/pesquisar" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Cód. de Barras</label>
                                    <input type="text" class="form-control" name="codigo_de_barras" autofocus>
                                    <p style="color: orange; font-size: 11px; text-align: right"><b>Se digitar pressione enter!</b></p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card -->

            <?php if (isset($produto_pesq)) : ?>
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= esc($titulo['icone']) ?>"></i> Dados</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Cód.</label>
                                    <input type="text" class="form-control" value="<?= esc($produto_pesq['id_produto']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="">Nome</label>
                                    <input type="text" class="form-control" value="<?= esc($produto_pesq['nome']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Unidade</label>
                                    <input type="text" class="form-control" value="<?= esc($produto_pesq['unidade']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Cód. de Barras</label>
                                    <input type="text" class="form-control" value="<?= esc($produto_pesq['codigo_de_barras']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Localização</label>
                                    <input type="text" class="form-control" value="<?= esc($produto_pesq['localizacao']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Cód. Categoria</label>
                                    <input type="text" class="form-control" value="<?= esc($produto_pesq['id_categoria']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Qtd em Estoque</label>
                                    <input type="text" class="form-control" value="<?= esc($produto_pesq['quantidade']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Qtd Mínima</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['quantidade_minima']) ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Valor de Custo</label>
                                    <input type="text" class="form-control" value="<?= number_format($produto_pesq['valor_de_custo'], 2, ',', '.') ?>" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Valor de Venda</label>
                                    <input type="text" class="form-control" value="<?= number_format($produto_pesq['valor_de_venda'], 2, ',', '.') ?>" disabled>
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
                                <h6 class="m-0 text-dark"><i class="<?= esc($titulo['icone']) ?>"></i> Foto</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <img src="<?= base_url('assets/img/produtos/' . (($produto_pesq['arquivo'] != "") ? $produto_pesq['arquivo'] : "produto-sem-imagem.jpg")) ?>" alt="" style="width: 200px">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->

            <?php endif; ?>
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
            timer: 3000
        });

        <?php
        $session = session();
        $alert = $session->getFlashdata('alert');


        if (isset($alert) && !isset($produto_pesq)) :
        ?>
            <?php if ($alert == "error_pesq_produto_por_cod_de_barras") : ?>
                Toast.fire({
                    type: 'warning',
                    title: 'Produto não encontrado!'
                })
            <?php endif; ?>
        <?php elseif (isset($alert)) : ?>
            <?php if ($alert == "success_pesquisar") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Produto encontrado!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
