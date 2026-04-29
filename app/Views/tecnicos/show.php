<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row" style="margin-bottom: 15px">
                <div class="col-sm-12 no-print">
                    <ol class="breadcrumb float-sm-right">
                        <a href="/tecnicos" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                            <a href="/tecnicos/edit/<?= $tecnico['id_tecnico'] ?>" class="btn btn-warning"><i class="fa fa-edit"></i> Editar</a>
                            <button type="button" class="btn btn-danger" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse Técnico? Essa ação não poderá ser desfeita!', '/tecnicos/delete/<?= $tecnico['id_tecnico'] ?>')"><i class="fa fa-trash"></i> Excluir</button>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            </div>
            <!-- /.card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex p-0">
                            <h2 class="card-title p-3">
                                Dados do Técnico
                            </h2>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="form-group">
                                        <label for="">Nome</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['nome'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Data de nascimento</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['data_de_nascimento'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">RG</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['rg'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">CPF</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['cpf'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Sexo</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['sexo'] ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                    <!-- ./card -->
                    <div class="card">
                        <div class="card-header d-flex p-0">
                            <h2 class="card-title p-3">
                                Dados do Endereço
                            </h2>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">CEP</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['cep'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Logradouro</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['logradouro'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="">Número</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['numero'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="">Complemento</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['complemento'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Bairro</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['bairro'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Cidade</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['cidade'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">UF</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['uf'] ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                    <!-- ./card -->
                    <div class="card">
                        <div class="card-header d-flex p-0">
                            <h2 class="card-title p-3">
                                Dados de Contato
                            </h2>
                        </div><!-- /.card-header -->
                        <div class="card-body">                            
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Fixo</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['fixo'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Celular 1</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['celular_1'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Celular 2</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['celular_2'] ?>" disabled="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">E-mail</label>
                                        <input type="text" class="form-control" value="<?= $tecnico['email'] ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                    <!-- ./card -->
                    <div class="card">
                        <div class="card-header d-flex p-0">
                            <h2 class="card-title p-3">
                                <i class="fa fa-info-circle"></i> Extra
                            </h2>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="">Observações</label>
                                        <textarea class="form-control" rows="10" disabled=""><?= $tecnico['observacoes'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.card-body -->
                    </div>
                    <!-- ./card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <!-- END CUSTOM TABS -->
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
            <?php if ($alert == "success_create_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Pagamento do tecnico cadastrado com sucesso!'
                })
            <?php elseif ($alert == "success_edit_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Pagamento do tecnico atualizado com sucesso!'
                })
            <?php elseif ($alert == "success_delete_pagamento") : ?>
                Toast.fire({
                    type: 'success',
                    title: 'Pagamento do tecnico excluido com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>