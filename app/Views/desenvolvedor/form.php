<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <form action="/desenvolvedor/store" method="post" autocomplete="off">
                <?= csrf_field() ?>
                <input type="hidden" name="id_integracao" value="<?= $integracao['id_integracao'] ?>">

                <div class="card">
                    <div class="card-header integracao-pagamento-form-header">
                        <h6 class="card-title"><i class="fas fa-plug"></i> <?= esc($integracao['nome']) ?></h6>
                        <a href="/desenvolvedor" class="btn btn-success"><i class="fas fa-arrow-left"></i> Voltar</a>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Importante:</strong> salvar e testar estas credenciais valida apenas a autenticação, mas não executa cobranças automaticamente.
                            <?= esc($detalhes_provedor['recomendacao']) ?>
                        </div>
                        <?php if (! ($detalhes_provedor['ativacao_suportada'] ?? false)) : ?>
                            <div class="alert alert-secondary">
                                <i class="fas fa-info-circle"></i>
                                <?= esc($detalhes_provedor['motivo_teste_indisponivel'] ?? 'Este conector ainda não pode ser ativado.') ?>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Ambiente</label>
                                    <select class="form-control select2" name="ambiente" required>
                                        <option value="sandbox" <?= $integracao['ambiente'] === 'sandbox' ? 'selected' : '' ?>>Sandbox / homologacao</option>
                                        <option value="producao" <?= $integracao['ambiente'] === 'producao' ? 'selected' : '' ?>>Producao</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control select2" name="ativo" <?= ! ($detalhes_provedor['ativacao_suportada'] ?? false) ? 'disabled' : '' ?>>
                                        <option value="0" <?= (int) $integracao['ativo'] !== 1 ? 'selected' : '' ?>>Inativa</option>
                                        <option value="1" <?= (int) $integracao['ativo'] === 1 ? 'selected' : '' ?>>Ativa após teste válido</option>
                                    </select>
                                    <?php if (! ($detalhes_provedor['ativacao_suportada'] ?? false)) : ?>
                                        <input type="hidden" name="ativo" value="0">
                                    <?php else : ?>
                                        <small class="form-text text-muted">Para ativar, salve como inativa, execute o diagnóstico e volte a esta tela.</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ((int) $integracao['api_publica'] === 1) : ?>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><?= esc($detalhes_provedor['credencial_publica']) ?></label>
                                        <input class="form-control" type="text" name="credencial_publica" value="<?= esc(old('credencial_publica', $integracao['credencial_publica'] ?? '')) ?>">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label><?= esc($detalhes_provedor['credencial_secreta']) ?></label>
                                        <input class="form-control" type="password" name="credencial_secreta" value="" placeholder="<?= $integracao['segredo_configurado'] ? 'Credencial ja configurada; deixe vazio para manter' : 'Informe para ativar' ?>">
                                        <small class="form-text text-muted">A credencial e criptografada antes de ser salva e nunca e exibida novamente.</small>
                                    </div>
                                </div>
                                <?php if ($integracao['segredo_configurado']) : ?>
                                    <div class="col-lg-12">
                                        <div class="custom-control custom-checkbox mb-3">
                                            <input class="custom-control-input" id="remover-credencial" type="checkbox" name="remover_credencial_secreta" value="1">
                                            <label class="custom-control-label" for="remover-credencial">Remover credencial secreta armazenada</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Observacoes tecnicas</label>
                                    <textarea class="form-control" name="observacoes" rows="4"><?= esc(old('observacoes', $integracao['observacoes'] ?? '')) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer integracao-pagamento-form-acoes">
                        <a class="btn btn-outline-info" href="<?= esc($integracao['documentacao_url']) ?>" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-external-link-alt"></i> Documentacao oficial
                        </a>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Salvar configuracao</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
