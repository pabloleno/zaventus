(function ($) {
    'use strict';

    window.configuraEnderecoPadrao = function (opcoes) {
        opcoes = opcoes || {};

        var $container = opcoes.container ? $(opcoes.container) : $(document);
        var buscarCampo = function (seletor) {
            return $container.find(seletor).first();
        };
        var $cep = buscarCampo(opcoes.cepSelector || '#cep');
        var $logradouro = buscarCampo(opcoes.logradouroSelector || '#logradouro');
        var $numero = buscarCampo(opcoes.numeroSelector || '#numero');
        var $complemento = buscarCampo(opcoes.complementoSelector || '#complemento');
        var $bairro = buscarCampo(opcoes.bairroSelector || '#bairro');
        var $uf = buscarCampo(opcoes.ufSelector || '#UF');
        var $cidade = buscarCampo(opcoes.cidadeSelector || '#cidade');
        var $municipio = buscarCampo(opcoes.municipioSelector || '#municipio');
        var municipiosUrl = String(opcoes.municipiosUrl || '').replace(/\/$/, '');
        var cidadeEhSelect = $cidade.is('select');
        var codigoSelecionado = String($cidade.attr('data-codigo-selecionado') || $cidade.val() || '');
        var municipioSelecionado = String($cidade.attr('data-municipio-selecionado') || $municipio.val() || '');
        var ultimoCepConsultado = '';
        var timerCep = null;

        if (!$cep.length || !$logradouro.length || !$numero.length || !$bairro.length || !$uf.length || !$cidade.length || !$municipio.length) {
            return;
        }

        if ($container.data('endereco-padrao-configurado')) {
            return;
        }

        $container.data('endereco-padrao-configurado', true);

        /**
         * Remove complementos do texto de cidade retornado pelo servico de CEP.
         */
        function limparCidade(texto) {
            if (!cidadeEhSelect) {
                $cidade.val('');
                $municipio.val('');
                return;
            }

            $cidade.empty().append(new Option(texto || 'Selecione o estado', ''));
            $cidade.prop('disabled', true).trigger('change.select2');
            $municipio.val('');
        }

        /**
         * Atualiza r municipio.
         */
        function atualizarMunicipio() {
            var nome = $cidade.find('option:selected').attr('data-municipio') || '';
            $municipio.val(nome);
        }

        /**
         * Seleciona a cidade correspondente ao codigo e nome informados.
         */
        function selecionarCidade(codigo, nome) {
            var codigoLimpo = String(codigo || '').replace(/\D/g, '');

            if (!cidadeEhSelect) {
                $cidade.val(codigoLimpo);
                $municipio.val(nome || '');
                return;
            }

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

        /**
         * Carrega as cidades da UF selecionada e restaura a selecao anterior.
         */
        function carregarCidades(uf, codigo, nome) {
            uf = String(uf || '').replace(/[^a-zA-Z]/g, '').toUpperCase().slice(0, 2);
            nome = String(nome || '').trim();

            if (!uf) {
                limparCidade('Selecione o estado');
                return;
            }

            if (!municipiosUrl) {
                selecionarCidade(codigo, nome);
                return;
            }

            $cidade.empty().append(new Option('Carregando cidades...', ''));
            $cidade.prop('disabled', true).trigger('change.select2');
            $municipio.val(nome);

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
                    $municipio.val(nome);
                });
        }

        /**
         * Consulta o CEP informado e preenche os campos de endereco disponiveis.
         */
        function buscarCep() {
            var cep = String($cep.val() || '').replace(/\D/g, '');

            if (cep.length !== 8 || cep === ultimoCepConsultado) {
                return;
            }

            ultimoCepConsultado = cep;
            $cep.trigger('endereco:cep-consultando');

            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/')
                .done(function (dados) {
                    if (!dados || dados.erro) {
                        ultimoCepConsultado = '';
                        $cep.trigger('endereco:cep-nao-encontrado');
                        return;
                    }

                    $logradouro.val(dados.logradouro || '');
                    $bairro.val(dados.bairro || '');

                    if ($complemento.length) {
                        $complemento.val(dados.complemento || '');
                    }

                    if (dados.uf) {
                        $uf.val(dados.uf).trigger('change.select2');
                        $municipio.val(dados.localidade || '');
                        carregarCidades(dados.uf, dados.ibge || '', dados.localidade || '');
                    }

                    if (dados.logradouro || dados.localidade) {
                        $numero.trigger('focus');
                    }

                    $cep.trigger('endereco:cep-preenchido', [dados]);
                })
                .fail(function () {
                    ultimoCepConsultado = '';
                    $cep.trigger('endereco:cep-erro');
                });
        }

        $uf.on('change', function () {
            carregarCidades(this.value, '', '');
        });

        if (cidadeEhSelect) {
            $cidade.on('change', atualizarMunicipio);
        }

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
    };
})(jQuery);
