<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <p class="mb-2"><span class="badge badge-<?= (int) ($produto['ativo'] ?? 1) === 1 ? 'success' : 'secondary' ?>"><?= (int) ($produto['ativo'] ?? 1) === 1 ? 'Ativo' : 'Inativo' ?></span></p>
            <?php if (!empty($produto['observacoes'])): ?><div class="card"><div class="card-body"><?= esc($produto['observacoes']) ?></div></div><?php endif; ?>
            <form action="/produtos/store" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= esc($titulo['icone']) ?>"></i> <?= esc($titulo['modulo']) ?></h6>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <a href="/produtos" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="">Nome</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['nome_do_produto']) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Unidade</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['unidade']) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Cód. barras</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['codigo_de_barras']) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">Localização</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['localizacao']) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Qtd</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['quantidade']) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Qtd mínima</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['quantidade_minima']) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Margem de lucro %</label>
                                    <input type="text" class="form-control" value="<?= decimal_monetario($produto['margem_de_lucro']) ?>%" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Valor de custo</label>
                                    <input type="text" class="form-control" value="<?= number_format($produto['valor_de_custo'], 2, ',', '.') ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Valor de venda</label>
                                    <input type="text" class="form-control" value="<?= number_format($produto['valor_de_venda'], 2, ',', '.') ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Lucro</label>
                                    <input type="text" class="form-control" value="<?= number_format($produto['lucro'], 2, ',', '.') ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">Validade</label>
                                    <input type="date" class="form-control" value="<?= esc($produto['validade']) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Categoria</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['nome_da_categoria_do_produto']) ?>" disabled="">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">Fornecedor</label>
                                    <input type="text" class="form-control" value="<?= esc($produto['nome_do_representante']) ?> - <?= esc($produto['nome_da_empresa']) ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="fas fa-edit"></i> Foto do Produto</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Foto do produto</label>
                                    <?php if ($produto['arquivo'] == "" || !is_file(FCPATH . 'assets/img/produtos/' . $produto['arquivo'])) : ?>
                                        <img src="<?= base_url('assets/img/produtos/produto-sem-imagem.jpg') ?>" alt="Imagem do produto" style="width: 100%">
                                    <?php else : ?>
                                        <img src="<?= base_url('assets/img/produtos/' . $produto['arquivo']) ?>" alt="Imagem do produto" style="width: 100%">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

            </form>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
