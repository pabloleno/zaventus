(function(window, document) {
    'use strict';

    var camposMonetarios = [
        'desconto',
        'frete',
        'lucro',
        'margem_de_lucro',
        'outros',
        'salario',
        'subtotal',
        'troco',
        'comissao'
    ];

    /**
     * Obtem o identificador normalizado de um campo da interface.
     */
    function nomeCampo(elemento) {
        return String(elemento.name || elemento.id || '').toLowerCase();
    }

    /**
     * Informa se o campo representa um valor monetario.
     */
    function campoMonetario(elemento) {
        var nome = nomeCampo(elemento);

        return nome === 'valor'
            || nome.indexOf('valor_') === 0
            || nome.slice(-6) === '_valor'
            || camposMonetarios.indexOf(nome) !== -1;
    }

    /**
     * Converte a entrada monetaria da interface em numero.
     */
    function numeroMonetario(valor) {
        var texto = String(valor == null ? '' : valor).trim().replace(/[^\d,.\-]/g, '');

        if (!texto) {
            return 0;
        }

        if (texto.indexOf(',') !== -1) {
            texto = texto.replace(/\./g, '').replace(',', '.');
        } else if ((texto.match(/\./g) || []).length > 1) {
            var ultimaPosicao = texto.lastIndexOf('.');
            texto = texto.slice(0, ultimaPosicao).replace(/\./g, '') + texto.slice(ultimaPosicao);
        }

        var numero = Number(texto);

        return Number.isFinite(numero) ? Math.round((numero + Number.EPSILON) * 100) / 100 : 0;
    }

    /**
     * Converte um valor monetario da interface para decimal.
     */
    function decimalMonetario(valor) {
        return numeroMonetario(valor).toFixed(2);
    }

    /**
     * Normaliza elemento.
     */
    function normalizaElemento(elemento) {
        if (!campoMonetario(elemento) || elemento.value === '') {
            return;
        }

        elemento.value = decimalMonetario(elemento.value);
    }

    /**
     * Normaliza formulario.
     */
    function normalizaFormulario(formulario) {
        formulario.querySelectorAll('input').forEach(normalizaElemento);
    }

    /**
     * Inicializa os comportamentos compartilhados da interface.
     */
    function inicializa() {
        document.querySelectorAll('input').forEach(normalizaElemento);

        document.addEventListener('blur', function(evento) {
            if (evento.target && evento.target.tagName === 'INPUT') {
                normalizaElemento(evento.target);
            }
        }, true);

        document.addEventListener('submit', function(evento) {
            normalizaFormulario(evento.target);
        }, true);
    }

    window.numeroMonetario = numeroMonetario;
    window.decimalMonetario = decimalMonetario;
    window.normalizaCampoMonetario = function(id) {
        var elemento = document.getElementById(id);

        if (elemento) {
            normalizaElemento(elemento);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializa);
    } else {
        inicializa();
    }
})(window, document);
