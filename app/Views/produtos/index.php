<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <?php foreach ((array) (session()->getFlashdata('erros_material') ?? []) as $erro): ?><div class="alert alert-danger" role="alert"><?= esc((string) $erro) ?></div><?php endforeach; ?>
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
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="/produtos/create" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Nova matéria-prima</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            </div>
            <!-- /.card -->
            <div class="card">
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 35px">Cód.</th>
                                <th style="width: 130px">Imagem</th>
                                <th>Nome</th>
                                <th style="width: 130px">Preço</th>
                                <th style="width: 150px">Qtd. disponível</th>
                                <th style="width: 170px">Localização</th>
                                <th style="width: 160px">Cód. de Barras</th>
                                <th style="width: 110px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($produtos)) : ?>
                                <?php foreach ($produtos as $produto) : ?>
                                    <?php
                                        $arquivoProduto = basename((string) ($produto['arquivo'] ?? ''));
                                        $imagemProduto = $arquivoProduto !== '' && is_file(FCPATH . 'assets/img/produtos/' . $arquivoProduto)
                                            ? 'assets/img/produtos/' . $arquivoProduto
                                            : 'assets/img/produtos/produto-sem-imagem.jpg';
                                    ?>
                                    <tr>
                                        <td><?= $produto['id_produto'] ?></td>
                                        <td class="text-center"><img src="<?= esc(base_url($imagemProduto)) ?>" alt="Imagem do produto" class="foto-cadastro-miniatura foto-produto-miniatura"></td>
                                        <td><?= esc($produto['nome']) ?><small class="d-block"><?= esc($produto['observacoes'] ?? '') ?></small><span class="badge badge-<?= (int) ($produto['ativo'] ?? 1) === 1 ? 'success' : 'secondary' ?>"><?= (int) ($produto['ativo'] ?? 1) === 1 ? 'Ativo' : 'Inativo' ?></span></td>
                                        <td>R$ <?= number_format((float) $produto['valor_de_venda'], 2, ',', '.') ?></td>
                                        <td><?= esc($produto['quantidade']) ?> <?= esc($produto['unidade']) ?><small class="d-block text-muted">Mínimo: <?= esc($produto['quantidade_minima']) ?></small></td>
                                        <td><?= esc(trim((string) $produto['localizacao']) !== '' ? $produto['localizacao'] : 'Não cadastrada') ?></td>
                                        <td><?= esc($produto['codigo_de_barras']) ?></td>
                                        <td>
                                            <a href="/produtos/show/<?= $produto['id_produto'] ?>" class="btn btn-info style-action"><i class="fa fa-folder-open"></i></a>
                                            <a href="/produtos/edit/<?= $produto['id_produto'] ?>" class="btn btn-warning style-action"><i class="fa fa-edit"></i></a>
                                            <?php if ((int) ($produto['ativo'] ?? 1) === 1): ?><form action="/produtos/delete/<?= (int) $produto['id_produto'] ?>" method="post" class="d-inline" onsubmit="return confirm('Inativar esta matéria-prima? O histórico será preservado.');"><?= csrf_field() ?><button type="submit" class="btn btn-outline-secondary style-action" title="Inativar matéria-prima"><i class="fa fa-ban"></i></button></form><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
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
            <?php if ($alert == "success_create") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Produto cadastrado com sucesso!'
                })
            <?php elseif ($alert == "success_inactivate") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Matéria-prima inativada. Para reativar, edite a situação.'
                })
            <?php elseif ($alert == "success_create_prod_por_xml") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Produtos do XML cadastrados com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
