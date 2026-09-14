<?php
    use App\Libraries\ImagemCadastro;

    $controle_de_acesso = $session->get('controle_de_acesso');
    $array_c_a = json_decode($controle_de_acesso);
    $foto_usuario = ImagemCadastro::url($session->get('foto') ?? '');
    $pode_cobrancas = (int) ($array_c_a->controle_geral->cobrancas ?? $array_c_a->controle_geral->clientes ?? 0) === 1;

    $menu_visivel = static function ($modulo, array $permissoes): bool {
        if (!isset($modulo->modulo) || (int) $modulo->modulo !== 1) {
            return false;
        }

        foreach ($permissoes as $permissao) {
            if (isset($modulo->{$permissao}) && (int) $modulo->{$permissao} === 1) {
                return true;
            }
        }

        return false;
    };

    $exibe_orcamentos = isset($array_c_a->financeiro->modulo, $array_c_a->financeiro->orcamentos)
        && (int) $array_c_a->financeiro->modulo === 1
        && (int) $array_c_a->financeiro->orcamentos === 1;
    $exibe_menu_vendas = $menu_visivel($array_c_a->vendas ?? null, ['hist_de_vendas']) || $exibe_orcamentos;
    $exibe_menu_controle_geral = $menu_visivel($array_c_a->controle_geral ?? null, ['clientes', 'fornecedores', 'funcionarios', 'vendedores']);
    $exibe_menu_estoque = $menu_visivel($array_c_a->estoque ?? null, ['produtos', 'reposicoes', 'saida_de_mercadorias', 'categorias_do_produto']);
    $exibe_menu_financeiro = $menu_visivel($array_c_a->financeiro ?? null, ['caixas', 'lancamentos', 'retiradas_do_caixa', 'despesas', 'contas_a_pagar', 'contas_a_receber', 'relatorio_dre', 'inventario_do_estoque']) || $pode_cobrancas;
    $exibe_menu_relatorios = $menu_visivel($array_c_a->relatorios ?? null, ['vendas', 'estoque', 'financeiro', 'geral']);
    $exibe_menu_configs = $menu_visivel($array_c_a->configs ?? null, ['empresa', 'sistema', 'desenvolvedor', 'usuarios', 'backup_de_dados']);
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/inicio" class="brand-link zaventus-brand-link">
        <span class="brand-text font-weight-light"><?= esc(lang('App.appName')) ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= esc($foto_usuario) ?>" class="img-circle elevation-2" alt="Foto do usuario">
            </div>
            <div class="info">
                <a href="/login/edit/<?= (int) $session->get('id_login') ?>" class="d-block"><?= esc($session->get('primeiro_nome')) ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact" data-widget="treeview" role="menu" data-accordion="false">
                <li id="1.m" class="nav-item">
                    <a id="1.0" href="/inicio" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p><?= esc(lang('App.menu.home')) ?></p>
                    </a>
                </li>

                <?php if($exibe_menu_vendas): ?>
                    <li id="2.m" class="nav-item has-treeview">
                        <a id="2.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-money-bill-alt"></i>
                            <p><?= esc(lang('App.menu.salesOs')) ?><i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if((int) ($array_c_a->vendas->hist_de_vendas ?? 0) === 1): ?>
                                <li class="nav-item">
                                    <a id="2.4" href="/vendas" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.salesHistory')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a id="2.6" href="/ordensDeServicos" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= esc(lang('App.menu.serviceOrders')) ?></p>
                                </a>
                            </li>
                            <?php if($exibe_orcamentos): ?>
                                <li class="nav-item">
                                    <a id="2.7" href="/ordensDeServicos/orcamentos" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.quotes')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($exibe_menu_controle_geral): ?>
                    <li id="3.m" class="nav-item has-treeview">
                        <a id="3.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p><?= esc(lang('App.menu.generalControl')) ?><i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->controle_geral->clientes == 1): ?>
                                <li class="nav-item">
                                    <a id="3.1" href="/clientes" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.clients')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->controle_geral->fornecedores == 1): ?>
                                <li class="nav-item">
                                    <a id="3.2" href="/fornecedores" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.suppliers')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->controle_geral->funcionarios == 1): ?>
                                <li class="nav-item">
                                    <a id="3.3" href="/funcionarios" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.employees')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->controle_geral->vendedores == 1): ?>
                                <li class="nav-item">
                                    <a id="3.4" href="/vendedores" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.sellers')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a id="3.5" href="/tecnicos" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= esc(lang('App.menu.technicians')) ?></p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="3.6" href="/servicosMaoDeObra" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p><?= esc(lang('App.menu.serviceLabor')) ?></p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($exibe_menu_estoque): ?>
                    <li id="4.m" class="nav-item has-treeview">
                        <a id="4.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-coins"></i>
                            <p><?= esc(lang('App.menu.stock')) ?><i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->estoque->produtos == 1): ?>
                                <li class="nav-item">
                                    <a id="4.1" href="/produtos" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.products')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->estoque->reposicoes == 1): ?>
                                <li class="nav-item">
                                    <a id="4.3" href="/reposicoes" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.restocks')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->estoque->saida_de_mercadorias == 1): ?>
                                <li class="nav-item">
                                    <a id="4.4" href="/saidaDeMercadorias" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.goodsOut')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->estoque->categorias_do_produto == 1): ?>
                                <li class="nav-item">
                                    <a id="4.2" href="/CategoriasDosProdutos" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?= esc(lang('App.menu.productCategories')) ?></p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($exibe_menu_financeiro): ?>
                    <li id="5.m" class="nav-item has-treeview">
                        <a id="5.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p><?= esc(lang('App.menu.finance')) ?><i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->financeiro->caixas == 1): ?>
                                <li class="nav-item"><a id="5.1" href="/caixas" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.cashRegisters')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->lancamentos == 1): ?>
                                <li class="nav-item"><a id="5.2" href="/lancamentos" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.entries')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->retiradas_do_caixa == 1): ?>
                                <li class="nav-item"><a id="5.4" href="/retiradas" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.cashWithdrawals')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->despesas == 1): ?>
                                <li class="nav-item"><a id="5.5" href="/despesas" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.expenses')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->contas_a_pagar == 1): ?>
                                <li class="nav-item"><a id="5.6" href="/contasPagar" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.payable')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->contas_a_receber == 1): ?>
                                <li class="nav-item"><a id="5.7" href="/contasReceber" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.receivable')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($pode_cobrancas): ?>
                                <li class="nav-item"><a id="5.8" href="/cobrancas" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.collections')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->relatorio_dre == 1): ?>
                                <li class="nav-item"><a id="5.10" href="/relatorioDRE" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.dreReport')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->inventario_do_estoque == 1): ?>
                                <li class="nav-item"><a id="5.11" href="/inventarioDoEstoque" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.stockInventory')) ?></p></a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($exibe_menu_relatorios): ?>
                    <li id="7.m" class="nav-item has-treeview">
                        <a id="7.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <p><?= esc(lang('App.menu.reports')) ?><i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->relatorios->vendas == 1): ?>
                                <li class="nav-item"><a id="7.1" href="/relatorios/historicoCompleto" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.salesComplete')) ?></p></a></li>
                                <li class="nav-item"><a id="7.2" href="/relatorios/porCliente" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.salesByClient')) ?></p></a></li>
                                <li class="nav-item"><a id="7.3" href="/relatorios/porVendedor" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.salesBySeller')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->relatorios->estoque == 1): ?>
                                <li class="nav-item"><a id="7.4" href="/relatorios/produtos" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.products')) ?></p></a></li>
                                <li class="nav-item"><a id="7.5" href="/relatorios/estoqueMinimo" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.stockMinimum')) ?></p></a></li>
                                <li class="nav-item"><a id="7.7" href="/relatorios/validadeDosProdutos" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.productExpiration')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->relatorios->financeiro == 1): ?>
                                <li class="nav-item"><a id="7.8.1" href="/relatorios/faturamentoDiario" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.dailyBilling')) ?></p></a></li>
                                <li class="nav-item"><a id="7.8" href="/relatorios/faturamentoDetalhado" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.detailedBilling')) ?></p></a></li>
                                <li class="nav-item"><a id="7.9" href="/relatorios/lancamentos" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.financeEntries')) ?></p></a></li>
                                <li class="nav-item"><a id="8.0" href="/relatorios/retiradasDoCaixa" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.financeWithdrawals')) ?></p></a></li>
                                <li class="nav-item"><a id="8.1" href="/relatorios/despesas" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.financeExpenses')) ?></p></a></li>
                                <li class="nav-item"><a id="8.2" href="/relatorios/contasPagar" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.adminPayable')) ?></p></a></li>
                                <li class="nav-item"><a id="8.3" href="/relatorios/contasReceber" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.adminReceivable')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->relatorios->geral == 1): ?>
                                <li class="nav-item"><a id="8.5" href="/relatorios/clientes" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.generalClients')) ?></p></a></li>
                                <li class="nav-item"><a id="8.6" href="/relatorios/fornecedores" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.generalSuppliers')) ?></p></a></li>
                                <li class="nav-item"><a id="8.7" href="/relatorios/funcionarios" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.generalEmployees')) ?></p></a></li>
                                <li class="nav-item"><a id="8.8" href="/relatorios/vendedores" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.generalSellers')) ?></p></a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($exibe_menu_configs): ?>
                    <li id="11.m" class="nav-item has-treeview">
                        <a id="11.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p><?= esc(lang('App.menu.settings')) ?><i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->configs->empresa == 1): ?>
                                <li class="nav-item"><a id="11.3" href="/configs/empresa" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.company')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->configs->sistema == 1): ?>
                                <li class="nav-item"><a id="11.4" href="/configs/sistema" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.system')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if((int) ($array_c_a->configs->desenvolvedor ?? $array_c_a->configs->sistema ?? 0) === 1): ?>
                                <li class="nav-item"><a id="11.7" href="/desenvolvedor" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.developer')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->configs->usuarios == 1): ?>
                                <li class="nav-item"><a id="11.5" href="/login/usuarios" class="nav-link"><i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.users')) ?></p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->configs->backup_de_dados == 1): ?>
                                <li class="nav-item">
                                    <form action="/configs/backupDataBase" method="post" class="m-0">
                                        <?= csrf_field() ?>
                                        <button id="11.6" type="submit" class="nav-link btn btn-link text-left w-100">
                                            <i class="far fa-circle nav-icon"></i><p><?= esc(lang('App.menu.dataBackup')) ?></p>
                                        </button>
                                    </form>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <form action="/login/logout" method="post" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="nav-link btn btn-link text-left w-100">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p><?= esc(lang('App.menu.logout')) ?></p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
