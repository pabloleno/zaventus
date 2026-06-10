<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <div class="alert alert-info integracao-pagamento-aviso">
                <i class="fas fa-shield-alt"></i>
                <strong>Diagnóstico seguro:</strong> o teste valida somente a autenticação por uma consulta de leitura.
                Nenhuma cobrança é criada. O vínculo com produtos e serviços continua manual até existir um conector transacional homologado.
            </div>

            <div class="row">
                <?php foreach ($integracoes as $integracao) : ?>
                    <div class="col-xl-4 col-lg-6 d-flex">
                        <div class="card integracao-pagamento-card">
                            <div class="card-header">
                                <h6 class="card-title mb-2 mb-sm-0">
                                    <i class="fas fa-plug"></i> <?= esc($integracao['nome']) ?>
                                </h6>
                                <span class="badge badge-<?= esc($integracao['status_classe']) ?> integracao-pagamento-status">
                                    <?= esc($integracao['status_integracao']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <p><?= esc($integracao['descricao']) ?></p>
                                <p class="mb-1"><strong>Recursos:</strong> <?= esc($integracao['detalhes']['recursos']) ?></p>
                                <p class="mb-1"><strong>Autenticacao:</strong> <?= esc($integracao['tipo_autenticacao']) ?></p>
                                <p class="mb-3"><strong>Ambiente:</strong> <?= esc(ucfirst($integracao['ambiente'])) ?></p>

                                <div class="integracao-pagamento-resumo">
                                    <div><span>Vínculos</span><strong><?= (int) $integracao['formas_vinculadas'] ?></strong></div>
                                    <div><span>Produtos</span><strong><?= (int) $integracao['formas_produtos'] ?></strong></div>
                                    <div><span>Serviços</span><strong><?= (int) $integracao['formas_servicos'] ?></strong></div>
                                </div>

                                <div class="integracao-pagamento-teste">
                                    <strong>Último diagnóstico</strong>
                                    <?php if (! empty($integracao['ultimo_teste_em'])) : ?>
                                        <span class="badge badge-<?= ($integracao['ultimo_teste_status'] ?? '') === 'sucesso' ? 'success' : (($integracao['ultimo_teste_status'] ?? '') === 'erro' ? 'danger' : 'secondary') ?>">
                                            <?= esc(ucfirst($integracao['ultimo_teste_status'])) ?>
                                        </span>
                                        <small><?= esc(date('d/m/Y H:i', strtotime($integracao['ultimo_teste_em']))) ?></small>
                                        <p class="mb-0"><?= esc($integracao['ultimo_teste_mensagem'] ?? '') ?></p>
                                    <?php else : ?>
                                        <p class="mb-0 text-muted">Ainda não executado.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="integracao-pagamento-acoes">
                                    <a class="btn btn-primary" href="/desenvolvedor/edit/<?= $integracao['id_integracao'] ?>">
                                        <i class="fas fa-cog"></i> Configurar
                                    </a>
                                    <?php if ($integracao['pode_testar']) : ?>
                                        <form action="/desenvolvedor/testar/<?= $integracao['id_integracao'] ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-success" type="submit">
                                                <i class="fas fa-vial"></i> Testar conexão
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <a class="btn btn-outline-info" href="<?= esc($integracao['documentacao_url']) ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-external-link-alt"></i> Documentação
                                    </a>
                                </div>
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped tabela-listagem">
                            <thead>
                                <tr>
                                    <th>Forma</th>
                                    <th>tPag</th>
                                    <th>Produtos</th>
                                    <th>Serviços</th>
                                    <th>Provedor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($formas_de_pagamento as $forma) : ?>
                                    <tr>
                                        <td><?= esc($forma['nome']) ?></td>
                                        <td><?= esc($forma['codigo_nfce']) ?></td>
                                        <td><?= (int) $forma['disponivel_produtos'] === 1 ? 'Sim' : 'Não' ?></td>
                                        <td><?= (int) $forma['disponivel_servicos'] === 1 ? 'Sim' : 'Não' ?></td>
                                        <td><?= esc($forma['integracao_nome'] ?? 'Manual / sem API') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <small class="form-text text-muted">
                        O vínculo é alterado em Configs &gt; Sistema &gt; Formas de Pagamento.
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
        var resultadoTeste = <?= json_encode(session()->getFlashdata('resultado_teste_integracao')) ?>;

        if (alerta === 'success_integracao_pagamento') {
            Swal.fire({ type: 'success', title: 'Configuracao do provedor salva com sucesso!' });
        } else if (alerta === 'success_teste_integracao_pagamento') {
            Swal.fire({ type: 'success', title: 'Autenticação validada', text: resultadoTeste });
        } else if (alerta === 'error_teste_integracao_pagamento') {
            Swal.fire({ type: 'warning', title: 'Diagnóstico concluído', text: resultadoTeste });
        } else if (alerta === 'error_integracao_pagamento' && erros.length) {
            Swal.fire({ type: 'error', title: 'Nao foi possivel salvar', text: erros.join(' ') });
        }
    });
</script>
