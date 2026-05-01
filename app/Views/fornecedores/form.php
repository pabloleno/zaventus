<?php
    use App\Libraries\ContatoPadrao;

    $fornecedorContato = $fornecedor ?? [];
    $fornecedorCelular = ContatoPadrao::primeiroValor($fornecedorContato, ['celular']);
    $fornecedorWhatsapp = ContatoPadrao::primeiroValor($fornecedorContato, ['whatsapp']);
    $fornecedorTelefoneFixo = ContatoPadrao::primeiroValor($fornecedorContato, ['telefone_fixo', 'comercial']);
    $fornecedorWhatsappLink = ContatoPadrao::whatsappLink($fornecedorWhatsapp);
    $fornecedorUfSelecionada = strtoupper(trim((string) ($fornecedorContato['UF'] ?? '')));
    $fornecedorCodigoMunicipioSelecionado = preg_replace('/\D/', '', (string) ($fornecedorContato['codigo_do_municipio'] ?? ''));
    $fornecedorMunicipioSelecionado = trim((string) ($fornecedorContato['municipio'] ?? ''));
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/fornecedores/store" method="post">
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
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="">Nome do Representante</label>
                                    <input type="text" class="form-control" id="nome" name="nome_do_representante" value="<?= (isset($fornecedor)) ? $fornecedor['nome_do_representante'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">CNPJ</label>
                                    <input type="number" class="form-control" id="nome" name="cnpj" value="<?= (isset($fornecedor)) ? $fornecedor['cnpj'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">IE</label>
                                    <input type="number" class="form-control" id="nome" name="ie" value="<?= (isset($fornecedor)) ? $fornecedor['ie'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="">Nome da Empresa</label>
                                    <input type="text" class="form-control" id="nome" name="nome_da_empresa" value="<?= (isset($fornecedor)) ? $fornecedor['nome_da_empresa'] : "" ?>" required="">
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
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="cep">CEP</label>
                                    <input type="text" class="form-control" id="cep" name="cep" value="<?= (isset($fornecedor)) ? $fornecedor['cep'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="logradouro">Endere&ccedil;o</label>
                                    <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?= (isset($fornecedor)) ? $fornecedor['logradouro'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="numero">N&deg;</label>
                                    <input type="text" class="form-control" id="numero" name="numero" value="<?= (isset($fornecedor)) ? $fornecedor['numero'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="complemento">Complemento</label>
                                    <input type="text" class="form-control" id="complemento" name="complemento" value="<?= (isset($fornecedor)) ? $fornecedor['complemento'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?= (isset($fornecedor)) ? $fornecedor['bairro'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="UF">Estado</label>
                                    <select class="form-control select2" id="UF" name="UF" style="width: 100%;">
                                        <option value="">UF</option>
                                        <?php foreach ($ufs as $uf) : ?>
                                            <option value="<?= $uf['UF'] ?>" <?= ($fornecedorUfSelecionada === $uf['UF']) ? "selected" : "" ?>><?= $uf['UF'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="cidade">Cidade</label>
                                    <select class="form-control select2" id="cidade" name="codigo_do_municipio" data-codigo-selecionado="<?= $fornecedorCodigoMunicipioSelecionado ?>" data-municipio-selecionado="<?= esc($fornecedorMunicipioSelecionado) ?>" style="width: 100%;">
                                        <option value="">Selecione o estado</option>
                                    </select>
                                    <input type="hidden" id="municipio" name="municipio" value="<?= esc($fornecedorMunicipioSelecionado) ?>">
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
                                    <label for="celular">Celular</label>
                                    <input type="text" class="form-control" id="celular" name="celular" value="<?= esc($fornecedorCelular) ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="whatsapp">Whatsapp</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?= esc($fornecedorWhatsapp) ?>">
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
                                    <label for="telefone_fixo">Telefone Fixo</label>
                                    <input type="text" class="form-control" id="telefone_fixo" name="telefone_fixo" value="<?= esc($fornecedorTelefoneFixo) ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">E-mail</label>
                                    <input type="text" class="form-control" name="email" value="<?= (isset($fornecedor)) ? $fornecedor['email'] : "" ?>">
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
                                    <textarea class="form-control" name="anotacoes" rows="10"><?= (isset($fornecedor)) ? $fornecedor['anotacoes'] : "" ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <?php if (isset($fornecedor)) : ?>
                    <!-- HIDDENS -->
                    <input type="hidden" class="form-control" name="id_fornecedor" value="<?= $fornecedor['id_fornecedor'] ?>">
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary"><?= (isset($fornecedor)) ? "Atualizar" : "Cadastrar" ?></button>
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
    $(function () {
        configuraEnderecoPadrao({
            municipiosUrl: <?= json_encode(rtrim(base_url('fornecedores/municipiosPorUf'), '/')) ?>
        });
    });
</script>
