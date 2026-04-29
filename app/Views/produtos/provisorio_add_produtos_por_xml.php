<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <a href="/produtos" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                </div>
                <!-- /.card-header -->
            </div>
            <!-- /.card -->
            
            <?php $num = 0; ?>
            <?php foreach($produtos as $produto): ?>
                <?php $num++; ?>

                <form action="/produtos/altera_dados_do_produto_provisorio_cad_por_xml" method="post" enctype="multipart/form-data">
                    <div id="<?= "prod_{$produto['id_produto']}" ?>" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h6 class="m-0 text-dark"><b>Produto Nº <?= $num ?></b></h6>
                                </div><!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="form-group">
                                        <label for="">Nome</label>
                                        <input type="text" class="form-control" name="nome" value="<?= $produto['nome'] ?>" required="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Unidade</label>
                                        <select class="form-control select2" name="unidade" style="width: 100%;">
                                            <?php if($produto['unidade'] == "UN"): ?>
                                                <option value="UN" selected="selected">UN</option>
                                                <option value="PCT">PCT</option>
                                                <option value="FRD">FRD</option>
                                            <?php elseif($produto['unidade'] == "PCT"): ?>
                                                <option value="UN">UN</option>
                                                <option value="PCT" selected="selected">PCT</option>
                                                <option value="FRD">FRD</option>
                                            <?php else: ?>
                                                <option value="UN">UN</option>
                                                <option value="PCT">PCT</option>
                                                <option value="FRD" selected="selected">FRD</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Cód. barras</label>
                                        <input type="text" class="form-control" name="codigo_de_barras" value="<?= $produto['codigo_de_barras'] ?>" required="">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="">Localização</label>
                                        <input type="text" class="form-control" name="localizacao" value="<?= $produto['localizacao'] ?>">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="">Qtd</label>
                                        <input type="text" class="form-control" name="quantidade" value="<?= $produto['quantidade'] ?>" required="">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="">Qtd mínima</label>
                                        <input type="text" class="form-control" name="quantidade_minima" value="<?= $produto['quantidade_minima'] ?>" required="">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Valor de custo</label>
                                        <input type="text" class="form-control" id="valor_de_custo" name="valor_de_custo" value="<?= $produto['valor_de_custo'] ?>" required="">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Valor de Venda</label>
                                        <input type="text" class="form-control" id="valor_de_venda" name="valor_de_venda" value="<?= $produto['valor_de_venda'] ?>" required="">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Validade</label>
                                        <input type="date" class="form-control" name="validade" value="<?= $produto['validade'] ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">NCM</label>
                                        <input type="text" class="form-control" name="NCM" value="<?= $produto['NCM'] ?>" required="">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">CSOSN</label>
                                        <input type="text" class="form-control" name="CSOSN" value="<?= $produto['CSOSN'] ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">CFOP</label>
                                        <input type="text" class="form-control" name="CFOP" value="<?= $produto['CFOP'] ?>" required="">
                                    </div>
                                </div>

                                <!-- HIDDEN -->
                                <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">
                                <!-- ------ -->

                            </div>
                            <div class="row">
                                <div class="col-lg-12" style="text-align: right">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </form>
            <?php endforeach?>

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12" style="text-align: right">
                            <a href="/produtos/finalizar_e_cadastrar_produtos_por_xml" class="btn btn-success"><i class="fas fa-save"></i> Finalizar e Cadastrar Produtos</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
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
            <?php if ($alert == "success_update_prod") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Dados atualizados com sucesso!'
                })
            <?php elseif ($alert == "success_edit") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Produto atualizado com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });

    var url = window.location.href;
    
    var nova = url.split('#');
    if(typeof nova[1] != 'undefined')
    {
        window.location.href = '#'+nova[1];
    }
</script>