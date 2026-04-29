<?php
    $controle_de_acesso = $session->get('controle_de_acesso');
    $array_c_a = json_decode($controle_de_acesso);
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url('assets/img/user.png') ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= $session->get('primeiro_nome') ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-compact" data-widget="treeview" role="menu" data-accordion="false">
                <li id="1.m" class="nav-item">
                    <a id="1.0" href="/inicio" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Inicio</p>
                    </a>
                </li>

                <?php if($array_c_a->vendas->modulo == 1): ?>
                    <li id="2.m" class="nav-item has-treeview">
                        <a id="2.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-money-bill-alt"></i>
                            <p>Vendas e OS<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->vendas->venda_rapida == 1): ?>
                                <li class="nav-item">
                                    <a id="2.2" href="/vendaRapida" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Venda Rapida</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->vendas->pdv == 1): ?>
                                <li class="nav-item">
                                    <a id="2.1" href="/pdv" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>PDV</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->vendas->pesq_produto == 1): ?>
                                <li class="nav-item">
                                    <a id="2.3" href="/produtos/pesquisar" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pesq. Produto</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->vendas->hist_de_vendas == 1): ?>
                                <li class="nav-item">
                                    <a id="2.4" href="/vendas" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Hist. de Vendas</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a id="2.5" href="/ordensDeServicos/create" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Gerar Ordem de Serv.</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="2.6" href="/ordensDeServicos" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ordens de Servicos</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($array_c_a->controle_geral->modulo == 1): ?>
                    <li id="3.m" class="nav-item has-treeview">
                        <a id="3.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Controle Geral<i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->controle_geral->clientes == 1): ?>
                                <li class="nav-item">
                                    <a id="3.1" href="/clientes" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Clientes</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->controle_geral->fornecedores == 1): ?>
                                <li class="nav-item">
                                    <a id="3.2" href="/fornecedores" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Fornecedores</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->controle_geral->funcionarios == 1): ?>
                                <li class="nav-item">
                                    <a id="3.3" href="/funcionarios" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Funcionarios</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->controle_geral->vendedores == 1): ?>
                                <li class="nav-item">
                                    <a id="3.4" href="/vendedores" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Vendedores</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a id="3.5" href="/tecnicos" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tecnicos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a id="3.6" href="/servicosMaoDeObra" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Servico/Mao de Obra</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($array_c_a->estoque->modulo == 1): ?>
                    <li id="4.m" class="nav-item has-treeview">
                        <a id="4.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-coins"></i>
                            <p>Estoque<i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->estoque->produtos == 1): ?>
                                <li class="nav-item">
                                    <a id="4.1" href="/produtos" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Produtos</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->estoque->reposicoes == 1): ?>
                                <li class="nav-item">
                                    <a id="4.3" href="/reposicoes" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reposicoes</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->estoque->saida_de_mercadorias == 1): ?>
                                <li class="nav-item">
                                    <a id="4.4" href="/saidaDeMercadorias" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Saida de mercadorias</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($array_c_a->estoque->categorias_do_produto == 1): ?>
                                <li class="nav-item">
                                    <a id="4.2" href="/CategoriasDosProdutos" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Categorias do Produto</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($array_c_a->financeiro->modulo == 1): ?>
                    <li id="5.m" class="nav-item has-treeview">
                        <a id="5.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>Financeiro<i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->financeiro->caixas == 1): ?>
                                <li class="nav-item"><a id="5.1" href="/caixas" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Caixas</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->lancamentos == 1): ?>
                                <li class="nav-item"><a id="5.2" href="/lancamentos" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Lancamentos</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->retiradas_do_caixa == 1): ?>
                                <li class="nav-item"><a id="5.4" href="/retiradas" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Retiradas do Caixa</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->despesas == 1): ?>
                                <li class="nav-item"><a id="5.5" href="/despesas" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Despesas</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->contas_a_pagar == 1): ?>
                                <li class="nav-item"><a id="5.6" href="/contasPagar" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Contas a pagar</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->contas_a_receber == 1): ?>
                                <li class="nav-item"><a id="5.7" href="/contasReceber" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Contas a receber</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->orcamentos == 1): ?>
                                <li class="nav-item"><a id="5.8" href="/orcamentos" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Orcamentos</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->pedidos == 1): ?>
                                <li class="nav-item"><a id="5.9" href="/pedidos" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Pedidos</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->relatorio_dre == 1): ?>
                                <li class="nav-item"><a id="5.10" href="/relatorioDRE" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Relatorio DRE</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->inventario_do_estoque == 1): ?>
                                <li class="nav-item"><a id="5.11" href="/inventarioDoEstoque" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Inventario do Estoque</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->financeiro->controle_fiscal == 1): ?>
                                <li class="nav-item"><a id="5.12" href="/controleFiscal/nfe" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Controle Fiscal - NFe</p></a></li>
                                <li class="nav-item"><a id="5.13" href="/controleFiscal/nfce" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Controle Fiscal - NFCe</p></a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($array_c_a->relatorios->modulo == 1): ?>
                    <li id="7.m" class="nav-item has-treeview">
                        <a id="7.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <p>Relatorios<i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->relatorios->vendas == 1): ?>
                                <li class="nav-item"><a id="7.1" href="/relatorios/historicoCompleto" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Vendas - Completo</p></a></li>
                                <li class="nav-item"><a id="7.2" href="/relatorios/porCliente" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Vendas - Por Cliente</p></a></li>
                                <li class="nav-item"><a id="7.3" href="/relatorios/porVendedor" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Vendas - Por Vendedor</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->relatorios->estoque == 1): ?>
                                <li class="nav-item"><a id="7.4" href="/relatorios/produtos" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Produtos</p></a></li>
                                <li class="nav-item"><a id="7.5" href="/relatorios/estoqueMinimo" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Produtos - Est. Minimo</p></a></li>
                                <li class="nav-item"><a id="7.6" href="/inventarioDoEstoque" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Produtos - Inventario</p></a></li>
                                <li class="nav-item"><a id="7.7" href="/relatorios/validadeDosProdutos" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Produtos - Hist. de Validade</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->relatorios->financeiro == 1): ?>
                                <li class="nav-item"><a id="7.8.1" href="/relatorios/faturamentoDiario" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Financeiro - Fat. Diario</p></a></li>
                                <li class="nav-item"><a id="7.8" href="/relatorios/faturamentoDetalhado" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Financeiro - Fat. Detalhado</p></a></li>
                                <li class="nav-item"><a id="7.9" href="/relatorios/lancamentos" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Financeiro - Lancamentos</p></a></li>
                                <li class="nav-item"><a id="8.0" href="/relatorios/retiradasDoCaixa" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Financeiro - Retiradas</p></a></li>
                                <li class="nav-item"><a id="8.1" href="/relatorios/despesas" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Financeiro - Despesas</p></a></li>
                                <li class="nav-item"><a id="8.2" href="/relatorios/contasPagar" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Admin. - Contas a Pagar</p></a></li>
                                <li class="nav-item"><a id="8.3" href="/relatorios/contasReceber" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Admin. - Contas a Receber</p></a></li>
                                <li class="nav-item"><a id="8.4" href="/relatorioDRE" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Admin. - DRE</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->relatorios->geral == 1): ?>
                                <li class="nav-item"><a id="8.5" href="/relatorios/clientes" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Geral - Clientes</p></a></li>
                                <li class="nav-item"><a id="8.6" href="/relatorios/fornecedores" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Geral - Fornecedores</p></a></li>
                                <li class="nav-item"><a id="8.7" href="/relatorios/funcionarios" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Geral - Funcionarios</p></a></li>
                                <li class="nav-item"><a id="8.8" href="/relatorios/vendedores" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Geral - Vendedores</p></a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if($array_c_a->configs->modulo == 1): ?>
                    <li id="11.m" class="nav-item has-treeview">
                        <a id="11.0" href="#" class="nav-link">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>Configs<i class="fas fa-angle-left right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if($array_c_a->configs->nfe == 1): ?>
                                <li class="nav-item"><a id="11.1" href="/configs/nfe" class="nav-link"><i class="far fa-circle nav-icon"></i><p>NFe</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->configs->nfce == 1): ?>
                                <li class="nav-item"><a id="11.2" href="/configs/nfce" class="nav-link"><i class="far fa-circle nav-icon"></i><p>NFCe</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->configs->empresa == 1): ?>
                                <li class="nav-item"><a id="11.3" href="/configs/empresa" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Empresa</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->configs->sistema == 1): ?>
                                <li class="nav-item"><a id="11.4" href="/configs/sistema" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Sistema</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->configs->usuarios == 1): ?>
                                <li class="nav-item"><a id="11.5" href="/login/usuarios" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Usuarios</p></a></li>
                            <?php endif; ?>
                            <?php if($array_c_a->configs->backup_de_dados == 1): ?>
                                <li class="nav-item"><a id="11.6" href="/configs/backupDataBase" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Backup Dados</p></a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="/login/logout" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Sair</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
