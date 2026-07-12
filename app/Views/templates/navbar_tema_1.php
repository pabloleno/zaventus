<!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="/inicio" class="navbar-brand">
        <!-- <img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8"> -->
        <span class="brand-text font-weight-light"><?= esc(lang('App.appName')) ?></span>
      </a>
      
      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li id="1.m" class="nav-item">
            <a id="1.0" href="/inicio" class="nav-link"><?= esc(lang('App.menu.home')) ?></a>
          </li>

          <?php
            $session = session();
            $usuario = $session->get('usuario');
            
            if (!isset($usuario)) {
              echo "<script>window.location.href = '/login'; </script>";
            }
            else
            {
              $controle_de_acesso = $session->get('controle_de_acesso');
              $array_c_a = json_decode($controle_de_acesso);
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
              $exibe_pedidos = isset($array_c_a->financeiro->modulo, $array_c_a->financeiro->pedidos)
                && (int) $array_c_a->financeiro->modulo === 1
                && (int) $array_c_a->financeiro->pedidos === 1;

              $exibe_menu_vendas = $menu_visivel($array_c_a->vendas ?? null, ['venda_rapida', 'pdv', 'pesq_produto', 'hist_de_vendas']) || $exibe_orcamentos || $exibe_pedidos;
              $exibe_menu_controle_geral = $menu_visivel($array_c_a->controle_geral ?? null, ['clientes', 'fornecedores', 'funcionarios', 'vendedores']);
              $exibe_menu_estoque = $menu_visivel($array_c_a->estoque ?? null, ['produtos', 'reposicoes', 'saida_de_mercadorias', 'categorias_do_produto']);
              $exibe_menu_financeiro = $menu_visivel($array_c_a->financeiro ?? null, ['caixas', 'lancamentos', 'retiradas_do_caixa', 'despesas', 'contas_a_pagar', 'contas_a_receber', 'relatorio_dre', 'inventario_do_estoque', 'controle_fiscal']) || $pode_cobrancas;
              $exibe_menu_relatorios = $menu_visivel($array_c_a->relatorios ?? null, ['vendas', 'estoque', 'financeiro', 'geral']);
              $exibe_menu_configs = $menu_visivel($array_c_a->configs ?? null, ['nfe', 'nfce', 'empresa', 'sistema', 'desenvolvedor', 'usuarios', 'backup_de_dados']);
            ?>

              <?php if($exibe_menu_vendas): ?>
                <li id="2.m" class="nav-item dropdown">
                  <a id="2.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><?= esc(lang('App.menu.salesOs')) ?></a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                    
                    <?php if($array_c_a->vendas->venda_rapida == 1): ?>
                      <li><a id="2.2" href="/vendaRapida" class="dropdown-item"><?= esc(lang('App.menu.quickSale')) ?></a></li>
                    <?php endif; ?>
                    
                    <?php if($array_c_a->vendas->pdv == 1): ?>
                      <li><a id="2.1" href="/pdv" class="dropdown-item"><?= esc(lang('App.menu.pdv')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->vendas->pesq_produto == 1): ?>
                      <li><a id="2.3" href="/produtos/pesquisar" class="dropdown-item"><?= esc(lang('App.menu.productSearch')) ?></a></li>
                    <?php endif; ?>
                    
                    <?php if($array_c_a->vendas->hist_de_vendas == 1): ?>
                      <li><a id="2.4" href="/vendas" class="dropdown-item"><?= esc(lang('App.menu.salesHistory')) ?></a></li>
                    <?php endif; ?>

                    <li class="dropdown-divider"></li>

                    <li><a id="2.6" href="/ordensDeServicos" class="dropdown-item"><?= esc(lang('App.menu.serviceOrders')) ?></a></li>

                    <?php if($exibe_orcamentos): ?>
                      <li><a id="2.7" href="/ordensDeServicos/orcamentos" class="dropdown-item"><?= esc(lang('App.menu.quotes')) ?></a></li>
                    <?php endif; ?>

                    <?php if($exibe_pedidos): ?>
                    <li><a id="2.8" href="/pedidos" class="dropdown-item"><?= esc(lang('App.menu.orders')) ?></a></li>
                    <?php endif; ?>
                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($exibe_menu_controle_geral): ?>
                <li id="3.m" class="nav-item dropdown">
                  <a id="3.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><?= esc(lang('App.menu.generalControl')) ?></a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                    
                    <?php if($array_c_a->controle_geral->clientes == 1): ?>
                      <li><a id="3.1" href="/clientes" class="dropdown-item"><?= esc(lang('App.menu.clients')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->controle_geral->fornecedores == 1): ?>
                      <li><a id="3.2" href="/fornecedores" class="dropdown-item"><?= esc(lang('App.menu.suppliers')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->controle_geral->funcionarios == 1): ?>
                      <li><a id="3.3" href="/funcionarios" class="dropdown-item"><?= esc(lang('App.menu.employees')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->controle_geral->vendedores == 1): ?>
                      <li><a id="3.4" href="/vendedores" class="dropdown-item"><?= esc(lang('App.menu.sellers')) ?></a></li>
                    <?php endif; ?>

                    <li class="dropdown-divider"></li>

                    <li><a id="3.5" href="/tecnicos" class="dropdown-item"><?= esc(lang('App.menu.technicians')) ?></a></li>

                    <li><a id="3.6" href="/servicosMaoDeObra" class="dropdown-item"><?= esc(lang('App.menu.serviceLabor')) ?></a></li>

                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($exibe_menu_estoque): ?>
                <li id="4.m" class="nav-item dropdown">
                  <a id="4.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><?= esc(lang('App.menu.stock')) ?></a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

                    <?php if($array_c_a->estoque->produtos == 1): ?>
                      <li><a id="4.1" href="/produtos" class="dropdown-item"><?= esc(lang('App.menu.products')) ?></a></li>
                    <?php endif; ?>
                    
                    <?php if($array_c_a->estoque->reposicoes == 1): ?>
                      <li><a id="4.3" href="/reposicoes" class="dropdown-item"><?= esc(lang('App.menu.restocks')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->estoque->saida_de_mercadorias == 1): ?>
                      <li><a id="4.4" href="/saidaDeMercadorias" class="dropdown-item"><?= esc(lang('App.menu.goodsOut')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->estoque->categorias_do_produto == 1): ?>
                      <li><a id="4.2" href="/CategoriasDosProdutos" class="dropdown-item"><?= esc(lang('App.menu.productCategories')) ?></a></li>
                    <?php endif; ?>

                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($exibe_menu_financeiro): ?>
                <li id="5.m" class="nav-item dropdown">
                  <a id="5.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><?= esc(lang('App.menu.finance')) ?></a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

                    <?php if($array_c_a->financeiro->caixas == 1): ?>
                      <li><a id="5.1" href="/caixas" class="dropdown-item"><?= esc(lang('App.menu.cashRegisters')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->lancamentos == 1): ?>
                      <li><a id="5.2" href="/lancamentos" class="dropdown-item"><?= esc(lang('App.menu.entries')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->retiradas_do_caixa == 1): ?>
                      <li><a id="5.4" href="/retiradas" class="dropdown-item"><?= esc(lang('App.menu.cashWithdrawals')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->despesas == 1): ?>
                      <li><a id="5.5" href="/despesas" class="dropdown-item"><?= esc(lang('App.menu.expenses')) ?></a></li>
                    <?php endif; ?>

                    <li class="dropdown-divider"></li>

                    <?php if($array_c_a->financeiro->contas_a_pagar == 1): ?>
                      <li><a id="5.6" href="/contasPagar" class="dropdown-item"><?= esc(lang('App.menu.payable')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->contas_a_receber == 1): ?>
                      <li><a id="5.7" href="/contasReceber" class="dropdown-item"><?= esc(lang('App.menu.receivable')) ?></a></li>
                    <?php endif; ?>

                    <?php if($pode_cobrancas): ?>
                      <li><a id="5.8" href="/cobrancas" class="dropdown-item"><?= esc(lang('App.menu.collections')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->relatorio_dre == 1): ?>
                      <li><a id="5.10" href="/relatorioDRE" class="dropdown-item"><?= esc(lang('App.menu.dreReport')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->inventario_do_estoque == 1): ?>
                      <li><a id="5.11" href="/inventarioDoEstoque" class="dropdown-item"><?= esc(lang('App.menu.stockInventory')) ?></a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->controle_fiscal == 1): ?>
                      <li><a id="5.12" href="/controleFiscal" class="dropdown-item">Gestao Fiscal</a></li>
                    <?php endif; ?>

                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($exibe_menu_relatorios): ?>
                <li id="7.m" class="nav-item dropdown">
                  <a id="7.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><?= esc(lang('App.menu.reports')) ?></a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                    <!-- <li><a href="#" class="dropdown-item">Contas à pagar</a></li> -->

                    <?php if($array_c_a->relatorios->vendas == 1): ?>
                      <!-- Vendas -->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><?= esc(lang('App.menu.salesHistory')) ?></a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li><a id="7.1" tabindex="-1" href="/relatorios/historicoCompleto" class="dropdown-item"><?= esc(lang('App.menu.salesComplete')) ?></a></li>
                          <li><a id="7.2" tabindex="-1" href="/relatorios/porCliente" class="dropdown-item"><?= esc(lang('App.menu.salesByClient')) ?></a></li>
                          <li><a id="7.3" tabindex="-1" href="/relatorios/porVendedor" class="dropdown-item"><?= esc(lang('App.menu.salesBySeller')) ?></a></li>
                          </li>
                        </ul>
                      </li>
                      <!-- /Vendas -->
                    <?php endif; ?>

                    <?php if($array_c_a->relatorios->estoque == 1): ?>
                      <!-- Estoque -->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><?= esc(lang('App.menu.stock')) ?></a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li><a id="7.4" tabindex="-1" href="/relatorios/produtos" class="dropdown-item"><?= esc(lang('App.menu.products')) ?></a></li>
                          <li><a id="7.5" tabindex="-1" href="/relatorios/estoqueMinimo" class="dropdown-item"><?= esc(lang('App.menu.stockMinimum')) ?></a></li>
                          <li><a id="7.7" tabindex="-1" href="/relatorios/validadeDosProdutos" class="dropdown-item"><?= esc(lang('App.menu.productExpiration')) ?></a></li>
                          </li>
                        </ul>
                      </li>
                      <!-- /Estoque -->
                    <?php endif; ?>

                    <?php if($array_c_a->relatorios->financeiro == 1): ?>
                      <!-- Financeiro -->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><?= esc(lang('App.menu.finance')) ?></a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li><a id="7.8.1" tabindex="-1" href="/relatorios/faturamentoDiario" class="dropdown-item"><?= esc(lang('App.menu.dailyBilling')) ?></a></li>
                          <li><a id="7.8" tabindex="-1" href="/relatorios/faturamentoDetalhado" class="dropdown-item"><?= esc(lang('App.menu.detailedBilling')) ?></a></li>
                          <li><a id="7.9" tabindex="-1" href="/relatorios/lancamentos" class="dropdown-item"><?= esc(lang('App.menu.financeEntries')) ?></a></li>
                          <li><a id="8.0" tabindex="-1" href="/relatorios/retiradasDoCaixa" class="dropdown-item"><?= esc(lang('App.menu.financeWithdrawals')) ?></a></li>
                          <li><a id="8.1" tabindex="-1" href="/relatorios/despesas" class="dropdown-item"><?= esc(lang('App.menu.financeExpenses')) ?></a></li>
                          <li><a id="8.2" tabindex="-1" href="/relatorios/contasPagar" class="dropdown-item"><?= esc(lang('App.menu.adminPayable')) ?></a></li>
                          <li><a id="8.3" tabindex="-1" href="/relatorios/contasReceber" class="dropdown-item"><?= esc(lang('App.menu.adminReceivable')) ?></a></li>
                          </li>
                        </ul>
                      </li>
                      <!-- /Financeiro -->
                    <?php endif; ?>

                    <?php if($array_c_a->relatorios->geral == 1): ?>
                      <!-- Geral -->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle"><?= esc(lang('App.menu.generalControl')) ?></a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li><a id="8.5" tabindex="-1" href="/relatorios/clientes" class="dropdown-item"><?= esc(lang('App.menu.clients')) ?></a></li>
                          <li><a id="8.6" tabindex="-1" href="/relatorios/fornecedores" class="dropdown-item"><?= esc(lang('App.menu.suppliers')) ?></a></li>
                          <li><a id="8.7" tabindex="-1" href="/relatorios/funcionarios" class="dropdown-item"><?= esc(lang('App.menu.employees')) ?></a></li>
                          <li><a id="8.8" tabindex="-1" href="/relatorios/vendedores" class="dropdown-item"><?= esc(lang('App.menu.sellers')) ?></a></li>
                          </li>
                        </ul>
                      </li>
                      <!-- /Geral -->
                    <?php endif; ?>

                  </ul>
                </li>
              <?php endif; ?>
              
            </ul>
          </div>

          <!-- Right navbar links -->
          <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <li class="nav-item">
              <?= view('templates/tema_cor_toggle', ['classe' => 'nav-link sistema-theme-toggle-navbar']) ?>
            </li>
            <?php if ($pode_alertas_cobrancas) : ?>
              <?= view('templates/alertas_cobrancas_navbar') ?>
            <?php endif; ?>
            <?php if($exibe_menu_configs): ?>
              <li id="11.m" class="nav-item dropdown">
                <a id="11.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><?= esc(lang('App.menu.settings')) ?></a>
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

                  <?php if($array_c_a->configs->nfe == 1): ?>
                    <li><a id="11.1" href="/configs/nfe" class="dropdown-item">NFe</a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->nfce == 1): ?>
                    <li><a id="11.2" href="/configs/nfce" class="dropdown-item">NFCe</a></li>
                  <?php endif; ?>

                  <?php if((int) ($array_c_a->configs->nfe ?? 0) === 1 || (int) ($array_c_a->configs->nfce ?? 0) === 1): ?>
                    <li><a id="11.8" href="/controleFiscal" class="dropdown-item">Gestao Fiscal</a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->empresa == 1): ?>
                    <li><a id="11.3" href="/configs/empresa" class="dropdown-item"><?= esc(lang('App.menu.company')) ?></a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->sistema == 1): ?>
                    <li><a id="11.4" href="/configs/sistema" class="dropdown-item"><?= esc(lang('App.menu.system')) ?></a></li>
                  <?php endif; ?>

                  <?php if((int) ($array_c_a->configs->desenvolvedor ?? $array_c_a->configs->sistema ?? 0) === 1): ?>
                    <li><a id="11.7" href="/desenvolvedor" class="dropdown-item"><?= esc(lang('App.menu.developer')) ?></a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->usuarios == 1): ?>
                    <li><a id="11.5" href="/login/usuarios" class="dropdown-item"><?= esc(lang('App.menu.users')) ?></a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->backup_de_dados == 1): ?>
                    <li><a id="11.6" href="/configs/backupDataBase" class="dropdown-item"><?= esc(lang('App.menu.dataBackup')) ?></a></li>
                  <?php endif; ?>

                </ul>
              </li>
            <?php endif; ?>

          <?php // FIM IF
            }       
          ?>

        <li class="nav-item">
          <a class="nav-link" href="/login/logout"><?= esc(lang('App.menu.logout')) ?> <i
              class="fas fa-sign-out-alt"></i></a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->
