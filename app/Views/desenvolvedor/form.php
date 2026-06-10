<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <form action="/desenvolvedor/store" method="post" autocomplete="off">
                <?= csrf_field() ?>
                <input type="hidden" name="id_integracao" value="<?= $integracao['id_integracao'] ?>">

                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title"><i class="fas fa-plug"></i> <?= esc($integracao['nome']) ?></h6>
                        <a href="/desenvolvedor" class="btn btn-success float-right"><i class="fas fa-arrow-left"></i> Voltar</a>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Importante:</strong> salvar estas credenciais prepara a homologacao, mas nao executa cobrancas automaticamente.
                            <?= esc($detalhes_provedor['recomendacao']) ?>
                        </div>

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
                                    <select class="form-control select2" name="ativo" <?= (int) $integracao['api_publica'] !== 1 ? 'disabled' : '' ?>>
                                        <option value="0" <?= (int) $integracao['ativo'] !== 1 ? 'selected' : '' ?>>Inativa</option>
                                        <option value="1" <?= (int) $integracao['ativo'] === 1 ? 'selected' : '' ?>>Ativa para homologacao</option>
                                    </select>
                                    <?php if ((int) $integracao['api_publica'] !== 1) : ?>
                                        <input type="hidden" name="ativo" value="0">
                                        <small class="form-text text-muted">Nao existe API publica suportada para este provedor.</small>
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
                    <div class="card-footer text-right">
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
