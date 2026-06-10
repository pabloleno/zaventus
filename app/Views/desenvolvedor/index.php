<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="alert alert-info">
                <i class="fas fa-shield-alt"></i>
                Este painel configura e vincula provedores. Ele nao dispara cobrancas nem confirma recebimentos sem um conector homologado e credenciais validas.
            </div>

            <div class="row">
                <?php foreach ($integracoes as $integracao) : ?>
                    <div class="col-lg-6">
                        <div class="card integracao-pagamento-card">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <i class="fas fa-plug"></i> <?= esc($integracao['nome']) ?>
                                </h6>
                                <span class="badge badge-<?= esc($integracao['status_classe']) ?> float-right">
                                    <?= esc($integracao['status_integracao']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <p><?= esc($integracao['descricao']) ?></p>
                                <p class="mb-1"><strong>Recursos:</strong> <?= esc($integracao['detalhes']['recursos']) ?></p>
                                <p class="mb-1"><strong>Autenticacao:</strong> <?= esc($integracao['tipo_autenticacao']) ?></p>
                                <p class="mb-3"><strong>Ambiente:</strong> <?= esc(ucfirst($integracao['ambiente'])) ?></p>
                                <a class="btn btn-primary" href="/desenvolvedor/edit/<?= $integracao['id_integracao'] ?>">
                                    <i class="fas fa-cog"></i> Configurar
                                </a>
                                <a class="btn btn-outline-info" href="<?= esc($integracao['documentacao_url']) ?>" target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-external-link-alt"></i> Documentacao oficial
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="card-title"><i class="fas fa-link"></i> Formas de pagamento e provedores vinculados</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped tabela-listagem">
                        <thead>
                            <tr>
                                <th>Forma</th>
                                <th>tPag</th>
                                <th>Produtos</th>
                                <th>Servicos</th>
                                <th>Provedor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formas_de_pagamento as $forma) : ?>
                                <tr>
                                    <td><?= esc($forma['nome']) ?></td>
                                    <td><?= esc($forma['codigo_nfce']) ?></td>
                                    <td><?= (int) $forma['disponivel_produtos'] === 1 ? 'Sim' : 'Nao' ?></td>
                                    <td><?= (int) $forma['disponivel_servicos'] === 1 ? 'Sim' : 'Nao' ?></td>
                                    <td><?= esc($forma['integracao_nome'] ?? 'Manual / sem API') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <small class="form-text text-muted">
                        O vinculo e alterado em Configs &gt; Sistema &gt; Formas de Pagamento.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var alerta = <?= json_encode(session()->getFlashdata('alert')) ?>;
        var erros = <?= json_encode(array_values((array) session()->getFlashdata('errors'))) ?>;

        if (alerta === 'success_integracao_pagamento') {
            Swal.fire({ type: 'success', title: 'Configuracao do provedor salva com sucesso!' });
        } else if (alerta === 'error_integracao_pagamento' && erros.length) {
            Swal.fire({ type: 'error', title: 'Nao foi possivel salvar', text: erros.join(' ') });
        }
    });
</script>
