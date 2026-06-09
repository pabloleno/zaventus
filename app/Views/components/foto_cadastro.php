<?php
    use App\Libraries\ImagemCadastro;

    $editavel = $editavel ?? false;
    $coluna = $coluna ?? ($editavel ? 'col-lg-12' : 'col-lg-3');
    $fotoUrl = ImagemCadastro::url($foto ?? '');
    $erroFoto = $editavel ? session()->getFlashdata('erro_foto') : null;
?>

<div class="<?= esc($coluna) ?>">
    <div class="form-group">
        <label>Foto de identificação</label>
        <div class="foto-cadastro-container">
            <img
                id="<?= $editavel ? 'foto-cadastro-preview' : '' ?>"
                src="<?= esc($fotoUrl) ?>"
                alt="Foto de identificação"
                class="foto-cadastro-preview"
            >
            <?php if ($editavel) : ?>
                <div class="foto-cadastro-controles">
                    <input
                        type="file"
                        class="form-control-file"
                        id="foto-cadastro-input"
                        name="foto"
                        accept="image/png,image/jpeg,image/webp"
                    >
                    <small class="form-text text-muted">PNG, JPG ou WEBP, com no máximo 2 MB.</small>
                    <?php if ($erroFoto) : ?>
                        <div class="text-danger mt-1"><?= esc($erroFoto) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($editavel) : ?>
    <script>
        document.getElementById('foto-cadastro-input')?.addEventListener('change', function(event) {
            const arquivo = event.target.files && event.target.files[0];

            if (arquivo) {
                document.getElementById('foto-cadastro-preview').src = URL.createObjectURL(arquivo);
            }
        });
    </script>
<?php endif; ?>
