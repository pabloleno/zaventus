<?php
    use App\Libraries\ContatoPadrao;

    $fornecedorContato = $fornecedor ?? [];
    $fornecedorCelular = ContatoPadrao::primeiroValor($fornecedorContato, ['celular']);
    $fornecedorWhatsapp = ContatoPadrao::primeiroValor($fornecedorContato, ['whatsapp']);
    $fornecedorTelefoneFixo = ContatoPadrao::primeiroValor($fornecedorContato, ['telefone_fixo', 'comercial']);
    $fornecedorWhatsappLink = ContatoPadrao::whatsappLink($fornecedorWhatsapp);
?>

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
                                <a href="/fornecedores" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                        <?= view('components/foto_cadastro', ['foto' => $fornecedor['foto'] ?? '']) ?>
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="">Nome do Representante</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['nome_do_representante'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">CNPJ</label>
                                <input type="number" class="form-control" value="<?= $fornecedor['cnpj'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">IE</label>
                                <input type="number" class="form-control" value="<?= $fornecedor['ie']  ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="">Nome da Empresa</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['nome_da_empresa'] ?>" disabled>
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
                        <div class="col-lg-12">
                            <h6><i class="fa fa-home"></i> Endereço</h6>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">CEP</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['cep'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Logradouro</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['logradouro'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="">Número</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['numero'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="">Complemento</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['complemento'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Bairro</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['bairro'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Cidade</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['municipio'] ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <label for="">Estado</label>
                                <input type="text" class="form-control" value="<?= esc($fornecedor['UF'] ?? '') ?>" disabled>
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
                        <div class="col-lg-12">
                            <h6><i class="fa fa-phone-square"></i> Contato</h6>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Celular</label>
                                <input type="text" class="form-control" name="celular" value="<?= esc($fornecedorCelular) ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Whatsapp</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="whatsapp" value="<?= esc($fornecedorWhatsapp) ?>" disabled>
                                    <?php if ($fornecedorWhatsappLink !== '') : ?>
                                        <div class="input-group-append">
                                            <a class="btn btn-success" href="<?= esc($fornecedorWhatsappLink) ?>" target="_blank" rel="noopener">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Telefone Fixo</label>
                                <input type="text" class="form-control" name="telefone_fixo" value="<?= esc($fornecedorTelefoneFixo) ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">E-mail</label>
                                <input type="text" class="form-control" value="<?= $fornecedor['email'] ?>" disabled>
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
                        <div class="col-lg-12">
                            <h6><i class="fa fa-info-circle"></i> Extra</h6>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Anotações</label>
                                <textarea class="form-control" rows="10" disabled><?= $fornecedor['anotacoes'] ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
