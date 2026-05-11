<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <!-- <li class="nav-item d-none d-sm-inline-block">
            <a href="index3.html" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li> -->
    </ul>

    <h5 class="zaventus-nav-title">
        <span><?= esc(lang('App.appName')) ?></span>
        <?php if ($session->get('nome_fantasia')) : ?>
            <small><?= esc($session->get('nome_fantasia')) ?></small>
        <?php endif; ?>
    </h5>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="/login/logout"><i class="fas fa-sign-out-alt"></i></a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
