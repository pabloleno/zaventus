<!-- Main Footer -->
<footer id="footer" class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
        <?= esc(lang('App.footer.developedBy')) ?>
    </div>
    <!-- Default to the left -->
    <?php $session = session() ?>
    <strong><?= $session->get('nome_fantasia') ?> &copy; <?= date('Y') ?> </strong> - <?= esc(lang('App.footer.rights')) ?>
</footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- Bootstrap 4 -->
<script src="<?= base_url('theme/plugins/bootstrap/js/bootstrap.bundle.js') ?>"></script>
<!-- Select2 -->
<script src="<?= base_url('theme/plugins/select2/js/select2.full.js') ?>"></script>
<?php
    $select2_locales = [
        'pt-BR' => 'pt-BR',
        'en'    => 'en',
        'es'    => 'es',
        'fr'    => 'fr',
        'de'    => 'de',
        'it'    => 'it',
    ];
    $select2_locale = $select2_locales[service('request')->getLocale()] ?? 'pt-BR';
?>
<script src="<?= base_url('theme/plugins/select2/js/i18n/' . $select2_locale . '.js') ?>"></script>
<!-- DataTables -->
<script src="<?= base_url('theme/plugins/datatables/jquery.dataTables.js') ?>"></script>
<script src="<?= base_url('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js') ?>"></script>
<!-- Bootstrap Switch -->
<script src="<?= base_url('theme/plugins/bootstrap-switch/js/bootstrap-switch.min.js') ?>"></script>
<!-- InputMask -->
<script src="<?= base_url('theme/plugins/inputmask/min/jquery.inputmask.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('theme/dist/js/adminlte.js') ?>"></script>
<script src="<?= base_url('assets/js/endereco-padrao.js?v=' . filemtime(FCPATH . 'assets/js/endereco-padrao.js')) ?>"></script>
<script src="<?= base_url('assets/js/campos-padrao.js?v=' . filemtime(FCPATH . 'assets/js/campos-padrao.js')) ?>"></script>
<script>
    $(function() {
        var dataTablesLanguage = <?= json_encode(lang('Ui.datatables'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var select2Language = <?= json_encode($select2_locale, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        var dataTableOptions = function() {
            return {
                language: dataTablesLanguage
            };
        };

        // DataTables
        $("#example1").DataTable(dataTableOptions());
        $("#example1-2").DataTable(dataTableOptions());
        $("#example1-3").DataTable(dataTableOptions());
        $("#example1-4").DataTable(dataTableOptions());
        $('#example2').DataTable({
            "language": dataTablesLanguage,
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
        });

        //Initialize Select2 Elements
        $('.select2').select2({
            language: select2Language
        })

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            language: select2Language
        })

        var errosCamposPadrao = <?= json_encode(array_values((array) session()->getFlashdata('errors')), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        if (errosCamposPadrao.length) {
            var listaErros = $('<ul class="text-left mb-0"></ul>');

            errosCamposPadrao.forEach(function(mensagem) {
                $('<li></li>').text(mensagem).appendTo(listaErros);
            });

            Swal.fire({
                type: 'error',
                title: 'Confira os campos',
                html: listaErros.prop('outerHTML')
            });
        }

    });

    function confirmaAcaoExcluir(msg, rota) {
        if (confirm(msg)) {
            window.location.href = rota;
        }
    }

    function trocaVirguraPorPonto(id) {
        var valor = document.getElementById(id).value;
        document.getElementById(id).value = valor.replace(',', '.')
    }

    function adicionaClasseMenu(id, classe) {
        var element = document.getElementById(id);
        if (element) {
            element.className += " " + classe;
        }
    }

    adicionaClasseMenu('<?= $links['menu'] ?>', 'menu-open');
    adicionaClasseMenu('<?= $links['item'] ?>', 'active');
    <?php if (isset($links['subItem'])) : ?>
        adicionaClasseMenu('<?= $links['subItem'] ?>', 'active');
    <?php endif; ?>
</script>
</body>

</html>
