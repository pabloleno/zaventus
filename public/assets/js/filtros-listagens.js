(function(global, $) {
    'use strict';

    /**
     * Monta as opcoes compartilhadas das tabelas de listagem.
     */
    function opcoesPadrao(idioma) {
        return {
            language: idioma,
            pageLength: 30,
            lengthMenu: [[30, 60, 100], [30, 60, 100]],
            autoWidth: false
        };
    }

    /**
     * Inicializa paginacao, busca e ordenacao das tabelas.
     */
    function inicializar(idioma) {
        if (!$ || !$.fn.DataTable) {
            return;
        }

        $('table.tabela-listagem, table[id^="example"]').each(function() {
            var possuiLinhaVaziaMesclada = $(this).find('tbody td[colspan]').length > 0;

            if (!possuiLinhaVaziaMesclada && !$.fn.dataTable.isDataTable(this)) {
                $(this).DataTable(opcoesPadrao(idioma));
            }
        });
    }

    var api = {
        inicializar: inicializar,
        opcoesPadrao: opcoesPadrao
    };

    global.FiltrosListagens = api;

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = api;
    }
})(typeof window !== 'undefined' ? window : globalThis, typeof jQuery !== 'undefined' ? jQuery : null);
