<li class="nav-item dropdown cobrancas-navbar-alertas">
    <a class="nav-link" data-toggle="dropdown" href="#" aria-label="Alertas de cobrancas pendentes" title="Cobrancas pendentes">
        <i class="far fa-bell"></i>
        <span class="badge badge-danger navbar-badge cobrancas-navbar-total d-none">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right cobrancas-navbar-dropdown">
        <span class="dropdown-item dropdown-header cobrancas-navbar-cabecalho">Consultando cobrancas...</span>
        <div class="dropdown-divider"></div>
        <div class="cobrancas-navbar-lista">
            <span class="dropdown-item text-muted text-center">Aguarde um instante.</span>
        </div>
        <div class="dropdown-divider"></div>
        <a href="/cobrancas" class="dropdown-item dropdown-footer">Abrir central de cobrancas</a>
    </div>
</li>

<script>
    (function() {
        var endpointAlertasCobrancas = <?= json_encode(base_url('cobrancas/alertas-navbar')) ?>;
        var limiteAlertasVisiveis = 20;

        /**
         * Formata valores monetarios exibidos no alerta sem inserir HTML externo.
         */
        function formataMoedaAlertaCobranca(valor) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(Number(valor) || 0);
        }

        /**
         * Formata a data de banco para leitura rapida no menu de alertas.
         */
        function formataVencimentoAlertaCobranca(vencimento) {
            var partes = String(vencimento).match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/);

            return partes ? partes[3] + '/' + partes[2] + '/' + partes[1] + ' ' + partes[4] + ':' + partes[5] : vencimento;
        }

        /**
         * Cria um item seguro da lista para uma parcela ainda nao solucionada.
         */
        function criaItemAlertaCobranca(alerta) {
            var item = $('<a class="dropdown-item cobrancas-navbar-item"></a>').attr('href', '/cobrancas');
            var linhaTitulo = $('<span class="cobrancas-navbar-item-titulo"></span>');
            var rotuloParcela = alerta.rotulo_parcela || (alerta.numero_parcela + '/' + (alerta.total_parcelas || alerta.numero_parcela));
            var restantes = Number(alerta.parcelas_restantes) || 0;
            var textoRestantes = restantes === 1 ? '1 restante' : restantes + ' restantes';
            var status = $('<span class="cobrancas-navbar-status"></span>')
                .addClass(alerta.status_alerta === 'Atrasada' ? 'cobrancas-navbar-status-atrasada' : 'cobrancas-navbar-status-hoje')
                .text(alerta.status_alerta);

            $('<strong></strong>').text(alerta.titulo).appendTo(linhaTitulo);
            status.appendTo(linhaTitulo);
            linhaTitulo.appendTo(item);
            $('<span class="cobrancas-navbar-item-cliente"></span>').text(alerta.cliente + ' - parcela ' + rotuloParcela + ' - ' + textoRestantes).appendTo(item);
            $('<span class="cobrancas-navbar-item-detalhe"></span>')
                .text(formataVencimentoAlertaCobranca(alerta.vencimento) + ' | ' + formataMoedaAlertaCobranca(alerta.valor_com_juros))
                .appendTo(item);

            return item;
        }

        /**
         * Atualiza o balao sem permitir que alertas pendentes sejam dispensados.
         */
        function renderizaAlertasCobrancas(resposta) {
            var total = Number(resposta.total) || 0;
            var alertas = Array.isArray(resposta.alertas) ? resposta.alertas : [];
            var totalElemento = $('.cobrancas-navbar-total');
            var lista = $('.cobrancas-navbar-lista').empty();

            $('.cobrancas-navbar-cabecalho').text(total === 1 ? '1 cobranca requer atencao' : total + ' cobrancas requerem atencao');
            totalElemento.text(total > 99 ? '99+' : total).toggleClass('d-none', total === 0);

            if (total === 0) {
                $('<span class="dropdown-item text-muted text-center"></span>').text('Nenhuma cobranca para hoje ou atrasada.').appendTo(lista);
                return;
            }

            alertas.slice(0, limiteAlertasVisiveis).forEach(function(alerta, indice) {
                criaItemAlertaCobranca(alerta).appendTo(lista);

                if (indice < Math.min(alertas.length, limiteAlertasVisiveis) - 1) {
                    $('<div class="dropdown-divider"></div>').appendTo(lista);
                }
            });

            if (alertas.length > limiteAlertasVisiveis) {
                $('<span class="dropdown-item text-muted text-center"></span>')
                    .text('Mais ' + (alertas.length - limiteAlertasVisiveis) + ' alertas na central.')
                    .appendTo(lista);
            }
        }

        /**
         * Consulta periodicamente os alertas para detectar a virada do dia sem recarregar a pagina.
         */
        function consultaAlertasCobrancas() {
            $.getJSON(endpointAlertasCobrancas)
                .done(renderizaAlertasCobrancas);
        }

        $(function() {
            consultaAlertasCobrancas();
            window.setInterval(consultaAlertasCobrancas, 60000);
        });
    })();
</script>
