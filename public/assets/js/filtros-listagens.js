(function(global, $) {
    'use strict';

    var estados = new Map();
    var filtroRegistrado = false;

    function normalizarTexto(valor) {
        return String(valor || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }

    function ehColunaData(rotulo) {
        var texto = normalizarTexto(rotulo);

        return /(^|\s)(data|entrada|saida|emissao|vencimento|validade|abertura|fechamento)(\s|$)/.test(texto)
            || texto.indexOf('data de ') !== -1
            || texto.indexOf('data/hora') !== -1;
    }

    function ehColunaHora(rotulo) {
        var texto = normalizarTexto(rotulo);

        return texto.indexOf('hora') !== -1 || texto.indexOf('horario') !== -1;
    }

    function identificarCamposTemporais(tabela) {
        var cabecalhos = [];

        $(tabela).find('thead th').each(function(indice) {
            cabecalhos.push({
                indice: indice,
                rotulo: $(this).text().trim()
            });
        });

        return cabecalhos
            .filter(function(cabecalho) {
                return ehColunaData(cabecalho.rotulo);
            })
            .map(function(cabecalho) {
                var proximo = cabecalhos[cabecalho.indice + 1];
                var indiceHora = proximo && ehColunaHora(proximo.rotulo) ? proximo.indice : null;

                return {
                    indiceData: cabecalho.indice,
                    indiceHora: indiceHora,
                    rotulo: cabecalho.rotulo + (indiceHora !== null ? ' / ' + proximo.rotulo : '')
                };
            });
    }

    function partesDataHora(valor) {
        var texto = String(valor || '').replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
        var data = null;
        var hora = null;
        var encontrouDataBr = texto.match(/(\d{2})\/(\d{2})\/(\d{4})/);
        var encontrouDataIso = texto.match(/(\d{4})-(\d{2})-(\d{2})/);
        var encontrouHora = texto.match(/(?:^|\s|-)(\d{1,2}):(\d{2})(?::(\d{2}))?/);

        if (encontrouDataBr) {
            data = [encontrouDataBr[3], encontrouDataBr[2], encontrouDataBr[1]].map(Number);
        } else if (encontrouDataIso) {
            data = [encontrouDataIso[1], encontrouDataIso[2], encontrouDataIso[3]].map(Number);
        }

        if (encontrouHora) {
            hora = [
                Number(encontrouHora[1]),
                Number(encontrouHora[2]),
                Number(encontrouHora[3] || 0)
            ];
        }

        return { data: data, hora: hora };
    }

    function dataHoraDaLinha(valorData, valorHora) {
        var principal = partesDataHora(valorData);
        var complementar = partesDataHora(valorHora);
        var data = principal.data || complementar.data;
        var hora = principal.hora || complementar.hora || [0, 0, 0];

        if (!data) {
            return null;
        }

        var resultado = new Date(data[0], data[1] - 1, data[2], hora[0], hora[1], hora[2]);

        return Number.isNaN(resultado.getTime()) ? null : resultado;
    }

    function registrarFiltroDataTables() {
        if (filtroRegistrado || !$.fn.dataTable) {
            return;
        }

        $.fn.dataTable.ext.search.push(function(configuracao, dados) {
            var estado = estados.get(configuracao.nTable);

            if (!estado || (!estado.inicio && !estado.fim)) {
                return true;
            }

            var campo = estado.campos[estado.indiceCampo];
            var dataLinha = dataHoraDaLinha(
                dados[campo.indiceData],
                campo.indiceHora !== null ? dados[campo.indiceHora] : ''
            );

            if (!dataLinha) {
                return false;
            }

            return (!estado.inicio || dataLinha >= estado.inicio)
                && (!estado.fim || dataLinha <= estado.fim);
        });

        filtroRegistrado = true;
    }

    function exibirErro(mensagem) {
        if (global.Swal && typeof global.Swal.fire === 'function') {
            global.Swal.fire({
                type: 'error',
                title: 'Confira o período',
                text: mensagem
            });
            return;
        }

        global.alert(mensagem);
    }

    function criarFiltroPeriodo(api, campos) {
        if (!campos.length) {
            return;
        }

        var tabela = api.table().node();
        var container = $(api.table().container());
        var painel = $(
            '<div class="filtro-periodo-listagem no-print">' +
                '<button type="button" class="btn btn-outline-primary btn-sm filtro-periodo-alternar">' +
                    '<i class="fas fa-calendar-alt"></i> Período personalizado' +
                '</button>' +
                '<div class="filtro-periodo-campos" style="display:none">' +
                    '<div class="filtro-periodo-item filtro-periodo-campo"><label>Campo temporal</label><select class="form-control form-control-sm"></select></div>' +
                    '<div class="filtro-periodo-item"><label>Data e hora inicial</label><input type="datetime-local" class="form-control form-control-sm filtro-periodo-inicio"></div>' +
                    '<div class="filtro-periodo-item"><label>Data e hora final</label><input type="datetime-local" class="form-control form-control-sm filtro-periodo-fim"></div>' +
                    '<div class="filtro-periodo-acoes">' +
                        '<button type="button" class="btn btn-primary btn-sm filtro-periodo-aplicar"><i class="fas fa-filter"></i> Aplicar</button>' +
                        '<button type="button" class="btn btn-default btn-sm filtro-periodo-limpar">Limpar</button>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
        var seletor = painel.find('select');

        campos.forEach(function(campo, indice) {
            $('<option></option>').val(indice).text(campo.rotulo).appendTo(seletor);
        });

        estados.set(tabela, {
            campos: campos,
            indiceCampo: 0,
            inicio: null,
            fim: null
        });

        painel.find('.filtro-periodo-alternar').on('click', function() {
            painel.find('.filtro-periodo-campos').slideToggle(150);
        });

        painel.find('.filtro-periodo-aplicar').on('click', function() {
            var inicioTexto = painel.find('.filtro-periodo-inicio').val();
            var fimTexto = painel.find('.filtro-periodo-fim').val();
            var inicio = inicioTexto ? new Date(inicioTexto) : null;
            var fim = fimTexto ? new Date(fimTexto) : null;

            if (!inicio && !fim) {
                exibirErro('Informe pelo menos a data e hora inicial ou final.');
                return;
            }

            if (inicio && fim && inicio > fim) {
                exibirErro('A data e hora inicial deve ser anterior à final.');
                return;
            }

            estados.set(tabela, {
                campos: campos,
                indiceCampo: Number(seletor.val() || 0),
                inicio: inicio,
                fim: fim
            });
            api.draw();
        });

        painel.find('.filtro-periodo-limpar').on('click', function() {
            painel.find('input').val('');
            estados.set(tabela, {
                campos: campos,
                indiceCampo: Number(seletor.val() || 0),
                inicio: null,
                fim: null
            });
            api.draw();
        });

        container.prepend(painel);
    }

    function opcoesPadrao(idioma) {
        return {
            language: idioma,
            pageLength: 30,
            lengthMenu: [[30, 60, 100], [30, 60, 100]],
            autoWidth: false,
            initComplete: function() {
                var api = this.api();
                var tabela = api.table().node();
                var campos = $(tabela).hasClass('tabela-periodo')
                    ? identificarCamposTemporais(tabela)
                    : [];

                criarFiltroPeriodo(api, campos);
            }
        };
    }

    function inicializar(idioma) {
        if (!$ || !$.fn.DataTable) {
            return;
        }

        registrarFiltroDataTables();

        $('table.tabela-listagem, table[id^="example"]').each(function() {
            var possuiLinhaVaziaMesclada = $(this).find('tbody td[colspan]').length > 0;

            if (!possuiLinhaVaziaMesclada && !$.fn.dataTable.isDataTable(this)) {
                $(this).DataTable(opcoesPadrao(idioma));
            }
        });
    }

    var api = {
        dataHoraDaLinha: dataHoraDaLinha,
        identificarCamposTemporais: identificarCamposTemporais,
        inicializar: inicializar,
        normalizarTexto: normalizarTexto,
        opcoesPadrao: opcoesPadrao
    };

    global.FiltrosListagens = api;

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = api;
    }
})(typeof window !== 'undefined' ? window : globalThis, typeof jQuery !== 'undefined' ? jQuery : null);
