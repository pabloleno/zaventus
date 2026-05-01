(function ($) {
    'use strict';

    function nomeCampo(elemento) {
        return String($(elemento).attr('name') || $(elemento).attr('id') || '').toLowerCase();
    }

    function aplicarMascara($campo, mascara, opcoes) {
        if ($.fn.inputmask) {
            $campo.inputmask(mascara, $.extend({
                clearIncomplete: true,
                removeMaskOnSubmit: true,
                showMaskOnHover: false
            }, opcoes || {}));
        }
    }

    function configurarDigitos($campo, tamanho) {
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
        $campo.attr({
            min: $campo.attr('min') || '0001-01-01',
            max: $campo.attr('max') || '9999-12-31',
            title: $campo.attr('title') || 'Informe uma data com ano de 4 digitos.'
        });

        $campo.on('input change blur', function () {
            var valor = String(this.value || '');
            var partes = valor.split('-');

            if (partes.length === 3 && partes[0].length > 4) {
                partes[0] = partes[0].slice(0, 4);
                this.value = partes.join('-');
            }
        });
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

    function configurarCampos() {
        $('input').each(function () {
            var $campo = $(this);
            var tipo = String($campo.attr('type') || 'text').toLowerCase();
            var nome = nomeCampo(this);

            if (!nome || ['hidden', 'file', 'checkbox', 'radio', 'submit', 'button'].indexOf(tipo) !== -1) {
                return;
            }

            if (tipo === 'date') {
                configurarData($campo);
                return;
            }

            if (ehTelefoneFixo(nome)) {
                $campo.attr({
                    type: 'text',
                    inputmode: 'numeric',
                    maxlength: 14,
                    minlength: 14,
                    placeholder: '(xx) xxxx-xxxx',
                    title: 'Informe 10 digitos com DDD.'
                });
                aplicarMascara($campo, '(99) 9999-9999');
                return;
            }

            if (ehTelefone(nome)) {
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
                $campo.attr({
                    type: 'email',
                    maxlength: 50,
                    title: 'Informe um e-mail valido com @.'
                });
                return;
            }

            if (nome === 'uf') {
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
        });
    }

    $(configurarCampos);
})(jQuery);
