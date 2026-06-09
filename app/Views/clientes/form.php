<?php
    use App\Libraries\ContatoPadrao;

    $clienteEndereco = $cliente ?? [];
    $ufSelecionada = strtoupper(trim((string) ($clienteEndereco['UF'] ?? '')));
    $codigoMunicipioSelecionado = preg_replace('/\D/', '', (string) ($clienteEndereco['codigo_do_municipio'] ?? ''));
    $municipioSelecionado = trim((string) ($clienteEndereco['municipio'] ?? ''));
    $clienteCelular = ContatoPadrao::primeiroValor($clienteEndereco, ['celular']);
    $clienteWhatsapp = ContatoPadrao::primeiroValor($clienteEndereco, ['whatsapp']);
    $clienteTelefoneFixo = ContatoPadrao::primeiroValor($clienteEndereco, ['telefone_fixo', 'residencial', 'comercial']);
    $clienteWhatsappLink = ContatoPadrao::whatsappLink($clienteWhatsapp);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/clientes/store" method="post" enctype="multipart/form-data">
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
                            <?= view('components/foto_cadastro', ['foto' => $cliente['foto'] ?? '', 'editavel' => true]) ?>
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
                                    <label for="data_de_nascimento">Data de nascimento</label>
                                    <input type="date" class="form-control" id="data_de_nascimento" name="data_de_nascimento" value="<?= (isset($cliente)) ? $cliente['data_de_nascimento'] : "" ?>">
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
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="cep">CEP</label>
                                    <input type="text" class="form-control" id="cep" name="cep" value="<?= (isset($cliente)) ? $cliente['cep'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="logradouro">Endere&ccedil;o</label>
                                    <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?= (isset($cliente)) ? $cliente['logradouro'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="numero">N&deg;</label>
                                    <input type="text" class="form-control" id="numero" name="numero" value="<?= (isset($cliente)) ? $cliente['numero'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="complemento">Complemento</label>
                                    <input type="text" class="form-control" id="complemento" name="complemento" value="<?= (isset($cliente)) ? $cliente['complemento'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?= (isset($cliente)) ? $cliente['bairro'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="UF">Estado</label>
                                    <select class="form-control select2" id="UF" name="UF" style="width: 100%;">
                                        <option value="">UF</option>
                                        <?php foreach ($ufs as $uf) : ?>
                                            <option value="<?= $uf['UF'] ?>" <?= ($ufSelecionada === $uf['UF']) ? "selected" : "" ?>><?= $uf['UF'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="cidade">Cidade</label>
                                    <select class="form-control select2" id="cidade" name="codigo_do_municipio" data-codigo-selecionado="<?= $codigoMunicipioSelecionado ?>" data-municipio-selecionado="<?= esc($municipioSelecionado) ?>" style="width: 100%;">
                                        <option value="">Selecione o estado</option>
                                    </select>
                                    <input type="hidden" id="municipio" name="municipio" value="<?= esc($municipioSelecionado) ?>">
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
                                    <input type="text" class="form-control" id="celular" name="celular" value="<?= esc($clienteCelular) ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="whatsapp">Whatsapp</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?= esc($clienteWhatsapp) ?>">
                                        <?php if ($clienteWhatsappLink !== '') : ?>
                                            <div class="input-group-append">
                                                <a class="btn btn-success" href="<?= esc($clienteWhatsappLink) ?>" target="_blank" rel="noopener">
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
                                    <input type="text" class="form-control" id="telefone_fixo" name="telefone_fixo" value="<?= esc($clienteTelefoneFixo) ?>">
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

    function configuraEnderecoCliente() {
        var $cep = $('#cep');
        var $logradouro = $('#logradouro');
        var $numero = $('#numero');
        var $bairro = $('#bairro');
        var $uf = $('#UF');
        var $cidade = $('#cidade');
        var $municipio = $('#municipio');
        var municipiosUrl = <?= json_encode(rtrim(base_url('clientes/municipiosPorUf'), '/')) ?>;
        var codigoSelecionado = String($cidade.attr('data-codigo-selecionado') || '');
        var municipioSelecionado = String($cidade.attr('data-municipio-selecionado') || '');
        var ultimoCepConsultado = '';
        var timerCep = null;

        function limparCidade(texto) {
            $cidade.empty().append(new Option(texto || 'Selecione o estado', ''));
            $cidade.prop('disabled', true).trigger('change.select2');
            $municipio.val('');
        }

        function atualizarMunicipio() {
            var nome = $cidade.find('option:selected').attr('data-municipio') || '';
            $municipio.val(nome);
        }

        function selecionarCidade(codigo, nome) {
            var codigoLimpo = String(codigo || '').replace(/\D/g, '');

            if (codigoLimpo && $cidade.find('option[value="' + codigoLimpo + '"]').length) {
                $cidade.val(codigoLimpo);
                return;
            }

            if (nome) {
                var nomeNormalizado = String(nome).trim().toLowerCase();

                $cidade.find('option').each(function () {
                    if (String($(this).attr('data-municipio') || '').trim().toLowerCase() === nomeNormalizado) {
                        $cidade.val($(this).val());
                        return false;
                    }
                });
            }
        }

        function carregarCidades(uf, codigo, nome) {
            uf = String(uf || '').replace(/[^a-zA-Z]/g, '').toUpperCase().slice(0, 2);

            if (!uf) {
                limparCidade('Selecione o estado');
                return;
            }

            $cidade.empty().append(new Option('Carregando cidades...', ''));
            $cidade.prop('disabled', true).trigger('change.select2');
            $municipio.val('');

            $.getJSON(municipiosUrl + '/' + encodeURIComponent(uf))
                .done(function (cidades) {
                    $cidade.empty().append(new Option('Selecione', ''));

                    $.each(cidades || [], function (_, cidade) {
                        var option = new Option(cidade.municipio, cidade.codigo);
                        $(option).attr('data-municipio', cidade.municipio);
                        $cidade.append(option);
                    });

                    selecionarCidade(codigo, nome);
                    $cidade.prop('disabled', false).trigger('change');
                    $cidade.trigger('change.select2');
                })
                .fail(function () {
                    limparCidade('Nao foi possivel carregar');
                });
        }

        function buscarCep() {
            var cep = String($cep.val() || '').replace(/\D/g, '');

            if (cep.length !== 8 || cep === ultimoCepConsultado) {
                return;
            }

            ultimoCepConsultado = cep;

            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/')
                .done(function (dados) {
                    if (!dados || dados.erro) {
                        return;
                    }

                    if (dados.logradouro) {
                        $logradouro.val(dados.logradouro);
                    }

                    if (dados.bairro) {
                        $bairro.val(dados.bairro);
                    }

                    if (dados.uf) {
                        $uf.val(dados.uf).trigger('change.select2');
                        carregarCidades(dados.uf, dados.ibge || '', dados.localidade || '');
                    }

                    if (dados.logradouro || dados.localidade) {
                        $numero.trigger('focus');
                    }
                });
        }

        $uf.on('change', function () {
            carregarCidades(this.value, '', '');
        });

        $cidade.on('change', atualizarMunicipio);

        $cep.on('input', function () {
            clearTimeout(timerCep);

            if (String(this.value || '').replace(/\D/g, '').length === 8) {
                timerCep = setTimeout(buscarCep, 300);
            }
        });

        $cep.on('blur', buscarCep);

        if ($uf.val()) {
            carregarCidades($uf.val(), codigoSelecionado, municipioSelecionado);
        } else {
            limparCidade('Selecione o estado');
        }
    }

    $(function () {
        configuraEnderecoPadrao({
            municipiosUrl: <?= json_encode(rtrim(base_url('clientes/municipiosPorUf'), '/')) ?>
        });
    });
    alteraTipoDoCliente();
</script>
