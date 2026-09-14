<?php
    use App\Libraries\AtendimentoGrafica;
    use App\Libraries\OrcamentoCalculo;

    $ordem = $ordem ?? [];
    $empresa = $empresa ?? [];
    $cliente = $ordem['cliente'] ?? [];
    $totais = $ordem['totais'] ?? [];
    $financeiro = $ordem['financeiro'] ?? [];
    $recebimentosConfirmados = array_values(array_filter($financeiro['recebimentos'] ?? [], static fn (array $recebimento): bool => empty($recebimento['estornado_at']) && empty($recebimento['deleted_at'])));
    $numero = (string) ($ordem['numero'] ?? ('OS-' . ($ordem['id_ordem'] ?? '')));
    $itens = array_values(array_filter($ordem['itens'] ?? [], static fn (array $item): bool => empty($item['removido_at'])));
    $servicos = array_values(array_filter($itens, static fn (array $item): bool => empty($item['cortesia'])));
    $cortesias = array_values(array_filter($itens, static fn (array $item): bool => ! empty($item['cortesia'])));
    $monetario = static function ($valor): string {
        [$inteiro, $centavos] = explode('.', OrcamentoCalculo::decimal($valor ?? '0', 2));
        return 'R$ ' . preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $inteiro) . ',' . $centavos;
    };
    $decimal = static function ($valor): string {
        $valor = rtrim(rtrim(OrcamentoCalculo::decimal($valor ?? '0', 4), '0'), '.');
        return str_replace('.', ',', $valor === '' ? '0' : $valor);
    };
    $data = static function ($valor, bool $comHora = false): string {
        $valor = is_string($valor) ? trim($valor) : $valor;
        if (! is_string($valor) || $valor === '' || str_starts_with($valor, '0000-00-00')) {
            return 'A combinar';
        }
        try {
            return (new DateTimeImmutable($valor))->format($comHora ? 'd/m/Y H:i' : 'd/m/Y');
        } catch (Throwable $exception) {
            return 'A combinar';
        }
    };
    $primeiroPreenchido = static function (array $valores): string {
        foreach ($valores as $valor) {
            $valor = trim((string) $valor);
            if ($valor !== '') {
                return $valor;
            }
        }
        return '';
    };
    $pessoaJuridica = ($cliente['tipo'] ?? 1) != 1;
    $nomeCliente = $primeiroPreenchido($pessoaJuridica
        ? [$cliente['razao_social'] ?? '', $cliente['nome'] ?? '', 'Cliente']
        : [$cliente['nome'] ?? '', $cliente['razao_social'] ?? '', 'Cliente']);
    $nomeEmpresa = trim((string) ($empresa['nome_fantasia'] ?? '')) ?: ($empresa['razao_social'] ?? '');
    $enderecoEmpresa = trim((string) ($empresa['endereco'] ?? '')) ?: implode(', ', array_filter([
        $empresa['logradouro'] ?? '', $empresa['numero'] ?? '', $empresa['complemento'] ?? '',
        $empresa['bairro'] ?? '', $empresa['municipio'] ?? '', $empresa['UF'] ?? '', $empresa['cep'] ?? '',
    ]));
    $telefoneEmpresa = $primeiroPreenchido([$empresa['telefone_fixo'] ?? '', $empresa['telefone'] ?? '', $empresa['celular'] ?? '']);
    $telefoneCliente = $primeiroPreenchido([$cliente['whatsapp'] ?? '', $cliente['celular'] ?? '', $cliente['telefone_fixo'] ?? '', $cliente['comercial'] ?? '', $cliente['residencial'] ?? '']);
    $documentoCliente = $primeiroPreenchido($pessoaJuridica ? [$cliente['cnpj'] ?? '', $cliente['cpf'] ?? ''] : [$cliente['cpf'] ?? '', $cliente['cnpj'] ?? '']);
    $atendente = $ordem['atendente']['primeiro_nome'] ?? $ordem['vendedor']['nome'] ?? '';
    $vendedor = $ordem['vendedor']['nome'] ?? '';
    $tecnico = $ordem['tecnico']['nome'] ?? '';
    $logo = ltrim(trim((string) ($empresa['logo_login'] ?? '')) ?: 'assets/img/zaventus-logo-completa-353079a01cd5.png', '/');
    $logoValido = preg_match('/\A[A-Za-z0-9_\/ .-]+\.(png|jpe?g|webp|gif)\z/i', $logo) === 1 && strpos($logo, '..') === false;
    $externo = count(array_filter($itens, static fn (array $item): bool => in_array($item['tipo_execucao'] ?? 'interna', ['externa', 'mista'], true) || ! empty($item['necessita_instalacao']))) > 0;
    $metricas = ['fixo' => 'Preço fixo', 'unidade' => 'Por unidade', 'quantidade' => 'Por quantidade', 'metro_linear' => 'Por metro linear', 'metro_quadrado' => 'Por m²'];
    $artes = ['nao_necessita' => 'Não necessita arte', 'cliente' => 'Arte fornecida pelo cliente', 'grafica' => 'A gráfica desenvolverá a arte'];
    $medidas = static function (array $item) use ($decimal): string {
        $partes = [];
        if (! empty($item['largura']) && bccomp((string) $item['largura'], '0', 4) > 0) {
            $medida = $decimal($item['largura']);
            if (! empty($item['altura']) && bccomp((string) $item['altura'], '0', 4) > 0) {
                $medida .= ' × ' . $decimal($item['altura']);
            }
            $partes[] = $medida . ' ' . ($item['unidade_dimensao'] ?? 'm');
        }
        if (! empty($item['area']) && $item['area'] !== '0.0000') {
            $partes[] = $decimal($item['area']) . ' m²';
        }
        return implode(' · ', $partes);
    };
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orçamento <?= esc($numero) ?> · <?= esc($nomeEmpresa) ?></title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #edf0f4; color: #202b3a; font: 14px/1.5 Arial, Helvetica, sans-serif; }
        .toolbar { max-width: 960px; margin: 22px auto 14px; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .toolbar a { color: #263b51; text-decoration: none; }
        .toolbar button { border: 0; border-radius: 6px; padding: 12px 18px; background: #253d58; color: #fff; font: inherit; font-weight: bold; cursor: pointer; }
        .toolbar small { display: block; margin-top: 5px; color: #556274; }
        .paper { max-width: 920px; margin: 0 auto 28px; padding: 42px 46px; background: white; box-shadow: 0 2px 14px #26364d12; }
        .document-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 26px; border-bottom: 3px solid #253d58; padding-bottom: 20px; }
        .brand { display: flex; align-items: flex-start; gap: 16px; min-width: 0; flex: 1; }
        .logo { max-width: 130px; max-height: 85px; object-fit: contain; }
        .company { font-size: 11px; line-height: 1.6; color: #556274; overflow-wrap: anywhere; }
        .company strong { display: block; font-size: 18px; color: #202b3a; margin-bottom: 4px; }
        .identity { text-align: right; flex: 0 0 215px; }
        .eyebrow { font-size: 11px; font-weight: bold; letter-spacing: 1.6px; text-transform: uppercase; color: #66768a; }
        h1 { font-size: 23px; line-height: 1.25; margin: 6px 0 9px; overflow-wrap: anywhere; }
        h2 { margin: 24px 0 10px; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: #253d58; }
        .muted { color: #66768a; }
        .small { font-size: 11px; }
        .grid { display: grid; grid-template-columns: 1.3fr 1fr; gap: 24px; margin-top: 20px; }
        .box { padding: 13px 16px; background: #f6f8fb; border: 1px solid #e3e8ef; border-radius: 4px; overflow-wrap: anywhere; }
        .box h2 { margin: 0 0 8px; }
        .line { margin: 3px 0; }
        .label { color: #617084; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead { display: table-header-group; }
        th { padding: 9px 8px; text-align: left; background: #eef2f7; border-bottom: 1px solid #d4dce6; font-size: 10px; color: #465970; }
        td { padding: 11px 8px; vertical-align: top; border-bottom: 1px solid #e4e9ef; }
        .numeric { text-align: right; white-space: nowrap; }
        .description { width: 49%; overflow-wrap: anywhere; }
        .item-name { font-weight: bold; }
        .detail { font-size: 11px; color: #66768a; margin-top: 3px; white-space: pre-line; }
        .gift { display: flex; justify-content: space-between; gap: 22px; padding: 12px 0; border-bottom: 1px solid #e4e9ef; break-inside: avoid; }
        .gift strong { color: #235f52; }
        .totals { width: 330px; max-width: 100%; margin: 20px 0 0 auto; }
        .totals div { display: flex; justify-content: space-between; gap: 20px; padding: 5px 0; }
        .totals .grand { border-top: 2px solid #253d58; font-size: 20px; font-weight: bold; padding-top: 10px; margin-top: 6px; }
        .payment-summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 12px 0; }
        .payment-summary .box { padding: 10px 12px; }
        .payment-summary strong { display: block; font-size: 16px; }
        .text { white-space: pre-line; overflow-wrap: anywhere; }
        .acceptance { margin-top: 28px; padding-top: 18px; border-top: 1px solid #d4dce6; break-inside: avoid; }
        .signatures { display: grid; grid-template-columns: 1fr 115px 1fr; gap: 20px; margin-top: 32px; }
        .signature { border-top: 1px solid #7c8a9b; padding-top: 5px; font-size: 10px; color: #66768a; }
        .footer { text-align: center; color: #7c8a9b; margin-top: 22px; font-size: 10px; }
        tr, .box, .totals, .payment-summary { break-inside: avoid; }
        @page { size: A4; margin: 14mm; }
        @media print {
            body { background: #fff; font-size: 10pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .toolbar { display: none; }
            .paper { max-width: none; margin: 0; padding: 0; box-shadow: none; }
            .logo { max-width: 105px; max-height: 70px; }
            h2 { break-after: avoid; }
            .document-header { break-inside: avoid; }
            .box { background: #f6f8fb; }
        }
        @media screen and (max-width: 650px) {
            .toolbar { align-items: flex-start; }
            .paper { padding: 22px 16px; }
            .document-header { flex-direction: column; }
            .identity { text-align: left; flex: auto; }
            .grid { grid-template-columns: 1fr; gap: 12px; }
            .payment-summary { grid-template-columns: 1fr; }
            .logo { max-width: 90px; }
            th, td { padding: 8px 4px; font-size: 10px; }
            .signatures { grid-template-columns: 1fr; gap: 34px; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="<?= esc(base_url('ordensDeServicos/show/' . (int) ($ordem['id_ordem'] ?? 0))) ?>">← Voltar ao atendimento</a>
        <div><button type="button" onclick="window.print()">Imprimir / Salvar PDF</button><small>Para gerar PDF, selecione “Salvar como PDF” na impressão.</small></div>
    </div>
    <main class="paper">
        <header class="document-header">
            <div class="brand">
                <?php if ($logoValido) : ?><img class="logo" src="<?= esc(base_url($logo)) ?>" alt="<?= esc($nomeEmpresa) ?>"><?php endif; ?>
                <div class="company">
                    <strong><?= esc($nomeEmpresa) ?></strong>
                    <?php if (! empty($empresa['razao_social']) && $empresa['razao_social'] !== $nomeEmpresa) : ?><div><?= esc($empresa['razao_social']) ?></div><?php endif; ?>
                    <?php if (! empty($empresa['cnpj'])) : ?><div>CNPJ <?= esc($empresa['cnpj']) ?></div><?php endif; ?>
                    <?php if ($enderecoEmpresa !== '') : ?><div><?= esc($enderecoEmpresa) ?></div><?php endif; ?>
                    <?php if ($telefoneEmpresa !== '') : ?><div>Telefone: <?= esc($telefoneEmpresa) ?></div><?php endif; ?>
                    <?php if (! empty($empresa['whatsapp'])) : ?><div>WhatsApp: <?= esc($empresa['whatsapp']) ?></div><?php endif; ?>
                    <?php if (! empty($empresa['email'])) : ?><div><?= esc($empresa['email']) ?></div><?php endif; ?>
                </div>
            </div>
            <div class="identity">
                <div class="eyebrow">Orçamento</div>
                <h1><?= esc($numero) ?></h1>
                <div class="small">Emissão: <?= esc($data(($ordem['data_de_entrada'] ?? '') . ' ' . ($ordem['hora_de_entrada'] ?? ''), true)) ?></div>
                <div class="small">Validade: <?= esc($data($ordem['validade_orcamento'] ?? null)) ?></div>
                <div class="small muted"><?= esc(AtendimentoGrafica::STATUS[AtendimentoGrafica::status($ordem)]) ?></div>
            </div>
        </header>

        <div class="grid">
            <section class="box">
                <h2>Cliente</h2>
                <strong><?= esc($nomeCliente) ?></strong>
                <?php if ($documentoCliente !== '') : ?><div class="line small">CPF/CNPJ: <?= esc($documentoCliente) ?></div><?php endif; ?>
                <?php if ($telefoneCliente !== '') : ?><div class="line small">Contato: <?= esc($telefoneCliente) ?></div><?php endif; ?>
                <?php if (! empty($cliente['email'])) : ?><div class="line small"><?= esc($cliente['email']) ?></div><?php endif; ?>
            </section>
            <section class="box">
                <h2>Atendimento e prazo</h2>
                <?php if ($atendente !== '') : ?><div class="line"><span class="label">Atendido por</span><br><?= esc($atendente) ?></div><?php endif; ?>
                <?php if ($vendedor !== '') : ?><div class="line small">Vendedor: <?= esc($vendedor) ?></div><?php endif; ?>
                <?php if ($tecnico !== '') : ?><div class="line small">Técnico: <?= esc($tecnico) ?></div><?php endif; ?>
                <div class="line small">Previsão de conclusão: <strong><?= esc($data($ordem['previsao_conclusao'] ?? null)) ?></strong></div>
            </section>
        </div>

        <h2>Serviços</h2>
        <table>
            <thead><tr><th class="description">Descrição e medidas</th><th class="numeric">Qtd.</th><th>Precificação</th><th class="numeric">Preço</th><th class="numeric">Total</th></tr></thead>
            <tbody>
                <?php foreach ($servicos as $item) : ?>
                    <tr>
                        <td class="description"><div class="item-name"><?= esc($item['nome'] ?? '') ?></div>
                            <?php if (! empty($item['descricao'])) : ?><div class="detail"><?= esc($item['descricao']) ?></div><?php endif; ?>
                            <?php if ($medidas($item) !== '') : ?><div class="detail"><?= esc($medidas($item)) ?></div><?php endif; ?>
                            <div class="detail"><?= esc($artes[$item['arte'] ?? 'nao_necessita'] ?? '') ?></div>
                        </td>
                        <td class="numeric"><?= esc($decimal($item['quantidade'] ?? 1)) ?> <?= esc($item['unidade'] ?? 'un') ?></td>
                        <td><?= esc($metricas[$item['tipo_preco'] ?? 'unidade'] ?? '') ?></td>
                        <td class="numeric"><?= esc($monetario($item['valor'] ?? '0')) ?></td>
                        <td class="numeric"><strong><?= esc($monetario($item['total'] ?? $item['subtotal'] ?? '0')) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($servicos === []) : ?><tr><td colspan="5" class="muted">Não há serviços cobrados neste orçamento.</td></tr><?php endif; ?>
            </tbody>
        </table>

        <?php if ($cortesias !== []) : ?>
            <h2>Cortesias</h2>
            <?php foreach ($cortesias as $item) : ?>
                <div class="gift"><div><b><?= esc($item['nome'] ?? '') ?></b>
                    <div class="detail"><?= esc($decimal($item['quantidade'] ?? 1)) ?> <?= esc($item['unidade'] ?? 'un') ?><?= $medidas($item) !== '' ? ' · ' . esc($medidas($item)) : '' ?></div>
                    <?php if (! empty($item['descricao'])) : ?><div class="detail"><?= esc($item['descricao']) ?></div><?php endif; ?>
                    <div class="detail"><?= esc($artes[$item['arte'] ?? 'nao_necessita'] ?? '') ?></div>
                </div><strong>Sem custo</strong></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <section class="totals">
            <div><span>Subtotal dos serviços</span><span><?= esc($monetario($totais['subtotal'] ?? '0')) ?></span></div>
            <div><span>Desconto<?= ($ordem['desconto_tipo'] ?? '') === 'percentual' ? ' (' . esc($decimal($ordem['desconto_informado'] ?? '0')) . '%)' : '' ?></span><span>− <?= esc($monetario($totais['desconto'] ?? '0')) ?></span></div>
            <?php if (($totais['frete'] ?? '0.00') !== '0.00') : ?><div><span>Frete</span><span><?= esc($monetario($totais['frete'])) ?></span></div><?php endif; ?>
            <?php if (($totais['outros'] ?? '0.00') !== '0.00') : ?><div><span>Adicionais</span><span><?= esc($monetario($totais['outros'])) ?></span></div><?php endif; ?>
            <div class="grand"><span>Total</span><span><?= esc($monetario($totais['total'] ?? '0')) ?></span></div>
        </section>

        <h2>Pagamento</h2>
        <?php if (! empty($ordem['condicao_pagamento'])) : ?><div class="text"><?= esc($ordem['condicao_pagamento']) ?></div><?php endif; ?>
        <div class="payment-summary">
            <div class="box"><span class="label">Entrada combinada</span><strong><?= ! empty($ordem['entrada_necessaria']) ? esc($monetario($ordem['valor_entrada'] ?? '0')) : 'Não exigida' ?></strong></div>
            <div class="box"><span class="label">Valor já recebido</span><strong><?= esc($monetario($financeiro['pago'] ?? '0')) ?></strong></div>
            <div class="box"><span class="label">Saldo restante</span><strong><?= esc($monetario($financeiro['saldo'] ?? $totais['total'] ?? '0')) ?></strong></div>
        </div>
        <?php if (! empty($ordem['parcelas'])) : ?>
            <table><thead><tr><th>Pagamento combinado</th><th>Vencimento</th><th class="numeric">Valor</th></tr></thead><tbody>
                <?php foreach ($ordem['parcelas'] as $parcela) : ?>
                    <?php if (! empty($parcela['removido_at'])) { continue; } ?>
                    <tr><td><?= esc($parcela['forma_de_pagamento'] ?? '') ?></td><td><?= esc($data($parcela['data_de_vencimento'] ?? null)) ?></td><td class="numeric"><?= esc($monetario($parcela['valor_da_parcela'] ?? '0')) ?></td></tr>
                <?php endforeach; ?>
            </tbody></table>
        <?php endif; ?>

        <?php if ($recebimentosConfirmados !== []) : ?>
            <h2>Recebimentos registrados</h2>
            <table><thead><tr><th>Forma de pagamento</th><th>Data</th><th class="numeric">Valor recebido</th></tr></thead><tbody>
                <?php foreach ($recebimentosConfirmados as $recebimento) : ?>
                    <tr><td><?= esc($recebimento['forma_de_pagamento'] ?? '') ?></td><td><?= esc($data($recebimento['data'] ?? null)) ?></td><td class="numeric"><?= esc($monetario($recebimento['valor'] ?? '0')) ?></td></tr>
                <?php endforeach; ?>
            </tbody></table>
        <?php endif; ?>

        <?php if ($externo) : ?>
            <h2>Execução externa / instalação</h2>
            <section class="box">
                <div class="text"><?= esc($ordem['execucao_endereco'] ?? 'Endereço a combinar') ?></div>
                <?php if (! empty($ordem['execucao_referencia'])) : ?><div class="small">Referência: <?= esc($ordem['execucao_referencia']) ?></div><?php endif; ?>
                <div class="line">Previsão: <?= esc($data($ordem['execucao_prevista'] ?? null, true)) ?></div>
                <?php if (! empty($ordem['execucao_responsavel'])) : ?><div class="small">Contato no local: <?= esc($ordem['execucao_responsavel']) ?> <?= esc($ordem['execucao_telefone'] ?? '') ?></div><?php endif; ?>
                <?php if (! empty($ordem['execucao_observacoes'])) : ?><div class="text small"><?= esc($ordem['execucao_observacoes']) ?></div><?php endif; ?>
            </section>
        <?php endif; ?>
        <?php if (! empty($ordem['observacoes'])) : ?><h2>Observações</h2><div class="text"><?= esc($ordem['observacoes']) ?></div><?php endif; ?>
        <?php if (! empty($empresa['condicoes_orcamento'])) : ?><h2>Condições gerais</h2><div class="text small"><?= esc($empresa['condicoes_orcamento']) ?></div><?php endif; ?>
        <section class="acceptance">
            <h2 style="margin-top: 0">Aceite do cliente</h2>
            <div class="small">Confirmo a descrição, as medidas, os valores e as condições deste orçamento.</div>
            <div class="signatures"><div class="signature">Nome</div><div class="signature">Data</div><div class="signature">Assinatura</div></div>
        </section>
        <footer class="footer"><?= esc($nomeEmpresa) ?> · <?= esc($numero) ?></footer>
    </main>
</body>
</html>
