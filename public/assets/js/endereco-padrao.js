(function ($) {
    'use strict';

    window.configuraEnderecoPadrao = function (opcoes) {
        opcoes = opcoes || {};

        var $cep = $(opcoes.cepSelector || '#cep');
        var $logradouro = $(opcoes.logradouroSelector || '#logradouro');
        var $numero = $(opcoes.numeroSelector || '#numero');
        var $bairro = $(opcoes.bairroSelector || '#bairro');
        var $uf = $(opcoes.ufSelector || '#UF');
        var $cidade = $(opcoes.cidadeSelector || '#cidade');
        var $municipio = $(opcoes.municipioSelector || '#municipio');
        var municipiosUrl = String(opcoes.municipiosUrl || '').replace(/\/$/, '');
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

            if (!uf || !municipiosUrl) {
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
    };
})(jQuery);
