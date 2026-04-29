<!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
      <a href="/inicio" class="navbar-brand">
        <!-- <img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8"> -->
        <span class="brand-text font-weight-light"><b><?= $session->get('nome_fantasia') ?></b></span>
      </a>
      
      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li id="1.m" class="nav-item">
        	<a id="1.0" href="/inicio" class="nav-link">Início</a>
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

              $exibe_menu_vendas = $menu_visivel($array_c_a->vendas ?? null, ['venda_rapida', 'pdv', 'pesq_produto', 'hist_de_vendas']);
              $exibe_menu_controle_geral = $menu_visivel($array_c_a->controle_geral ?? null, ['clientes', 'fornecedores', 'funcionarios', 'vendedores']);
              $exibe_menu_estoque = $menu_visivel($array_c_a->estoque ?? null, ['produtos', 'reposicoes', 'saida_de_mercadorias', 'categorias_do_produto']);
              $exibe_menu_financeiro = $menu_visivel($array_c_a->financeiro ?? null, ['caixas', 'lancamentos', 'retiradas_do_caixa', 'despesas', 'contas_a_pagar', 'contas_a_receber', 'orcamentos', 'pedidos', 'relatorio_dre', 'inventario_do_estoque', 'controle_fiscal']);
              $exibe_menu_relatorios = $menu_visivel($array_c_a->relatorios ?? null, ['vendas', 'estoque', 'financeiro', 'geral']);
              $exibe_menu_configs = $menu_visivel($array_c_a->configs ?? null, ['nfe', 'nfce', 'empresa', 'sistema', 'usuarios', 'backup_de_dados']);
            ?>

              <?php if($exibe_menu_vendas): ?>
                <li id="2.m" class="nav-item dropdown">
                  <a id="2.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Vendas e OS</a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                    
                    <?php if($array_c_a->vendas->venda_rapida == 1): ?>
                      <li><a id="2.2" href="/vendaRapida" class="dropdown-item">Venda Rápida</a></li>
                    <?php endif; ?>
                    
                    <?php if($array_c_a->vendas->pdv == 1): ?>
                      <li><a id="2.1" href="/pdv" class="dropdown-item">PDV</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->vendas->pesq_produto == 1): ?>
                      <li><a id="2.3" href="/produtos/pesquisar" class="dropdown-item">Pesq. Produto </a></li>
                    <?php endif; ?>
                    
                    <?php if($array_c_a->vendas->hist_de_vendas == 1): ?>
                      <li><a id="2.4" href="/vendas" class="dropdown-item">Hist. de Vendas</a></li>
                    <?php endif; ?>

                    <li class="dropdown-divider"></li>

                    <li><a id="2.5" href="/ordensDeServicos/create" class="dropdown-item">Gerar Ordem de Serv.</a></li>

                    <li><a id="2.6" href="/ordensDeServicos" class="dropdown-item">Ordens de Serviços</a></li>
                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($exibe_menu_controle_geral): ?>
                <li id="3.m" class="nav-item dropdown">
                  <a id="3.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Controle Geral</a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                    
                    <?php if($array_c_a->controle_geral->clientes == 1): ?>
                      <li><a id="3.1" href="/clientes" class="dropdown-item">Clientes</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->controle_geral->fornecedores == 1): ?>
                      <li><a id="3.2" href="/fornecedores" class="dropdown-item">Fornecedores</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->controle_geral->funcionarios == 1): ?>
                      <li><a id="3.3" href="/funcionarios" class="dropdown-item">Funcionários</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->controle_geral->vendedores == 1): ?>
                      <li><a id="3.4" href="/vendedores" class="dropdown-item">Vendedores</a></li>
                    <?php endif; ?>

                    <li class="dropdown-divider"></li>

                    <li><a id="3.5" href="/tecnicos" class="dropdown-item">Técnicos</a></li>

                    <li><a id="3.6" href="/servicosMaoDeObra" class="dropdown-item">Serviço/Mão de Obra</a></li>

                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($exibe_menu_estoque): ?>
                <li id="4.m" class="nav-item dropdown">
                  <a id="4.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Estoque</a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

                    <?php if($array_c_a->estoque->produtos == 1): ?>
                      <li><a id="4.1" href="/produtos" class="dropdown-item">Produtos</a></li>
                    <?php endif; ?>
                    
                    <?php if($array_c_a->estoque->reposicoes == 1): ?>
                      <li><a id="4.3" href="/reposicoes" class="dropdown-item">Reposições</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->estoque->saida_de_mercadorias == 1): ?>
                      <li><a id="4.4" href="/saidaDeMercadorias" class="dropdown-item">Saída de mercadorias</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->estoque->categorias_do_produto == 1): ?>
                      <li><a id="4.2" href="/CategoriasDosProdutos" class="dropdown-item">Categorias do Produto</a></li>
                    <?php endif; ?>

                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($exibe_menu_financeiro): ?>
                <li id="5.m" class="nav-item dropdown">
                  <a id="5.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Financeiro</a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

                    <?php if($array_c_a->financeiro->caixas == 1): ?>
                      <li><a id="5.1" href="/caixas" class="dropdown-item">Caixas</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->lancamentos == 1): ?>
                      <li><a id="5.2" href="/lancamentos" class="dropdown-item">Lançamentos</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->retiradas_do_caixa == 1): ?>
                      <li><a id="5.4" href="/retiradas" class="dropdown-item">Retiradas do Caixa</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->despesas == 1): ?>
                      <li><a id="5.5" href="/despesas" class="dropdown-item">Despesas</a></li>
                    <?php endif; ?>

                    <li class="dropdown-divider"></li>

                    <?php if($array_c_a->financeiro->contas_a_pagar == 1): ?>
                      <li><a id="5.6" href="/contasPagar" class="dropdown-item">Contas à pagar</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->contas_a_receber == 1): ?>
                      <li><a id="5.7" href="/contasReceber" class="dropdown-item">Contas à receber</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->orcamentos == 1): ?>
                      <li><a id="5.8" href="/orcamentos" class="dropdown-item">Orçamentos</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->pedidos == 1): ?>
                      <li><a id="5.9" href="/pedidos" class="dropdown-item">Pedidos</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->relatorio_dre == 1): ?>
                      <li><a id="5.10" href="/relatorioDRE" class="dropdown-item">Relatório DRE</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->inventario_do_estoque == 1): ?>
                      <li><a id="5.11" href="/inventarioDoEstoque" class="dropdown-item">Inventário do Estoque</a></li>
                    <?php endif; ?>

                    <?php if($array_c_a->financeiro->controle_fiscal == 1): ?>
                      <!-- Level two dropdown-->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Controle Fiscal</a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li>
                            <a id="5.12" tabindex="-1" href="/controleFiscal/nfe" class="dropdown-item">NFe</a>
                          </li>
                          <li>
                            <a id="5.13" tabindex="-1" href="/controleFiscal/nfce" class="dropdown-item">NFCe</a>
                          </li>
                          </li>
                        </ul>
                      </li>
                      <!-- End Level two -->
                    <?php endif; ?>

                  </ul>
                </li>
              <?php endif; ?>
              
              <?php if($exibe_menu_relatorios): ?>
                <li id="7.m" class="nav-item dropdown">
                  <a id="7.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Relatórios</a>
                  <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                    <!-- <li><a href="#" class="dropdown-item">Contas à pagar</a></li> -->

                    <?php if($array_c_a->relatorios->vendas == 1): ?>
                      <!-- Vendas -->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Vendas</a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li><a id="7.1" tabindex="-1" href="/relatorios/historicoCompleto" class="dropdown-item">Histórico Completo</a></li>
                          <li><a id="7.2" tabindex="-1" href="/relatorios/porCliente" class="dropdown-item">Por Cliente</a></li>
                          <li><a id="7.3" tabindex="-1" href="/relatorios/porVendedor" class="dropdown-item">Por Vendedor</a></li>
                          </li>
                        </ul>
                      </li>
                      <!-- /Vendas -->
                    <?php endif; ?>

                    <?php if($array_c_a->relatorios->estoque == 1): ?>
                      <!-- Estoque -->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Estoque</a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li><a id="7.4" tabindex="-1" href="/relatorios/produtos" class="dropdown-item">Produtos</a></li>
                          <li><a id="7.5" tabindex="-1" href="/relatorios/estoqueMinimo" class="dropdown-item">Estoque Mínimo</a></li>
                          <li><a id="7.6" tabindex="-1" href="/inventarioDoEstoque" class="dropdown-item">Inventário</a></li>
                          <li><a id="7.7" tabindex="-1" href="/relatorios/validadeDosProdutos" class="dropdown-item">Validade de produto</a></li>
                          </li>
                        </ul>
                      </li>
                      <!-- /Estoque -->
                    <?php endif; ?>

                    <?php if($array_c_a->relatorios->financeiro == 1): ?>
                      <!-- Financeiro -->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Financeiro</a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li><a id="7.8.1" tabindex="-1" href="/relatorios/faturamentoDiario" class="dropdown-item">Faturamento Diário</a></li>
                          <li><a id="7.8" tabindex="-1" href="/relatorios/faturamentoDetalhado" class="dropdown-item">Faturamento Detalhado</a></li>
                          <li><a id="7.9" tabindex="-1" href="/relatorios/lancamentos" class="dropdown-item">Lançamentos</a></li>
                          <li><a id="8.0" tabindex="-1" href="/relatorios/retiradasDoCaixa" class="dropdown-item">Retiradas do Caixa</a></li>
                          <li><a id="8.1" tabindex="-1" href="/relatorios/despesas" class="dropdown-item">Despesas</a></li>
                          <li><a id="8.2" tabindex="-1" href="/relatorios/contasPagar" class="dropdown-item">Contas à Pagar</a></li>
                          <li><a id="8.3" tabindex="-1" href="/relatorios/contasReceber" class="dropdown-item">Contas à Receber</a></li>
                          <li><a id="8.4" tabindex="-1" href="/relatorioDRE" class="dropdown-item">DRE</a></li>
                          </li>
                        </ul>
                      </li>
                      <!-- /Financeiro -->
                    <?php endif; ?>

                    <?php if($array_c_a->relatorios->geral == 1): ?>
                      <!-- Geral -->
                      <li class="dropdown-submenu dropdown-hover">
                        <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Geral</a>
                        <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                          <li><a id="8.5" tabindex="-1" href="/relatorios/clientes" class="dropdown-item">Clientes</a></li>
                          <li><a id="8.6" tabindex="-1" href="/relatorios/fornecedores" class="dropdown-item">Fornecedores</a></li>
                          <li><a id="8.7" tabindex="-1" href="/relatorios/funcionarios" class="dropdown-item">Funcionários</a></li>
                          <li><a id="8.8" tabindex="-1" href="/relatorios/vendedores" class="dropdown-item">Vendedores</a></li>
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
            <?php if($exibe_menu_configs): ?>
              <li id="11.m" class="nav-item dropdown">
                <a id="11.0" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Configs</a>
                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

                  <?php if($array_c_a->configs->nfe == 1): ?>
                    <li><a id="11.1" href="/configs/nfe" class="dropdown-item">NFe</a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->nfce == 1): ?>
                    <li><a id="11.2" href="/configs/nfce" class="dropdown-item">NFCe</a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->empresa == 1): ?>
                    <li><a id="11.3" href="/configs/empresa" class="dropdown-item">Empresa</a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->sistema == 1): ?>
                    <li><a id="11.4" href="/configs/sistema" class="dropdown-item">Sistema</a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->usuarios == 1): ?>
                    <li><a id="11.5" href="/login/usuarios" class="dropdown-item">Usuários</a></li>
                  <?php endif; ?>

                  <?php if($array_c_a->configs->backup_de_dados == 1): ?>
                    <li><a id="11.6" href="/configs/backupDataBase" class="dropdown-item">Backup Dados</a></li>
                  <?php endif; ?>

                </ul>
              </li>
            <?php endif; ?>

          <?php // FIM IF
            }       
          ?>

        <li class="nav-item">
          <a class="nav-link" href="/login/logout">Sair <i
              class="fas fa-sign-out-alt"></i></a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->
