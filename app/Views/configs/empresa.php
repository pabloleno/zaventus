<?php
    $empresa = $empresa ?? [];
    $ufSelecionada = strtoupper(trim((string) ($empresa['UF'] ?? '')));
    $codigoMunicipioSelecionado = preg_replace('/\D/', '', (string) ($empresa['codigo_do_municipio'] ?? ''));
    $municipioSelecionado = trim((string) ($empresa['municipio'] ?? ''));
    $telefoneLegado = $empresa['telefone'] ?? '';
    $telefoneLegadoDigitos = preg_replace('/\D/', '', (string) $telefoneLegado);
    $celular = $empresa['celular'] ?? '';
    $whatsapp = $empresa['whatsapp'] ?? '';
    $telefoneFixo = $empresa['telefone_fixo'] ?? '';

    if ($telefoneFixo === '' && strlen($telefoneLegadoDigitos) === 10) {
        $telefoneFixo = $telefoneLegado;
    }

    if ($celular === '' && strlen($telefoneLegadoDigitos) === 11) {
        $celular = $telefoneLegado;
    }

    $logradouro = $empresa['logradouro'] ?? ($empresa['endereco'] ?? '');
    $whatsappDigitos = preg_replace('/\D/', '', (string) $whatsapp);

    if (strlen($whatsappDigitos) === 11) {
        $whatsappLink = 'https://web.whatsapp.com/send?phone=55' . $whatsappDigitos;
    } elseif (strlen($whatsappDigitos) > 11) {
        $whatsappLink = 'https://web.whatsapp.com/send?phone=' . $whatsappDigitos;
    } else {
        $whatsappLink = '';
    }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/configs/store_empresa" method="post">
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
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="razao_social">Raz&atilde;o Social</label>
                                    <input type="text" class="form-control" id="razao_social" name="razao_social" value="<?= esc($empresa['razao_social'] ?? '') ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="nome_fantasia">Nome Fantasia</label>
                                    <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia" value="<?= esc($empresa['nome_fantasia'] ?? '') ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="cnpj">CNPJ</label>
                                    <input type="text" class="form-control" id="cnpj" name="cnpj" value="<?= esc($empresa['cnpj'] ?? '') ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="inscricao_estadual">Inscri&ccedil;&atilde;o Estadual</label>
                                    <input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual" value="<?= esc($empresa['inscricao_estadual'] ?? '') ?>" required="">
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
                                <h6><i class="fa fa-phone-square"></i> Contatos</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="celular">Celular</label>
                                    <input type="text" class="form-control" id="celular" name="celular" value="<?= esc($celular) ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="whatsapp">Whatsapp</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="<?= esc($whatsapp) ?>">
                                        <?php if ($whatsappLink !== '') : ?>
                                            <div class="input-group-append">
                                                <a class="btn btn-success" href="<?= esc($whatsappLink) ?>" target="_blank" rel="noopener">
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
                                    <input type="text" class="form-control" id="telefone_fixo" name="telefone_fixo" value="<?= esc($telefoneFixo) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-12">
                                <h6><i class="fa fa-home"></i> Endere&ccedil;o</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="cep">CEP</label>
                                    <input type="text" class="form-control" id="cep" name="cep" value="<?= esc($empresa['cep'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="logradouro">Endere&ccedil;o</label>
                                    <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?= esc($logradouro) ?>">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="numero">N&deg;</label>
                                    <input type="text" class="form-control" id="numero" name="numero" value="<?= esc($empresa['numero'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="complemento">Complemento</label>
                                    <input type="text" class="form-control" id="complemento" name="complemento" value="<?= esc($empresa['complemento'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="bairro">Bairro</label>
                                    <input type="text" class="form-control" id="bairro" name="bairro" value="<?= esc($empresa['bairro'] ?? '') ?>">
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
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary">Salvar Altera&ccedil;&otilde;es</button>
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
    function configuraEnderecoEmpresa() {
        var $cep = $('#cep');
        var $logradouro = $('#logradouro');
        var $numero = $('#numero');
        var $bairro = $('#bairro');
        var $uf = $('#UF');
        var $cidade = $('#cidade');
        var $municipio = $('#municipio');
        var municipiosUrl = <?= json_encode(rtrim(base_url('configs/municipiosPorUf'), '/')) ?>;
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

    $(function() {
        configuraEnderecoPadrao({
            municipiosUrl: <?= json_encode(rtrim(base_url('configs/municipiosPorUf'), '/')) ?>
        });

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
                    title: 'Dados da Empresa atualizados com sucesso!'
                })
            <?php endif; ?>
        <?php endif; ?>
    });
</script>
