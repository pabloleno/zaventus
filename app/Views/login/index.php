<?php
$favicon = trim((string) ($empresa['favicon'] ?? '')) ?: 'favicon.ico';
$logo_login = trim((string) ($empresa['logo_login'] ?? '')) ?: 'assets/img/zaventus-login-marca.png';
$nome_sistema = lang('App.appName');
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="<?= esc(str_replace('_', '-', $empresa['idioma'] ?? service('request')->getLocale() ?? 'pt-BR')) ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title><?= esc(lang('App.appName')) ?></title>

    <link rel="icon" href="<?= esc(base_url($favicon)) ?>" sizes="any">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/fontawesome-free/css/all.css') ?>">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.css') ?>">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css') ?>">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/select2/css/select2.css') ?>">
    <link rel="stylesheet" href="<?= base_url('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.css') ?>">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/icheck-bootstrap/icheck-bootstrap.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('theme/dist/css/adminlte.css') ?>">
    <!-- Style -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
    <script src="<?= base_url('assets/js/tema-cor.js?v=' . filemtime(FCPATH . 'assets/js/tema-cor.js')) ?>"></script>
    <style>
        .login-page .zaventus-login-logo{
            margin-bottom: 18px;
            text-align: center;
            width: 100%;
        }

        .login-page .zaventus-login-logo .zaventus-login-mark{
            display: block;
            height: auto;
            margin: 0 auto;
            max-width: 150px;
            width: 42%;
        }

        .login-page .zaventus-login-logo .zaventus-login-title{
            color: #1f2937;
            display: block;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
            margin-top: 10px;
            text-align: center;
        }
    </style>
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- ========= Scripts com prioridade ============= -->
    <!-- jQuery -->
    <script src="<?= base_url('theme/plugins/jquery/jquery.js') ?>"></script>
    <!-- SweetAlert2 -->
    <script src="<?= base_url('theme/plugins/sweetalert2/sweetalert2.js') ?>"></script>
    <!-- OPTIONAL SCRIPTS -->
    <script src="<?= base_url('theme/plugins/chart.js/Chart.min.js') ?>"></script>
</head>

<body class="hold-transition login-page">
    <?= view('templates/tema_cor_toggle', ['classe' => 'sistema-theme-toggle-login']) ?>
    <div class="login-box">
        <div class="login-logo zaventus-login-logo">
            <img class="zaventus-login-mark" src="<?= esc(base_url($logo_login)) ?>" alt="<?= esc($nome_sistema) ?>">
            <strong class="zaventus-login-title"><?= esc($nome_sistema) ?></strong>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg"><?= esc(lang('App.login.accessAccount')) ?></p>

                <form action="/login/autenticar" method="post">
                    <?= csrf_field() ?>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="usuario" placeholder="<?= esc(lang('App.login.user')) ?>" autofocus required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="senha" placeholder="<?= esc(lang('App.login.password')) ?>" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block"><?= esc(lang('App.login.authenticate')) ?></button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- Bootstrap 4 -->
    <script src="<?= base_url('theme/plugins/bootstrap/js/bootstrap.bundle.js') ?>"></script>
    <!-- Select2 -->
    <script src="<?= base_url('theme/plugins/select2/js/select2.full.js') ?>"></script>
    <!-- DataTables -->
    <script src="<?= base_url('theme/plugins/datatables/jquery.dataTables.js') ?>"></script>
    <script src="<?= base_url('theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js') ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url('theme/dist/js/adminlte.js') ?>"></script>
    <script>
        $(function() {
            // -------------- ALERTAS ---------------- //
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });

            <?php
            $session = session();
            $alert = $session->getFlashdata('alert');

            if (isset($alert)) :
            ?>
                <?php if ($alert == "error_autentication") : ?>
                    Toast.fire({
                        type: 'error',
                        title: <?= json_encode(lang('App.login.invalidCredentials')) ?>
                    })
                <?php elseif ($alert == "error_too_many_login_attempts") : ?>
                    Toast.fire({
                        type: 'warning',
                        title: <?= json_encode(lang('App.login.tooManyAttempts')) ?>
                    })
                <?php elseif ($alert == "session_expired") : ?>
                    Toast.fire({
                        type: 'warning',
                        title: <?= json_encode(lang('App.login.sessionExpired')) ?>
                    })
                <?php endif; ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>
