(function(window, document) {
    'use strict';

    var CHAVE_PREFERENCIA = 'sistema-modo-cor';
    var CHAVE_ANTIGA_DASHBOARD = 'dashboard-modo-cor';
    var CLASSE_ESCURA = 'sistema-dark-mode';

    /**
     * Le a preferencia persistida e migra a chave usada inicialmente pela dashboard.
     */
    function preferenciaSalva() {
        try {
            var preferencia = window.localStorage.getItem(CHAVE_PREFERENCIA);

            if (preferencia) {
                return preferencia;
            }

            preferencia = window.localStorage.getItem(CHAVE_ANTIGA_DASHBOARD);

            if (preferencia) {
                window.localStorage.setItem(CHAVE_PREFERENCIA, preferencia);
                window.localStorage.removeItem(CHAVE_ANTIGA_DASHBOARD);
            }

            return preferencia || 'claro';
        } catch (erro) {
            return 'claro';
        }
    }

    /**
     * Informa se a paleta escura esta ativa no documento.
     */
    function escuroAtivo() {
        return document.documentElement.classList.contains(CLASSE_ESCURA);
    }

    /**
     * Mantem a classe legada da dashboard enquanto a paleta global e adotada.
     */
    function sincronizaClasseDoCorpo() {
        if (document.body) {
            document.body.classList.toggle('dashboard-dark-mode', escuroAtivo());
        }
    }

    /**
     * Atualiza icones e textos acessiveis de todos os controles de tema.
     */
    function atualizaControles() {
        var escuro = escuroAtivo();
        var rotulo = escuro ? 'Ativar modo claro' : 'Ativar modo escuro';

        document.querySelectorAll('[data-tema-cor-toggle]').forEach(function(controle) {
            controle.setAttribute('aria-label', rotulo);
            controle.setAttribute('aria-pressed', escuro ? 'true' : 'false');
            controle.setAttribute('title', rotulo);

            var icone = controle.querySelector('i');

            if (icone) {
                icone.className = escuro ? 'fas fa-sun' : 'fas fa-moon';
            }
        });
    }

    /**
     * Atualiza graficos Chart.js existentes para manter textos e grades legiveis.
     */
    function atualizaGraficos() {
        if (!window.Chart || !window.Chart.defaults || !window.Chart.defaults.global) {
            return;
        }

        var texto = escuroAtivo() ? '#dbe5f1' : '#475569';
        var grade = escuroAtivo() ? 'rgba(148, 163, 184, .22)' : 'rgba(148, 163, 184, .16)';

        window.Chart.defaults.global.defaultFontColor = texto;

        Object.keys(window.Chart.instances || {}).forEach(function(chave) {
            var grafico = window.Chart.instances[chave];

            if (!grafico || !grafico.options) {
                return;
            }

            if (grafico.options.legend && grafico.options.legend.labels) {
                grafico.options.legend.labels.fontColor = texto;
            }

            if (grafico.options.scales) {
                (grafico.options.scales.xAxes || []).forEach(function(eixo) {
                    eixo.ticks = eixo.ticks || {};
                    eixo.gridLines = eixo.gridLines || {};
                    eixo.ticks.fontColor = texto;
                    eixo.gridLines.color = grade;
                    eixo.gridLines.zeroLineColor = grade;
                });
                (grafico.options.scales.yAxes || []).forEach(function(eixo) {
                    eixo.ticks = eixo.ticks || {};
                    eixo.gridLines = eixo.gridLines || {};
                    eixo.ticks.fontColor = texto;
                    eixo.gridLines.color = grade;
                    eixo.gridLines.zeroLineColor = grade;
                });
            }

            grafico.update();
        });
    }

    /**
     * Persiste a preferencia quando o navegador permite armazenamento local.
     */
    function salvaPreferencia(modo) {
        try {
            window.localStorage.setItem(CHAVE_PREFERENCIA, modo);
        } catch (erro) {
            // A alternancia permanece ativa durante a pagina atual.
        }
    }

    /**
     * Aplica a paleta escolhida e notifica componentes com renderizacao propria.
     */
    function aplicaModo(modo, persistir) {
        var escuro = modo === 'escuro';

        document.documentElement.classList.toggle(CLASSE_ESCURA, escuro);
        sincronizaClasseDoCorpo();

        if (persistir) {
            salvaPreferencia(escuro ? 'escuro' : 'claro');
        }

        atualizaControles();
        atualizaGraficos();
        document.dispatchEvent(new CustomEvent('sistema:tema-cor-alterado', {
            detail: {
                modo: escuro ? 'escuro' : 'claro',
                escuro: escuro
            }
        }));
    }

    /**
     * Alterna entre as paletas clara e escura.
     */
    function alternaModo() {
        aplicaModo(escuroAtivo() ? 'claro' : 'escuro', true);
    }

    /**
     * Liga os controles encontrados, inclusive quando inseridos dinamicamente.
     */
    function inicializa() {
        sincronizaClasseDoCorpo();
        atualizaControles();
        atualizaGraficos();
        window.addEventListener('load', atualizaGraficos);

        document.addEventListener('click', function(evento) {
            var controle = evento.target.closest('[data-tema-cor-toggle]');

            if (!controle) {
                return;
            }

            evento.preventDefault();
            alternaModo();
        });

        if (window.MutationObserver) {
            new MutationObserver(atualizaControles).observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    aplicaModo(preferenciaSalva(), false);

    window.TemaCor = {
        aplicaModo: aplicaModo,
        alternaModo: alternaModo,
        escuroAtivo: escuroAtivo
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializa);
    } else {
        inicializa();
    }
})(window, document);
