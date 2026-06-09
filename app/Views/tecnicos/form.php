<?php
    use App\Libraries\ContatoPadrao;

    $tecnicoContato = $tecnico ?? [];
    $tecnicoCelular = ContatoPadrao::primeiroValor($tecnicoContato, ['celular', 'celular_1']);
    $tecnicoWhatsapp = ContatoPadrao::primeiroValor($tecnicoContato, ['whatsapp', 'celular_2']);
    $tecnicoTelefoneFixo = ContatoPadrao::primeiroValor($tecnicoContato, ['telefone_fixo', 'fixo']);
    $tecnicoWhatsappLink = ContatoPadrao::whatsappLink($tecnicoWhatsapp);
    $tecnicoUfSelecionada = strtoupper(trim((string) ($tecnicoContato['uf'] ?? '')));
    $tecnicoCodigoMunicipioSelecionado = preg_replace('/\D/', '', (string) ($tecnicoContato['codigo_do_municipio'] ?? ''));
    $tecnicoCidadeSelecionada = trim((string) ($tecnicoContato['cidade'] ?? ''));
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/tecnicos/store" method="post" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <?php if(isset($tecnico)): ?>
                                        <a href="/tecnicos/show/<?= $tecnico['id_tecnico'] ?>" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
                                    <?php else: ?>
                                        <a href="/tecnicos" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
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
                            <?= view('components/foto_cadastro', ['foto' => $tecnico['foto'] ?? '', 'editavel' => true]) ?>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" value="<?= (isset($tecnico)) ? $tecnico['nome'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Data de nascimento</label>
                                    <input type="date" class="form-control" id="data_de_nascimento" name="data_de_nascimento" value="<?= (isset($tecnico)) ? $tecnico['data_de_nascimento'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">RG</label>
                                    <input type="text" class="form-control" id="rg" name="rg" value="<?= (isset($tecnico)) ? $tecnico['rg'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">CPF</label>
                                    <input type="text" class="form-control" id="cpf" name="cpf" value="<?= (isset($tecnico)) ? $tecnico['cpf'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Sexo</label>
                                    <input type="text" class="form-control" name="sexo" value="<?= (isset($tecnico)) ? $tecnico['sexo'] : "" ?>" required="">
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
                                    <input type="text" class="form-control" id="cep" name="cep" value="<?= (isset($tecnico)) ? $tecnico['cep'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="logradouro">Endere&ccedil;o</label>
                                    <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?= (isset($tecnico)) ? $tecnico['logradouro'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="numero">N&deg;</label>
                                    <input type="text" class="form-control" id="numero" name="numero" value="<?= (isset($tecnico)) ? $tecnico['numero'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="complemento">Complemento</label>
                                    <input type="text" class="form-control" id="complemento" name="complemento" value="<?= (isset($tecnico)) ? $tecnico['complemento'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?= (isset($tecnico)) ? $tecnico['bairro'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="UF">Estado</label>
                                    <select class="form-control select2" id="UF" name="uf" style="width: 100%;">
                                        <option value="">UF</option>
                                        <?php foreach ($ufs as $uf) : ?>
                                            <option value="<?= $uf['UF'] ?>" <?= ($tecnicoUfSelecionada === $uf['UF']) ? "selected" : "" ?>><?= $uf['UF'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="cidade">Cidade</label>
                                    <select class="form-control select2" id="cidade" name="codigo_do_municipio" data-codigo-selecionado="<?= $tecnicoCodigoMunicipioSelecionado ?>" data-municipio-selecionado="<?= esc($tecnicoCidadeSelecionada) ?>" style="width: 100%;">
                                        <option value="">Selecione o estado</option>
                                    </select>
                                    <input type="hidden" id="cidade_nome" name="cidade" value="<?= esc($tecnicoCidadeSelecionada) ?>">
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
                                    <input type="text" class="form-control" id="celular" name="celular" value="<?= esc($tecnicoCelular) ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="whatsapp">Whatsapp</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?= esc($tecnicoWhatsapp) ?>">
                                        <?php if ($tecnicoWhatsappLink !== '') : ?>
                                            <div class="input-group-append">
                                                <a class="btn btn-success" href="<?= esc($tecnicoWhatsappLink) ?>" target="_blank" rel="noopener">
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
                                    <input type="text" class="form-control" id="telefone_fixo" name="telefone_fixo" value="<?= esc($tecnicoTelefoneFixo) ?>">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">E-mail</label>
                                    <input type="text" class="form-control" name="email" value="<?= (isset($tecnico)) ? $tecnico['email'] : "" ?>">
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
                                    <label for="">Observações</label>
                                    <textarea class="form-control" name="observacoes" rows="10"><?= (isset($tecnico)) ? $tecnico['observacoes'] : "" ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <?php if (isset($tecnico)) : ?>
                    <!-- HIDDENS -->
                    <input type="hidden" class="form-control" name="id_tecnico" value="<?= $tecnico['id_tecnico'] ?>">
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary"><?= (isset($tecnico)) ? "Atualizar" : "Cadastrar" ?></button>
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
            municipiosUrl: <?= json_encode(rtrim(base_url('tecnicos/municipiosPorUf'), '/')) ?>,
            municipioSelector: '#cidade_nome'
        });
    });
</script>
