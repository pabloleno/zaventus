<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/configs/store_nfe" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
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
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">cUF</label>
                                    <input type="text" class="form-control" name="cUF" value="<?= $dados['cUF'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">natOp</label>
                                    <input type="text" class="form-control" name="natOp" value="<?= $dados['natOp'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">serie</label>
                                    <input type="text" class="form-control" name="serie" value="<?= $dados['serie'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">nNF</label>
                                    <input type="text" class="form-control" name="nNF" value="<?= $dados['nNF'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">cMunFG</label>
                                    <input type="text" class="form-control" name="cMunFG" value="<?= $dados['cMunFG'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">tpAmb</label>
                                    <input type="text" class="form-control" name="tpAmb" value="<?= $dados['tpAmb'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">verProc</label>
                                    <input type="text" class="form-control" name="verProc" value="<?= $dados['verProc'] ?>" required="">
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
                            <div class="col-sm-12">
                                <h6 class="m-0 text-dark"><i class="fas fa-edit"></i> Emitente</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">CNPJ</label>
                                    <input type="text" class="form-control" name="CNPJ" value="<?= $dados['CNPJ'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">xNome</label>
                                    <input type="text" class="form-control" name="xNome" value="<?= $dados['xNome'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">xFant</label>
                                    <input type="text" class="form-control" name="xFant" value="<?= $dados['xFant'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">IE</label>
                                    <input type="text" class="form-control" name="IE" value="<?= $dados['IE'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">CRT</label>
                                    <input type="text" class="form-control" name="CRT" value="<?= $dados['CRT'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">CEP</label>
                                    <input type="text" class="form-control" name="CEP" value="<?= $dados['CEP'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="">xLgr</label>
                                    <input type="text" class="form-control" name="xLgr" value="<?= $dados['xLgr'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">nro</label>
                                    <input type="text" class="form-control" name="nro" value="<?= $dados['nro'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="">xCpl</label>
                                    <input type="text" class="form-control" name="xCpl" value="<?= $dados['xCpl'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">xBairro</label>
                                    <input type="text" class="form-control" name="xBairro" value="<?= $dados['xBairro'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">cMun</label>
                                    <input type="text" class="form-control" name="cMun" value="<?= $dados['cMun'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">xMun</label>
                                    <input type="text" class="form-control" name="xMun" value="<?= $dados['xMun'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">UF</label>
                                    <input type="text" class="form-control" name="UF" value="<?= $dados['UF'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">cPais</label>
                                    <input type="text" class="form-control" name="cPais" value="<?= $dados['cPais'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">xPais</label>
                                    <input type="text" class="form-control" name="xPais" value="<?= $dados['xPais'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">fone</label>
                                    <input type="text" class="form-control" name="fone" value="<?= $dados['fone'] ?>" required="">
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
                            <div class="col-sm-12">
                                <h6 class="m-0 text-dark"><i class="fas fa-edit"></i> Responsável Técnico</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">CNPJ</label>
                                    <input type="text" class="form-control" name="CNPJ_responsavel_tecnico" value="<?= $dados['CNPJ_responsavel_tecnico'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">xContato</label>
                                    <input type="text" class="form-control" name="xContato" value="<?= $dados['xContato'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">email</label>
                                    <input type="text" class="form-control" name="email_responsavel_tecnico" value="<?= $dados['email_responsavel_tecnico'] ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">fone</label>
                                    <input type="text" class="form-control" name="fone_responsavel_tecnico" value="<?= $dados['fone_responsavel_tecnico'] ?>" required="">
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
                            <div class="col-sm-12">
                                <h6 class="m-0 text-dark"><i class="fas fa-edit"></i> Certificado</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="">Status Certificado</label>
                                    <input type="text" class="form-control" name="certificado" value="<?= ($dados['certificado'] == 1) ? "O certificado foi carregado!" : "Nenhum certificado ainda carregado!" ?>" disabled>
                                
                                    <div class="input-group">
                                        <input type="file" name="arquivo">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Senha</label>
                                    <input type="text" class="form-control" name="senha" value="<?= $dados['senha'] ?>" required="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </form>
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
            <?php if ($alert == "success_edit") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Dados da NFe atualizados com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>