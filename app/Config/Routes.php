<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('clientes/municipiosPorUf/(:alpha)', 'Clientes::municipiosPorUf/$1');
$routes->get('configs/municipiosPorUf/(:alpha)', 'Configs::municipiosPorUf/$1');
$routes->get('fornecedores/municipiosPorUf/(:alpha)', 'Fornecedores::municipiosPorUf/$1');
$routes->get('funcionarios/municipiosPorUf/(:alpha)', 'Funcionarios::municipiosPorUf/$1');
$routes->get('tecnicos/municipiosPorUf/(:alpha)', 'Tecnicos::municipiosPorUf/$1');
$routes->get('cobrancas/alertas-navbar', 'Cobrancas::alertasNavbar');

// Rotas explicitas do ciclo 1: autenticacao, uploads, financeiro e fiscal.
$routes->get('login', 'Login::index');
$routes->post('login/autenticar', 'Login::autenticar');
$routes->get('login/logout', 'Login::logout');

$routes->get('produtos', 'Produtos::index');
$routes->get('produtos/index', 'Produtos::index');
$routes->get('produtos/create', 'Produtos::create');
$routes->get('produtos/edit/(:num)', 'Produtos::edit/$1');
$routes->get('produtos/show/(:num)', 'Produtos::show/$1');
$routes->post('produtos/store', 'Produtos::store');
$routes->post('produtos/delete/(:num)', 'Produtos::delete/$1');
$routes->post('produtos/removerImagem/(:num)', 'Produtos::removerImagem/$1');
$routes->post('produtos/add_por_xml', 'Produtos::add_por_xml');
$routes->post('produtos/reposicao_por_xml', 'Produtos::reposicao_por_xml');
$routes->post('produtos/finalizar_e_cadastrar_produtos_por_xml', 'Produtos::finalizar_e_cadastrar_produtos_por_xml');
$routes->post('produtos/finalizar_e_repoe_produtos_por_xml', 'Produtos::finalizar_e_repoe_produtos_por_xml');

$routes->get('configs/nfe', 'Configs::nfe');
$routes->post('configs/store_nfe', 'Configs::store_nfe');
$routes->get('configs/nfce', 'Configs::nfce');
$routes->post('configs/store_nfce', 'Configs::store_nfce');
$routes->get('configs/empresa', 'Configs::empresa');
$routes->post('configs/store_empresa', 'Configs::store_empresa');
$routes->get('configs/sistema', 'Configs::sistema');
$routes->post('configs/store_sistema', 'Configs::store_sistema');
$routes->post('configs/store_personalizacao', 'Configs::store_personalizacao');
$routes->post('configs/store_forma_de_pagamento', 'Configs::store_forma_de_pagamento');
$routes->post('configs/delete_forma_de_pagamento/(:num)', 'Configs::delete_forma_de_pagamento/$1');

$routes->get('contasPagar', 'ContasPagar::index');
$routes->get('contasPagar/index', 'ContasPagar::index');
$routes->get('contasPagar/create', 'ContasPagar::create');
$routes->get('contasPagar/edit/(:num)', 'ContasPagar::edit/$1');
$routes->post('contasPagar/store', 'ContasPagar::store');
$routes->post('contasPagar/delete/(:num)', 'ContasPagar::delete/$1');

$routes->get('contasReceber', 'ContasReceber::index');
$routes->get('contasReceber/index', 'ContasReceber::index');
$routes->get('contasReceber/create', 'ContasReceber::create');
$routes->get('contasReceber/edit/(:num)', 'ContasReceber::edit/$1');
$routes->post('contasReceber/store', 'ContasReceber::store');
$routes->post('contasReceber/delete/(:num)', 'ContasReceber::delete/$1');

$routes->get('cobrancas', 'Cobrancas::index');
$routes->get('cobrancas/index', 'Cobrancas::index');
$routes->get('cobrancas/create', 'Cobrancas::create');
$routes->get('cobrancas/edit/(:num)', 'Cobrancas::edit/$1');
$routes->post('cobrancas/store', 'Cobrancas::store');
$routes->post('cobrancas/concluir/(:num)', 'Cobrancas::concluir/$1');
$routes->post('cobrancas/estender/(:num)', 'Cobrancas::estender/$1');
$routes->post('cobrancas/delete/(:num)', 'Cobrancas::delete/$1');

$routes->get('controleFiscal', 'ControleFiscal::index');
$routes->get('controleFiscal/index', 'ControleFiscal::index');
$routes->get('controleFiscal/nfe', 'ControleFiscal::nfe');
$routes->get('controleFiscal/nfce', 'ControleFiscal::nfce');
$routes->get('controleFiscal/statusServico/(:segment)', 'ControleFiscal::statusServico/$1');
$routes->post('controleFiscal/consultar/(:segment)/(:num)', 'ControleFiscal::consultar/$1/$2');
$routes->post('controleFiscal/cancelar', 'ControleFiscal::cancelar');
$routes->get('controleFiscal/showErro/(:num)', 'ControleFiscal::showErro/$1');
$routes->get('controleFiscal/showErroNFe/(:num)', 'ControleFiscal::showErroNFe/$1');
$routes->get('controleFiscal/showErroNFCe/(:num)', 'ControleFiscal::showErroNFCe/$1');
$routes->get('controleFiscal/baixaXML/(:num)', 'ControleFiscal::baixaXML/$1');
$routes->get('controleFiscal/baixaXML/(:segment)/(:num)', 'ControleFiscal::baixaXML/$1/$2');
$routes->get('controleFiscal/baixaXML/(:segment)/(:num)/(:segment)', 'ControleFiscal::baixaXML/$1/$2/$3');
$routes->get('controleFiscal/baixaXMLS/(:segment)/(:segment)', 'ControleFiscal::baixaXMLS/$1/$2');

$routes->post('NFe/emiteNFe/(:num)/(:num)', 'NFe::emiteNFe/$1/$2');
$routes->post('NFe/reemitir/(:num)/(:num)/(:num)', 'NFe::reemitir/$1/$2/$3');
$routes->post('NFe/cancelar', 'NFe::cancelar');
$routes->post('NFe/cancelarLegado', 'NFe::cancelarLegado');
$routes->post('Pdv/emiteNFCe/(:num)/(:num)', 'Pdv::emiteNFCe/$1/$2');
$routes->post('pdv/finalizaVendaEmiteNFCe/(:num)', 'Pdv::finalizaVendaEmiteNFCe/$1');

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
