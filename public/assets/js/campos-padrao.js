(function ($) {
    'use strict';

    var DATA_MINIMA_PADRAO = '1900-01-01';
    var DATA_MAXIMA_PADRAO = '2100-12-31';
    var MENSAGEM_DATA_PADRAO = 'Informe uma data entre 01/01/1900 e 31/12/2100.';

    function nomeCampo(elemento) {
        return String($(elemento).attr('name') || $(elemento).attr('id') || '').toLowerCase();
    }

    function aplicarMascara($campo, mascara, opcoes) {
        if ($.fn.inputmask) {
            var configuracao = $.extend({
                clearIncomplete: true,
                removeMaskOnSubmit: true,
                showMaskOnHover: false
            }, opcoes || {});

            if (Array.isArray(mascara)) {
                configuracao.mask = mascara;
                $campo.inputmask(configuracao);
                return;
            }

            $campo.inputmask(mascara, configuracao);
        }
    }

    function configurarDigitos($campo, tamanho) {
        if ($campo.data('campo-padrao-configurado')) {
            return;
        }

        $campo.data('campo-padrao-configurado', true);
        $campo.attr({
            type: 'text',
            inputmode: 'numeric',
            maxlength: tamanho
        });

        $campo.on('input', function () {
            var valor = this.value.replace(/\D/g, '').slice(0, tamanho);
            this.value = valor;
        });
    }

    function configurarData($campo) {
        if ($campo.data('campo-padrao-configurado')) {
            return;
        }

        $campo.data('campo-padrao-configurado', true);
        $campo.attr({
            type: 'date',
            min: $campo.attr('min') || DATA_MINIMA_PADRAO,
            max: $campo.attr('max') || DATA_MAXIMA_PADRAO,
            title: $campo.attr('title') || MENSAGEM_DATA_PADRAO
        });

        $campo.on('input change blur', function () {
            var valor = String(this.value || '');
            var partes = valor.split('-');

            if (partes.length === 3 && partes[0].length > 4) {
                partes[0] = partes[0].slice(0, 4);
                this.value = partes.join('-');
            }

            valor = String(this.value || '');

            if (valor && (valor < $campo.attr('min') || valor > $campo.attr('max'))) {
                this.setCustomValidity(MENSAGEM_DATA_PADRAO);
                return;
            }

            this.setCustomValidity('');
        });
    }

    function marcarConfigurado($campo) {
        if ($campo.data('campo-padrao-configurado')) {
            return false;
        }

        $campo.data('campo-padrao-configurado', true);
        return true;
    }

    function ehTelefone(nome) {
        return /(^|_)(telefone|fone|fixo|celular|whatsapp|comercial|residencial)(_|$)/.test(nome);
    }

    function ehTelefoneFixo(nome) {
        return nome === 'telefone_fixo';
    }

    function ehCnpj(nome) {
        return nome.indexOf('cnpj') !== -1;
    }

    function ehCpf(nome) {
        return nome === 'cpf' || /(^|_)cpf(_|$)/.test(nome);
    }

    function ehCep(nome) {
        return nome === 'cep';
    }

    function ehEmail(nome) {
        return nome.indexOf('email') !== -1;
    }

    function ehData(nome) {
        return nome === 'data'
            || /^data(_|$)/.test(nome)
            || /(^|_)(validade|vencimento|nascimento|contratacao|admissao|demissao|emissao|expedicao)(_|$)/.test(nome);
    }

    function configurarCampo(elemento) {
        var $campo = $(elemento);
        var tipo = String($campo.attr('type') || 'text').toLowerCase();
        var nome = nomeCampo(elemento);

        if (!nome || ['hidden', 'file', 'checkbox', 'radio', 'submit', 'button'].indexOf(tipo) !== -1) {
            return;
        }

        if (tipo === 'date' || ehData(nome)) {
            configurarData($campo);
            return;
        }

        if (ehTelefoneFixo(nome)) {
            if (!marcarConfigurado($campo)) {
                return;
            }

            $campo.attr({
                type: 'text',
                inputmode: 'numeric',
                placeholder: '(xx) xxxx-xxxx',
                title: 'Informe 10 ou 11 digitos com DDD.'
            });
            $campo.removeAttr('maxlength minlength');
            aplicarMascara($campo, ['(99) 9999-9999', '(99) 99999-9999'], {
                keepStatic: true
            });
            return;
        }

        if (ehTelefone(nome)) {
            if (!marcarConfigurado($campo)) {
                return;
            }

            $campo.attr({
                type: 'text',
                inputmode: 'numeric',
                maxlength: 15,
                minlength: 15,
                placeholder: '(xx) xxxxx-xxxx',
                title: 'Informe 11 digitos com DDD.'
            });
            aplicarMascara($campo, '(99) 99999-9999');
            return;
        }

        if (ehCnpj(nome)) {
            if (!marcarConfigurado($campo)) {
                return;
            }

            $campo.attr({
                type: 'text',
                inputmode: 'numeric',
                maxlength: 18,
                minlength: 18,
                placeholder: 'XX.XXX.XXX/XXXX-XX',
                title: 'Informe 14 digitos.'
            });
            aplicarMascara($campo, '99.999.999/9999-99');
            return;
        }

        if (ehCpf(nome)) {
            if (!marcarConfigurado($campo)) {
                return;
            }

            $campo.attr({
                type: 'text',
                inputmode: 'numeric',
                maxlength: 14,
                placeholder: 'XXX.XXX.XXX-XX'
            });
            aplicarMascara($campo, '999.999.999-99');
            return;
        }

        if (ehCep(nome)) {
            if (!marcarConfigurado($campo)) {
                return;
            }

            $campo.attr({
                type: 'text',
                inputmode: 'numeric',
                maxlength: 9,
                placeholder: 'XXXXX-XXX'
            });
            aplicarMascara($campo, '99999-999');
            return;
        }

        if (ehEmail(nome)) {
            if (!marcarConfigurado($campo)) {
                return;
            }

            $campo.attr({
                type: 'email',
                maxlength: 50,
                title: 'Informe um e-mail valido com @.'
            });
            return;
        }

        if (nome === 'uf') {
            if (!marcarConfigurado($campo)) {
                return;
            }

            $campo.attr('maxlength', 2);
            $campo.on('input', function () {
                this.value = this.value.replace(/[^a-zA-Z]/g, '').toUpperCase().slice(0, 2);
            });
            return;
        }

        var limites = {
            ncm: 8,
            cfop: 4,
            csosn: 3,
            crt: 1,
            cuf: 2,
            cmunfg: 7,
            cmun: 7,
            cpais: 4,
            ie: 13,
            inscricao_estadual: 13
        };

        if (limites[nome]) {
            configurarDigitos($campo, limites[nome]);
            return;
        }

        if (tipo === 'text' && !$campo.attr('maxlength')) {
            $campo.attr('maxlength', 50);
        }
    }

    function configurarCampos(contexto) {
        var $contexto = contexto ? $(contexto) : $(document);
        var $campos = $contexto.is('input') ? $contexto : $contexto.find('input');

        $campos.each(function () {
            configurarCampo(this);
        });
    }

    function observarCamposNovos() {
        if (!window.MutationObserver || !document.body) {
            return;
        }

        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                $(mutation.addedNodes).each(function () {
                    if (this.nodeType !== 1) {
                        return;
                    }

                    configurarCampos(this);
                });
            });
        }).observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    $(function () {
        configurarCampos();
        observarCamposNovos();
    });
})(jQuery);
