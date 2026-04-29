<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/clientes/store" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <?php if(isset($cliente)): ?>
                                        <a href="/clientes/show/<?= $cliente['id_cliente'] ?>" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
                                    <?php else: ?>
                                        <a href="/clientes" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
                                    <?php endif; ?>
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
                            <?php if (isset($cliente)) : ?>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <select class="form-control select2" id="tipo" name="tipo" style="width: 100%;" onchange="alteraTipoDoCliente()">
                                            <?php if ($cliente['tipo'] == 1) : ?>
                                                <option value="1" selected="">Pessoa Física</option>
                                                <option value="2">Pessoa Jurídica</option>
                                            <?php else : ?>
                                                <option value="1">Pessoa Física</option>
                                                <option value="2" selected="">Pessoa Jurídica</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <select class="form-control select2" id="tipo" name="tipo" style="width: 100%;" onchange="alteraTipoDoCliente()">
                                            <option value="1" selected="">Pessoa Física</option>
                                            <option value="2">Pessoa Jurídica</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?= (isset($cliente)) ? $cliente['nome'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Data de nascimento</label>
                                    <input type="text" class="form-control" id="data_de_nascimento" name="data_de_nascimento" value="<?= (isset($cliente)) ? $cliente['data_de_nascimento'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">RG</label>
                                    <input type="text" class="form-control" id="rg" name="rg" value="<?= (isset($cliente)) ? $cliente['rg'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">CPF</label>
                                    <input type="text" class="form-control" id="cpf" name="cpf" value="<?= (isset($cliente)) ? $cliente['cpf'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="">Razão social</label>
                                    <input type="text" class="form-control" id="razao_social" name="razao_social" value="<?= (isset($cliente)) ? $cliente['razao_social'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Nome fantasia</label>
                                    <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia" value="<?= (isset($cliente)) ? $cliente['nome_fantasia'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">CNPJ</label>
                                    <input type="text" class="form-control" id="cnpj" name="cnpj" value="<?= (isset($cliente)) ? $cliente['cnpj'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">IE</label>
                                    <input type="text" class="form-control" id="ie" name="ie" value="<?= (isset($cliente)) ? $cliente['ie'] : "" ?>" required="">
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
                                    <input type="text" class="form-control" name="cep" value="<?= (isset($cliente)) ? $cliente['cep'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Logradouro</label>
                                    <input type="text" class="form-control" name="logradouro" value="<?= (isset($cliente)) ? $cliente['logradouro'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="">Número</label>
                                    <input type="text" class="form-control" name="numero" value="<?= (isset($cliente)) ? $cliente['numero'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label for="">Complemento</label>
                                    <input type="text" class="form-control" name="complemento" value="<?= (isset($cliente)) ? $cliente['complemento'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Bairro</label>
                                    <input type="text" class="form-control" name="bairro" value="<?= (isset($cliente)) ? $cliente['bairro'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Municipio</label>
                                    <select class="form-control select2" name="municipio" style="width: 100%;">
                                        <?php foreach($municipios as $municipio): ?>
                                            <option value="<?= $municipio['codigo'] ?>;<?= $municipio['municipio'] ?>" <?= (!empty($cliente) && $cliente['codigo_do_municipio'] == $municipio['codigo']) ? "selected" : "" ?>><?= $municipio['municipio'] ?></option>
                                        <?php endforeach?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="">UF</label>
                                    <input type="text" class="form-control" name="UF" value="<?= (isset($cliente)) ? $cliente['UF'] : "" ?>">
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
                                    <input type="text" class="form-control" name="celular" value="<?= (isset($cliente)) ? $cliente['celular'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Comercial</label>
                                    <input type="text" class="form-control" name="comercial" value="<?= (isset($cliente)) ? $cliente['comercial'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Residencial</label>
                                    <input type="text" class="form-control" name="residencial" value="<?= (isset($cliente)) ? $cliente['residencial'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">E-mail</label>
                                    <input type="text" class="form-control" name="email" value="<?= (isset($cliente)) ? $cliente['email'] : "" ?>">
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
                                    <textarea class="form-control" name="anotacoes" rows="10"><?= (isset($cliente)) ? $cliente['anotacoes'] : "" ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <?php if (isset($cliente)) : ?>
                    <!-- HIDDENS -->
                    <input type="hidden" class="form-control" name="id_cliente" value="<?= $cliente['id_cliente'] ?>">
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary"><?= (isset($cliente)) ? "Atualizar" : "Cadastrar" ?></button>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                </div>
                <!-- /.card -->
            </form>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    function alteraTipoDoCliente() {
        tipo = document.getElementById('tipo').value;

        if (tipo == 1) {
            // Reabilita campos PESSOA FÍSICA
            document.getElementById('nome').disabled = false;
            document.getElementById('data_de_nascimento').disabled = false;
            document.getElementById('rg').disabled = false;
            document.getElementById('cpf').disabled = false;

            // Desabilita campos PESSOA JURÍDICA
            document.getElementById('razao_social').disabled = true;
            document.getElementById('nome_fantasia').disabled = true;
            document.getElementById('cnpj').disabled = true;
            document.getElementById('ie').disabled = true;
        } else {
            // Desabilita campos PESSOA FÍSICA
            document.getElementById('nome').disabled = true;
            document.getElementById('data_de_nascimento').disabled = true;
            document.getElementById('rg').disabled = true;
            document.getElementById('cpf').disabled = true;

            // Reabilita os campos para uso PESSOA JURÍDICA
            document.getElementById('razao_social').disabled = false;
            document.getElementById('nome_fantasia').disabled = false;
            document.getElementById('cnpj').disabled = false;
            document.getElementById('ie').disabled = false;
        }
    }

    alteraTipoDoCliente();
</script>