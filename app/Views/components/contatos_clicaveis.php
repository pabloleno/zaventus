<?php

use App\Libraries\ContatoPadrao;

$dadosContato = is_array($contato ?? null) ? $contato : [];
$telefone = ContatoPadrao::telefone($dadosContato);
$whatsapp = ContatoPadrao::whatsapp($dadosContato);
$email = ContatoPadrao::email($dadosContato);
$telefoneLink = ContatoPadrao::telefoneLink($telefone);
$whatsappLink = ContatoPadrao::whatsappLink($whatsapp);
$emailLink = ContatoPadrao::emailLink($email);
$possuiContato = $telefoneLink !== '' || $whatsappLink !== '' || $emailLink !== '';
?>

<div class="contatos-listagem">
    <?php if ($telefoneLink !== '') : ?>
        <a href="<?= esc($telefoneLink) ?>" class="contato-listagem-link contato-listagem-telefone" title="Ligar para <?= esc($telefone) ?>" aria-label="Ligar para <?= esc($telefone) ?>">
            <i class="fas fa-phone" aria-hidden="true"></i>
            <span><?= esc($telefone) ?></span>
        </a>
    <?php endif; ?>

    <?php if ($whatsappLink !== '') : ?>
        <a href="<?= esc($whatsappLink) ?>" class="contato-listagem-link contato-listagem-whatsapp" target="_blank" rel="noopener" title="Conversar pelo WhatsApp com <?= esc($whatsapp) ?>" aria-label="Conversar pelo WhatsApp com <?= esc($whatsapp) ?>">
            <i class="fab fa-whatsapp" aria-hidden="true"></i>
            <span><?= esc($whatsapp) ?></span>
        </a>
    <?php endif; ?>

    <?php if ($emailLink !== '') : ?>
        <a href="<?= esc($emailLink) ?>" class="contato-listagem-link contato-listagem-email" title="Enviar e-mail para <?= esc($email) ?>" aria-label="Enviar e-mail para <?= esc($email) ?>">
            <i class="fas fa-envelope" aria-hidden="true"></i>
            <span><?= esc($email) ?></span>
        </a>
    <?php endif; ?>

    <?php if (! $possuiContato) : ?>
        <span class="text-muted">Não informado</span>
    <?php endif; ?>
</div>
