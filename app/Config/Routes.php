<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load framework routes first so the application may override them.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// Security baseline: only routes declared by the application are reachable.
$routes->setAutoRoute(false);

require APPPATH . 'Config/Routes/legacy.php';

// Environment-only routes are loaded last and must also declare their verb.
$environmentRoutes = APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';

if (is_file($environmentRoutes)) {
    require $environmentRoutes;
}
