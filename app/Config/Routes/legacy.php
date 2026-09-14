<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/**
 * Mapa explicito das rotas legadas que permanecem ativas durante a migracao.
 *
 * Este arquivo deve ser carregado por app/Config/Routes.php antes de desligar
 * o auto-routing. GET fica restrito a consultas e formularios; toda operacao
 * que altera estado usa POST.
 *
 * @var RouteCollection $routes
 */

// Entrada e dashboard.
$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
$routes->get('home/index', 'Home::index');
$routes->get('inicio', 'Inicio::index');
$routes->get('inicio/index', 'Inicio::index');

// Autenticacao e administracao de usuarios.
$routes->get('login', 'Login::index');
$routes->get('login/index', 'Login::index');
$routes->post('login/autenticar', 'Login::autenticar');
$routes->post('login/logout', 'Login::logout');
$routes->get('login/usuarios', 'Login::usuarios');
$routes->get('login/create', 'Login::create');
$routes->get('login/edit/(:num)', 'Login::edit/$1');
$routes->post('login/store', 'Login::store');
$routes->post('login/delete/(:num)', 'Login::delete/$1');

// Clientes.
$routes->get('clientes', 'Clientes::index');
$routes->get('clientes/index', 'Clientes::index');
$routes->get('clientes/show/(:num)', 'Clientes::show/$1');
$routes->get('clientes/create', 'Clientes::create');
$routes->get('clientes/edit/(:num)', 'Clientes::edit/$1');
$routes->get('clientes/municipiosPorUf', 'Clientes::municipiosPorUf');
$routes->get('clientes/municipiosPorUf/(:alpha)', 'Clientes::municipiosPorUf/$1');
$routes->post('clientes/store', 'Clientes::store');
$routes->post('clientes/delete/(:num)', 'Clientes::delete/$1');

// Fornecedores.
$routes->get('fornecedores', 'Fornecedores::index');
$routes->get('fornecedores/index', 'Fornecedores::index');
$routes->get('fornecedores/show/(:num)', 'Fornecedores::show/$1');
$routes->get('fornecedores/create', 'Fornecedores::create');
$routes->get('fornecedores/edit/(:num)', 'Fornecedores::edit/$1');
$routes->get('fornecedores/municipiosPorUf', 'Fornecedores::municipiosPorUf');
$routes->get('fornecedores/municipiosPorUf/(:alpha)', 'Fornecedores::municipiosPorUf/$1');
$routes->post('fornecedores/store', 'Fornecedores::store');
$routes->post('fornecedores/delete/(:num)', 'Fornecedores::delete/$1');

// Funcionarios, vendedores e tecnicos.
$routes->get('funcionarios', 'Funcionarios::index');
$routes->get('funcionarios/index', 'Funcionarios::index');
$routes->get('funcionarios/show/(:num)', 'Funcionarios::show/$1');
$routes->get('funcionarios/create', 'Funcionarios::create');
$routes->get('funcionarios/edit/(:num)', 'Funcionarios::edit/$1');
$routes->get('funcionarios/municipiosPorUf', 'Funcionarios::municipiosPorUf');
$routes->get('funcionarios/municipiosPorUf/(:alpha)', 'Funcionarios::municipiosPorUf/$1');
$routes->post('funcionarios/store', 'Funcionarios::store');
$routes->post('funcionarios/delete/(:num)', 'Funcionarios::delete/$1');

$routes->get('vendedores', 'Vendedores::index');
$routes->get('vendedores/index', 'Vendedores::index');
$routes->get('vendedores/create', 'Vendedores::create');
$routes->get('vendedores/edit/(:num)', 'Vendedores::edit/$1');
$routes->post('vendedores/store', 'Vendedores::store');
$routes->post('vendedores/delete/(:num)', 'Vendedores::delete/$1');

$routes->get('tecnicos', 'Tecnicos::index');
$routes->get('tecnicos/index', 'Tecnicos::index');
$routes->get('tecnicos/show/(:num)', 'Tecnicos::show/$1');
$routes->get('tecnicos/create', 'Tecnicos::create');
$routes->get('tecnicos/edit/(:num)', 'Tecnicos::edit/$1');
$routes->get('tecnicos/municipiosPorUf', 'Tecnicos::municipiosPorUf');
$routes->get('tecnicos/municipiosPorUf/(:alpha)', 'Tecnicos::municipiosPorUf/$1');
$routes->post('tecnicos/store', 'Tecnicos::store');
$routes->post('tecnicos/delete/(:num)', 'Tecnicos::delete/$1');

// Catalogo legado mantido ate a migracao para servicos.
$routes->get('categoriasDosProdutos', 'CategoriasDosProdutos::index');
$routes->get('categoriasDosProdutos/index', 'CategoriasDosProdutos::index');
$routes->get('categoriasDosProdutos/create', 'CategoriasDosProdutos::create');
$routes->get('categoriasDosProdutos/edit/(:num)', 'CategoriasDosProdutos::edit/$1');
$routes->post('categoriasDosProdutos/store', 'CategoriasDosProdutos::store');
$routes->post('categoriasDosProdutos/delete/(:num)', 'CategoriasDosProdutos::delete/$1');

// Alias com a capitalizacao encontrada nas views legadas.
$routes->get('CategoriasDosProdutos', 'CategoriasDosProdutos::index');
$routes->get('CategoriasDosProdutos/index', 'CategoriasDosProdutos::index');
$routes->get('CategoriasDosProdutos/create', 'CategoriasDosProdutos::create');
$routes->get('CategoriasDosProdutos/edit/(:num)', 'CategoriasDosProdutos::edit/$1');
$routes->post('CategoriasDosProdutos/store', 'CategoriasDosProdutos::store');
$routes->post('CategoriasDosProdutos/delete/(:num)', 'CategoriasDosProdutos::delete/$1');

$routes->get('produtos', 'Produtos::index');
$routes->get('produtos/index', 'Produtos::index');
$routes->get('produtos/pesquisar', 'Produtos::pesquisar');
$routes->post('produtos/pesquisar', 'Produtos::pesquisar');
$routes->get('produtos/show/(:num)', 'Produtos::show/$1');
$routes->get('produtos/create', 'Produtos::create');
$routes->get('produtos/edit/(:num)', 'Produtos::edit/$1');
$routes->post('produtos/store', 'Produtos::store');
$routes->post('produtos/delete/(:num)', 'Produtos::delete/$1');
$routes->post('produtos/removerImagem/(:num)', 'Produtos::removerImagem/$1');

$routes->get('servicosMaoDeObra', 'ServicosMaoDeObra::index');
$routes->get('servicosMaoDeObra/index', 'ServicosMaoDeObra::index');
$routes->get('servicosMaoDeObra/create', 'ServicosMaoDeObra::create');
$routes->get('servicosMaoDeObra/edit/(:num)', 'ServicosMaoDeObra::edit/$1');
$routes->post('servicosMaoDeObra/store', 'ServicosMaoDeObra::store');
$routes->post('servicosMaoDeObra/delete/(:num)', 'ServicosMaoDeObra::delete/$1');

// Estoque de materia-prima.
$routes->get('reposicoes', 'Reposicoes::index');
$routes->get('reposicoes/index', 'Reposicoes::index');
$routes->get('reposicoes/create', 'Reposicoes::create');
$routes->post('reposicoes/store', 'Reposicoes::store');
$routes->post('reposicoes/delete/(:num)', 'Reposicoes::delete/$1');

$routes->get('saidaDeMercadorias', 'SaidaDeMercadorias::index');
$routes->get('saidaDeMercadorias/index', 'SaidaDeMercadorias::index');
$routes->get('saidaDeMercadorias/create', 'SaidaDeMercadorias::create');
$routes->post('saidaDeMercadorias/store', 'SaidaDeMercadorias::store');
$routes->post('saidaDeMercadorias/delete/(:num)', 'SaidaDeMercadorias::delete/$1');

$routes->get('inventarioDoEstoque', 'InventarioDoEstoque::index');
$routes->get('inventarioDoEstoque/index', 'InventarioDoEstoque::index');
$routes->post('inventarioDoEstoque/create_1', 'InventarioDoEstoque::create_1');
$routes->get('inventarioDoEstoque/show/(:num)', 'InventarioDoEstoque::show/$1');
$routes->get('inventarioDoEstoque/edit/(:num)', 'InventarioDoEstoque::edit/$1');
$routes->get('inventarioDoEstoque/add/(:num)', 'InventarioDoEstoque::add/$1');
$routes->get('inventarioDoEstoque/listaProdutos/(:num)', 'InventarioDoEstoque::listaProdutos/$1');
$routes->get(
    'inventarioDoEstoque/editProduto/(:num)/(:num)',
    'InventarioDoEstoque::editProduto/$1/$2'
);
$routes->post('inventarioDoEstoque/store', 'InventarioDoEstoque::store');
$routes->post('inventarioDoEstoque/store_produto', 'InventarioDoEstoque::store_produto');
$routes->post('inventarioDoEstoque/delete/(:num)', 'InventarioDoEstoque::delete/$1');
$routes->post(
    'inventarioDoEstoque/deleteProduto/(:num)/(:num)',
    'InventarioDoEstoque::deleteProduto/$1/$2'
);

// Financeiro operacional.
$routes->get('caixas', 'Caixas::index');
$routes->get('caixas/index', 'Caixas::index');
$routes->get('caixas/show/(:num)', 'Caixas::show/$1');
$routes->get('caixas/abrir', 'Caixas::abrir');
$routes->get('caixas/edit/(:num)', 'Caixas::edit/$1');
$routes->post('caixas/fechar/(:num)', 'Caixas::fechar/$1');
$routes->post('caixas/reabrir/(:num)', 'Caixas::reabrir/$1');
$routes->post('caixas/store', 'Caixas::store');
$routes->post('caixas/delete/(:num)', 'Caixas::delete/$1');

$routes->get('lancamentos', 'Lancamentos::index');
$routes->get('lancamentos/index', 'Lancamentos::index');
$routes->get('lancamentos/create', 'Lancamentos::create');
$routes->get('lancamentos/edit/(:num)', 'Lancamentos::edit/$1');
$routes->post('lancamentos/store', 'Lancamentos::store');
$routes->post('lancamentos/delete/(:num)', 'Lancamentos::delete/$1');

$routes->get('retiradas', 'Retiradas::index');
$routes->get('retiradas/index', 'Retiradas::index');
$routes->get('retiradas/create', 'Retiradas::create');
$routes->get('retiradas/edit/(:num)', 'Retiradas::edit/$1');
$routes->post('retiradas/store', 'Retiradas::store');
$routes->post('retiradas/delete/(:num)', 'Retiradas::delete/$1');

$routes->get('despesas', 'Despesas::index');
$routes->get('despesas/index', 'Despesas::index');
$routes->get('despesas/create', 'Despesas::create');
$routes->get('despesas/edit/(:num)', 'Despesas::edit/$1');
$routes->post('despesas/store', 'Despesas::store');
$routes->post('despesas/delete/(:num)', 'Despesas::delete/$1');

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

$routes->get('pagamentosDoCliente/create/(:num)', 'PagamentosDoCliente::create/$1');
$routes->get(
    'pagamentosDoCliente/edit/(:num)/(:num)',
    'PagamentosDoCliente::edit/$1/$2'
);
$routes->post('pagamentosDoCliente/store', 'PagamentosDoCliente::store');
$routes->post(
    'pagamentosDoCliente/delete/(:num)/(:num)',
    'PagamentosDoCliente::delete/$1/$2'
);

$routes->get('formasDePagamento', 'FormasDePagamento::index');
$routes->get('formasDePagamento/index', 'FormasDePagamento::index');

// Cobrancas e alertas.
$routes->get('cobrancas', 'Cobrancas::index');
$routes->get('cobrancas/index', 'Cobrancas::index');
$routes->get('cobrancas/create', 'Cobrancas::create');
$routes->get('cobrancas/edit/(:num)', 'Cobrancas::edit/$1');
$routes->get('cobrancas/alertas-navbar', 'Cobrancas::alertasNavbar');
$routes->get('cobrancas/alertasNavbar', 'Cobrancas::alertasNavbar');
$routes->post('cobrancas/store', 'Cobrancas::store');
$routes->post('cobrancas/concluir/(:num)', 'Cobrancas::concluir/$1');
$routes->post('cobrancas/estender/(:num)', 'Cobrancas::estender/$1');
$routes->post('cobrancas/delete/(:num)', 'Cobrancas::delete/$1');

// Configuracoes nao fiscais e integracoes de pagamento.
$routes->get('configs/empresa', 'Configs::empresa');
$routes->post('configs/store_empresa', 'Configs::store_empresa');
$routes->get('configs/sistema', 'Configs::sistema');
$routes->post('configs/store_sistema', 'Configs::store_sistema');
$routes->post('configs/store_personalizacao', 'Configs::store_personalizacao');
$routes->post('configs/alteraTema', 'Configs::alteraTema');
$routes->get('configs/municipiosPorUf', 'Configs::municipiosPorUf');
$routes->get('configs/municipiosPorUf/(:alpha)', 'Configs::municipiosPorUf/$1');
$routes->get('configs/createFormaDePagamento', 'Configs::createFormaDePagamento');
$routes->get('configs/editFormaDePagamento/(:num)', 'Configs::editFormaDePagamento/$1');
$routes->post('configs/store_forma_de_pagamento', 'Configs::store_forma_de_pagamento');
$routes->post('configs/delete_forma_de_pagamento/(:num)', 'Configs::delete_forma_de_pagamento/$1');
$routes->post('configs/backupDataBase', 'Configs::backupDataBase');

$routes->get('desenvolvedor', 'Desenvolvedor::index');
$routes->get('desenvolvedor/index', 'Desenvolvedor::index');
$routes->get('desenvolvedor/edit/(:num)', 'Desenvolvedor::edit/$1');
$routes->post('desenvolvedor/store', 'Desenvolvedor::store');
$routes->post('desenvolvedor/testar/(:num)', 'Desenvolvedor::testar/$1');

// Orcamento e ordem de servico compartilham o atendimento auditado.
$routes->get('ordensDeServicos', 'OrdensDeServicos::atendimentoListar');
$routes->get('ordensDeServicos/index', 'OrdensDeServicos::atendimentoListar');
$routes->get('ordensDeServicos/orcamentos', 'OrdensDeServicos::atendimentoOrcamentos');
$routes->get('ordensDeServicos/create', 'OrdensDeServicos::atendimentoNovo');
$routes->get('ordensDeServicos/show/(:num)', 'OrdensDeServicos::atendimentoMostrar/$1');
$routes->get('ordensDeServicos/edit/(:num)', 'OrdensDeServicos::atendimentoEditar/$1');
$routes->get('ordensDeServicos/excluidos', 'OrdensDeServicos::atendimentoExcluidos');
$routes->get('ordensDeServicos/imprimirAtendimento/(:num)', 'OrdensDeServicos::imprimirAtendimento/$1');
$routes->get('ordensDeServicos/baixarAnexoAtendimento/(:num)', 'OrdensDeServicos::baixarAnexoAtendimento/$1');
$routes->post('ordensDeServicos/salvarAtendimento', 'OrdensDeServicos::salvarAtendimento');
$routes->post('ordensDeServicos/calcularAtendimento', 'OrdensDeServicos::calcularAtendimento');
$routes->post('ordensDeServicos/statusAtendimento', 'OrdensDeServicos::statusAtendimento');
$routes->post('ordensDeServicos/reabrirAtendimento', 'OrdensDeServicos::reabrirAtendimento');
$routes->post('ordensDeServicos/excluirAtendimento', 'OrdensDeServicos::excluirAtendimento');
$routes->post('ordensDeServicos/restaurarAtendimento', 'OrdensDeServicos::restaurarAtendimento');
$routes->post('ordensDeServicos/apagarAtendimento', 'OrdensDeServicos::apagarAtendimento');
$routes->post('ordensDeServicos/receberAtendimento', 'OrdensDeServicos::receberAtendimento');
$routes->post('ordensDeServicos/estornarRecebimentoAtendimento', 'OrdensDeServicos::estornarRecebimentoAtendimento');
$routes->post('ordensDeServicos/anexarAtendimento', 'OrdensDeServicos::anexarAtendimento');
$routes->post('ordensDeServicos/consumirAtendimento', 'OrdensDeServicos::consumirAtendimento');
$routes->post('ordensDeServicos/estornarConsumoAtendimento', 'OrdensDeServicos::estornarConsumoAtendimento');
$routes->post('ordensDeServicos/salvarEquipamentoAtendimento', 'OrdensDeServicos::salvarEquipamentoAtendimento');
$routes->post('ordensDeServicos/removerEquipamentoAtendimento', 'OrdensDeServicos::removerEquipamentoAtendimento');
// Abas antigas recebem orientacao para recarregar sem gravacoes parciais.
$routes->post('ordensDeServicos/alteraSituacaoDaOrdemDeServicos', 'OrdensDeServicos::statusAtendimento');
$routes->post('ordensDeServicos/addEquipamento', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/deleteEquipamento/(:num)', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/addEquipamentoEdit', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/deleteEquipamentoEdit/(:num)/(:num)', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/addProduto', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/deleteProduto/(:num)', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/alteraDadosProdutoPeca', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/addProdutoEdit', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/deleteProdutoEdit/(:num)/(:num)', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/alteraDadosProdutoPecaEdit', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/addServicoMaoDeObra', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/deleteServicoMaoDeObra/(:num)', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/alteraDadosServicoMaoDeObra', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/addServicoMaoDeObraEdit', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/deleteServicoMaoDeObraEdit/(:num)/(:num)', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/alteraDadosServicoMaoDeObraEdit', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/alteraTotal', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/alteraTotalEdit', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/calculaPagamentoAVista', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/calculaParcelasOs', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/alteraDadosDaParcela', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/calculaPagamentoAVistaEdit', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/calculaParcelasOsEdit', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/finalizaOrdemDeServico', 'OrdensDeServicos::atendimentoFormularioAtual');
$routes->post('ordensDeServicos/editDadosResponsaveis_e_DadosFinaisOrdemDeServico', 'OrdensDeServicos::atendimentoFormularioAtual');

// Historico de vendas: deliberadamente somente leitura.
$routes->get('vendas', 'Vendas::index');
$routes->get('vendas/index', 'Vendas::index');
$routes->get('vendas/show/(:num)', 'Vendas::show/$1');

// Relatorios legados, todos somente leitura.
$routes->get('relatorioDRE', 'RelatorioDRE::index');
$routes->get('relatorioDRE/index', 'RelatorioDRE::index');
$routes->post('relatorioDRE', 'RelatorioDRE::index');
$routes->get('relatorios/clientes', 'Relatorios::clientes');
$routes->get('relatorios/fornecedores', 'Relatorios::fornecedores');
$routes->get('relatorios/funcionarios', 'Relatorios::funcionarios');
$routes->get('relatorios/historicoCompleto', 'Relatorios::historicoCompleto');
$routes->get('relatorios/porCliente', 'Relatorios::porCliente');
$routes->get('relatorios/porVendedor', 'Relatorios::porVendedor');
$routes->get('relatorios/produtos', 'Relatorios::produtos');
$routes->get('relatorios/estoqueMinimo', 'Relatorios::estoqueMinimo');
$routes->get('relatorios/validadeDosProdutos', 'Relatorios::validadeDosProdutos');
$routes->get('relatorios/faturamentoDiario', 'Relatorios::faturamentoDiario');
$routes->get('relatorios/faturamentoDetalhado', 'Relatorios::faturamentoDetalhado');
$routes->get('relatorios/lancamentos', 'Relatorios::lancamentos');
$routes->get('relatorios/retiradasDoCaixa', 'Relatorios::retiradasDoCaixa');
$routes->get('relatorios/despesas', 'Relatorios::despesas');
$routes->get('relatorios/contasPagar', 'Relatorios::contasPagar');
$routes->get('relatorios/contasReceber', 'Relatorios::contasReceber');
$routes->get('relatorios/vendedores', 'Relatorios::vendedores');

// Os formularios de filtro legados ainda enviam POST. Eles sao somente leitura.
$routes->post('relatorios/clientes', 'Relatorios::clientes');
$routes->post('relatorios/fornecedores', 'Relatorios::fornecedores');
$routes->post('relatorios/funcionarios', 'Relatorios::funcionarios');
$routes->post('relatorios/historicoCompleto', 'Relatorios::historicoCompleto');
$routes->post('relatorios/porCliente', 'Relatorios::porCliente');
$routes->post('relatorios/porVendedor', 'Relatorios::porVendedor');
$routes->post('relatorios/produtos', 'Relatorios::produtos');
$routes->post('relatorios/estoqueMinimo', 'Relatorios::estoqueMinimo');
$routes->post('relatorios/validadeDosProdutos', 'Relatorios::validadeDosProdutos');
$routes->post('relatorios/faturamentoDiario', 'Relatorios::faturamentoDiario');
$routes->post('relatorios/faturamentoDetalhado', 'Relatorios::faturamentoDetalhado');
$routes->post('relatorios/lancamentos', 'Relatorios::lancamentos');
$routes->post('relatorios/retiradasDoCaixa', 'Relatorios::retiradasDoCaixa');
$routes->post('relatorios/despesas', 'Relatorios::despesas');
$routes->post('relatorios/contasPagar', 'Relatorios::contasPagar');
$routes->post('relatorios/contasReceber', 'Relatorios::contasReceber');
$routes->post('relatorios/vendedores', 'Relatorios::vendedores');

/*
 * Rotas deliberadamente bloqueadas (nao registrar neste mapa).
 * Com o auto-routing desligado, todas devem responder 404.
 *
 * Controllers/fluxos aposentados integralmente:
 * - Orcamentos::* (orcamento legado baseado em produtos).
 * - Pedidos::*.
 * - VendaRapida::*.
 * - Pdv::*.
 * - NFe::*.
 * - ControleFiscal::*.
 * - ImprimeDanfe::*.
 * - ConfigNFCe::* (arquivo legado vazio).
 *
 * Configuracoes fiscais aposentadas:
 * - GET  configs/nfe e configs/nfce.
 * - POST configs/store_nfe e configs/store_nfce.
 *
 * Importacao/reposicao via XML fiscal aposentada em Produtos:
 * - produtos/add_por_xml.
 * - produtos/remove_fornecedor_cadastrado_por_xml/*.
 * - produtos/provisorio_add_produtos_por_xml.
 * - produtos/altera_dados_do_produto_provisorio_cad_por_xml.
 * - produtos/finalizar_e_cadastrar_produtos_por_xml.
 * - produtos/reposicao_por_xml.
 * - produtos/provisorio_reposicao_produtos_por_xml.
 * - produtos/altera_dados_do_produto_provisorio_reposicao_por_xml.
 * - produtos/finalizar_e_repoe_produtos_por_xml.
 *
 * Mutacao de venda historica bloqueada:
 * - vendas/delete/*.
 * - ordensDeServicos/delete/* (usar situacao Cancelada).
 *
 * Metodos publicos tecnicos/internos que nunca sao endpoints:
 * - __construct, initController, format, tipoVenda e tipoPedido.
 */
