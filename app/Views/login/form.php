<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <form action="/login/store" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> <?= $titulo['modulo'] ?></h6>
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <a href="/login/usuarios" class="btn btn-success button-voltar"><i class="fa fa-arrow-alt-circle-left"></i> Voltar</a>
                                    <?php foreach ($caminhos as $caminho) : ?>
                                        <?php if (!$caminho['active']) : ?>
                                            <li class="breadcrumb-item"><a href="<?= $caminho['rota'] ?>"><?= $caminho['titulo'] ?></a></li>
                                        <?php else : ?>
                                            <li class="breadcrumb-item active"><?= $caminho['titulo'] ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ol>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Primeiro Nome</label>
                                    <input type="text" class="form-control" name="primeiro_nome" value="<?= (isset($usuario)) ? $usuario['primeiro_nome'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Usuário</label>
                                    <input type="text" class="form-control" name="usuario" value="<?= (isset($usuario)) ? $usuario['usuario'] : "" ?>" required="">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Senha</label>
                                    <input type="text" class="form-control" name="senha" value="<?= (isset($usuario)) ? $usuario['senha'] : "" ?>" required="">
                                </div>
                            </div>

                            <?php if (isset($usuario)) : ?>
                                <!-- HIDDENS EDIT -->
                                <input type="hidden" class="form-control" name="id_login" value="<?= $usuario['id_login'] ?>">
                            <?php endif; ?>

                        </div>
                    </div>
                    <!-- /.card-body -->
                    <!-- <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary"><?= (isset($usuario)) ? "Atualizar" : "Cadastrar" ?></button>
                            </div>
                        </div>
                    </div> -->
                </div>
                <!-- /.card -->
                
                <?php
                    if(isset($usuario))
                    {
                        $controle_de_acesso = json_decode($usuario['controle_de_acesso']);
                    }
                ?>

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-12">
                                <h6 class="m-0 text-dark"><i class="<?= $titulo['icone'] ?>"></i> Permissões no Sistema</h6>
                            </div><!-- /.col -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <?php if(isset($usuario)): ?>
                            <div class="row">
                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">VENDAS</label>
                                    <input type="checkbox" id="modulo_de_vendas" name="modulo_vendas" <?= ($controle_de_acesso->vendas->modulo == 1) ? "checked" : "" ?> onclick="desabilitaModuloVendas()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Venda Rápida</label>
                                            <select class="form-control" id="venda_rapida" name="venda_rapida" <?= (!$controle_de_acesso->vendas->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->vendas->venda_rapida == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">PDV</label>
                                            <select class="form-control" id="pdv" name="pdv" <?= (!$controle_de_acesso->vendas->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->vendas->pdv == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Pesq. Produto</label>
                                            <select class="form-control" id="pesq_produto" name="pesq_produto" <?= (!$controle_de_acesso->vendas->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->vendas->pesq_produto == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Hist. de Vendas</label>
                                            <select class="form-control" id="hist_de_vendas" name="hist_de_vendas" <?= (!$controle_de_acesso->vendas->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->vendas->hist_de_vendas == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">CONTROLE GERAL</label>
                                    <input type="checkbox" id="modulo_controle_geral" name="modulo_controle_geral" <?= ($controle_de_acesso->controle_geral->modulo == 1) ? "checked" : "" ?> onclick="AcoesModuloControleGeral()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Clientes</label>
                                            <select class="form-control" id="clientes" name="clientes" <?= (!$controle_de_acesso->controle_geral->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->controle_geral->clientes == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Fornecedores</label>
                                            <select class="form-control" id="fornecedores" name="fornecedores" <?= (!$controle_de_acesso->controle_geral->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->controle_geral->fornecedores == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Funcionários</label>
                                            <select class="form-control" id="funcionarios" name="funcionarios" <?= (!$controle_de_acesso->controle_geral->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->controle_geral->funcionarios == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Vendedores</label>
                                            <select class="form-control" id="vendedores" name="vendedores" <?= (!$controle_de_acesso->controle_geral->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->controle_geral->vendedores == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">ESTOQUE</label>
                                    <input type="checkbox" id="modulo_estoque" name="modulo_estoque" <?= ($controle_de_acesso->estoque->modulo == 1) ? "checked" : "" ?> onclick="AcoesModuloEstoque()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Produtos</label>
                                            <select class="form-control" id="produtos" name="produtos" <?= (!$controle_de_acesso->estoque->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->estoque->produtos == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Reposições</label>
                                            <select class="form-control" id="reposicoes" name="reposicoes" <?= (!$controle_de_acesso->estoque->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->estoque->reposicoes == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Saída de Mercadorias</label>
                                            <select class="form-control" id="saida_de_mercadorias" name="saida_de_mercadorias" <?= (!$controle_de_acesso->estoque->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->estoque->saida_de_mercadorias == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Categorias do Produto</label>
                                            <select class="form-control" id="categorias_do_produto" name="categorias_do_produto" <?= (!$controle_de_acesso->estoque->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->estoque->categorias_do_produto == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">RELATÓRIOS</label>
                                    <input type="checkbox" id="modulo_relatorios" name="modulo_relatorios" <?= ($controle_de_acesso->relatorios->modulo == 1) ? "checked" : "" ?> onclick="AcoesModuloRelatorios()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Vendas</label>
                                            <select class="form-control" id="vendas" name="vendas" <?= (!$controle_de_acesso->relatorios->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->relatorios->vendas == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Estoque</label>
                                            <select class="form-control" id="estoque" name="estoque" <?= (!$controle_de_acesso->relatorios->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->relatorios->estoque == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Financeiro</label>
                                            <select class="form-control" id="financeiro" name="financeiro" <?= (!$controle_de_acesso->relatorios->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->relatorios->financeiro == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Geral</label>
                                            <select class="form-control" id="geral" name="geral" <?= (!$controle_de_acesso->relatorios->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->relatorios->geral == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">FINANCEIRO</label>
                                    <input type="checkbox" id="modulo_financeiro" name="modulo_financeiro" <?= ($controle_de_acesso->financeiro->modulo == 1) ? "checked" : "" ?> onclick="AcoesModuloFinanceiro()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Caixas</label>
                                            <select class="form-control" id="caixas" name="caixas" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->caixas == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Lançamentos</label>
                                            <select class="form-control" id="lancamentos" name="lancamentos" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->lancamentos == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Retiradas do Caixa</label>
                                            <select class="form-control" id="retiradas_do_caixa" name="retiradas_do_caixa" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->retiradas_do_caixa == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Despesas</label>
                                            <select class="form-control" id="despesas" name="despesas" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->despesas == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div><hr></div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Contas à Pagar</label>
                                            <select class="form-control" id="contas_a_pagar" name="contas_a_pagar" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->contas_a_pagar == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Contas á Receber</label>
                                            <select class="form-control" id="contas_a_receber" name="contas_a_receber" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->contas_a_receber == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Orçamentos</label>
                                            <select class="form-control" id="orcamentos" name="orcamentos" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->orcamentos == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Pedidos</label>
                                            <select class="form-control" id="pedidos" name="pedidos" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->pedidos == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Relatório DRE</label>
                                            <select class="form-control" id="relatorio_dre" name="relatorio_dre" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->relatorio_dre == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Inventário do Estoque</label>
                                            <select class="form-control" id="inventario_do_estoque" name="inventario_do_estoque" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->inventario_do_estoque == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Controle Fiscal</label>
                                            <select class="form-control" id="controle_fiscal" name="controle_fiscal" <?= (!$controle_de_acesso->financeiro->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->financeiro->controle_fiscal == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">CONFIGS</label>
                                    <input type="checkbox" id="modulo_configs" name="modulo_configs" <?= ($controle_de_acesso->configs->modulo == 1) ? "checked" : "" ?> onclick="AcoesModuloConfigs()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">NFe</label>
                                            <select class="form-control" id="nfe" name="nfe" <?= (!$controle_de_acesso->configs->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->configs->nfe == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">NFCe</label>
                                            <select class="form-control" id="nfce" name="nfce" <?= (!$controle_de_acesso->configs->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->configs->nfce == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Empresa</label>
                                            <select class="form-control" id="empresa" name="empresa" <?= (!$controle_de_acesso->configs->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->configs->empresa == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Sistema</label>
                                            <select class="form-control" id="sistema" name="sistema" <?= (!$controle_de_acesso->configs->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->configs->sistema == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Usuários</label>
                                            <select class="form-control" id="usuarios" name="usuarios" <?= (!$controle_de_acesso->configs->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->configs->usuarios == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Backup de Dados</label>
                                            <select class="form-control" id="backup_de_dados" name="backup_de_dados" <?= (!$controle_de_acesso->configs->modulo == 1) ? "disabled" : "" ?>>
                                                <?php if($controle_de_acesso->configs->backup_de_dados == 1): ?>
                                                    <option value="1" selected>Sim</option>
                                                    <option value="0">Não</option>
                                                <?php else: ?>
                                                    <option value="1">Sim</option>
                                                    <option value="0" selected>Não</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">VENDAS</label>
                                    <input type="checkbox" id="modulo_de_vendas" name="modulo_vendas" checked onclick="desabilitaModuloVendas()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Venda Rápida</label>
                                            <select class="form-control" id="venda_rapida" name="venda_rapida">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">PDV</label>
                                            <select class="form-control" id="pdv" name="pdv">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Pesq. Produto</label>
                                            <select class="form-control" id="pesq_produto" name="pesq_produto">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Hist. de Vendas</label>
                                            <select class="form-control" id="hist_de_vendas" name="hist_de_vendas">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">CONTROLE GERAL</label>
                                    <input type="checkbox" id="modulo_controle_geral" name="modulo_controle_geral" checked onclick="AcoesModuloControleGeral()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Clientes</label>
                                            <select class="form-control" id="clientes" name="clientes">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Fornecedores</label>
                                            <select class="form-control" id="fornecedores" name="fornecedores">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Funcionários</label>
                                            <select class="form-control" id="funcionarios" name="funcionarios">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Vendedores</label>
                                            <select class="form-control" id="vendedores" name="vendedores">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">ESTOQUE</label>
                                    <input type="checkbox" id="modulo_estoque" name="modulo_estoque" checked onclick="AcoesModuloEstoque()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Produtos</label>
                                            <select class="form-control" id="produtos" name="produtos">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Reposições</label>
                                            <select class="form-control" id="reposicoes" name="reposicoes">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Saída de Mercadorias</label>
                                            <select class="form-control" id="saida_de_mercadorias" name="saida_de_mercadorias">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Categorias do Produto</label>
                                            <select class="form-control" id="categorias_do_produto" name="categorias_do_produto">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">RELATÓRIOS</label>
                                    <input type="checkbox" id="modulo_relatorios" name="modulo_relatorios" checked onclick="AcoesModuloRelatorios()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Vendas</label>
                                            <select class="form-control" id="vendas" name="vendas">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Estoque</label>
                                            <select class="form-control" id="estoque" name="estoque">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Financeiro</label>
                                            <select class="form-control" id="financeiro" name="financeiro">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">financeiro</label>
                                            <select class="form-control" id="financeiro" name="financeiro">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Geral</label>
                                            <select class="form-control" id="geral" name="geral">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">FINANCEIRO</label>
                                    <input type="checkbox" id="modulo_financeiro" name="modulo_financeiro" checked onclick="AcoesModuloFinanceiro()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Caixas</label>
                                            <select class="form-control" id="caixas" name="caixas">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Lançamentos</label>
                                            <select class="form-control" id="lancamentos" name="lancamentos">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Retiradas do Caixa</label>
                                            <select class="form-control" id="retiradas_do_caixa" name="retiradas_do_caixa">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Despesas</label>
                                            <select class="form-control" id="despesas" name="despesas">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Contas à Pagar</label>
                                            <select class="form-control" id="contas_a_pagar" name="contas_a_pagar">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Contas á Receber</label>
                                            <select class="form-control" id="contas_a_receber" name="contas_a_receber">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Orçamentos</label>
                                            <select class="form-control" id="orcamentos" name="orcamentos">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Pedidos</label>
                                            <select class="form-control" id="pedidos" name="pedidos">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Relatório DRE</label>
                                            <select class="form-control" id="relatorio_dre" name="relatorio_dre">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Inventário do Estoque</label>
                                            <select class="form-control" id="inventario_do_estoque" name="inventario_do_estoque">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Controle Fiscal</label>
                                            <select class="form-control" id="controle_fiscal" name="controle_fiscal">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3" style="border: 1px solid lightgrey;">
                                    <label style="margin-top: 10px">CONFIGS</label>
                                    <input type="checkbox" id="modulo_configs" name="modulo_configs" checked onclick="AcoesModuloConfigs()">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">NFe</label>
                                            <select class="form-control" id="nfe" name="nfe">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">NFCe</label>
                                            <select class="form-control" id="nfce" name="nfce">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Empresa</label>
                                            <select class="form-control" id="empresa" name="empresa">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Sistema</label>
                                            <select class="form-control" id="sistema" name="sistema">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Usuários</label>
                                            <select class="form-control" id="usuarios" name="usuarios">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="">Backup de Dados</label>
                                            <select class="form-control" id="backup_de_dados" name="backup_de_dados">
                                                <option value="1">Sim</option>
                                                <option value="0">Não</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12" style="text-align: right">
                                <button type="submit" class="btn btn-primary"><?= (isset($usuario)) ? "Atualizar" : "Cadastrar" ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </form>
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    function desabilitaModuloVendas()
    {
        if(document.getElementById('modulo_de_vendas').checked)
        {
            document.getElementById('venda_rapida').disabled = false;
            document.getElementById('pdv').disabled = false;
            document.getElementById('pesq_produto').disabled = false;
            document.getElementById('hist_de_vendas').disabled = false;
        }
        else
        {
            document.getElementById('venda_rapida').disabled = true;
            document.getElementById('pdv').disabled = true;
            document.getElementById('pesq_produto').disabled = true;
            document.getElementById('hist_de_vendas').disabled = true;
        }
    }

    function AcoesModuloControleGeral()
    {
        if(document.getElementById('modulo_controle_geral').checked)
        {
            document.getElementById('clientes').disabled = false;
            document.getElementById('fornecedores').disabled = false;
            document.getElementById('funcionarios').disabled = false;
            document.getElementById('vendedores').disabled = false;
        }
        else
        {
            document.getElementById('clientes').disabled = true;
            document.getElementById('fornecedores').disabled = true;
            document.getElementById('funcionarios').disabled = true;
            document.getElementById('vendedores').disabled = true;
        }
    }

    function AcoesModuloEstoque()
    {
        if(document.getElementById('modulo_estoque').checked)
        {
            document.getElementById('produtos').disabled = false;
            document.getElementById('reposicoes').disabled = false;
            document.getElementById('saida_de_mercadorias').disabled = false;
            document.getElementById('categorias_do_produto').disabled = false;
        }
        else
        {
            document.getElementById('produtos').disabled = true;
            document.getElementById('reposicoes').disabled = true;
            document.getElementById('saida_de_mercadorias').disabled = true;
            document.getElementById('categorias_do_produto').disabled = true;
        }
    }

    function AcoesModuloFinanceiro()
    {
        if(document.getElementById('modulo_financeiro').checked)
        {
            document.getElementById('caixas').disabled = false;
            document.getElementById('lancamentos').disabled = false;
            document.getElementById('retiradas_do_caixa').disabled = false;
            document.getElementById('despesas').disabled = false;

            document.getElementById('contas_a_pagar').disabled = false;
            document.getElementById('contas_a_receber').disabled = false;
            document.getElementById('orcamentos').disabled = false;
            document.getElementById('pedidos').disabled = false;
            document.getElementById('relatorio_dre').disabled = false;
            document.getElementById('inventario_do_estoque').disabled = false;
            document.getElementById('controle_fiscal').disabled = false;
        }
        else
        {
            document.getElementById('caixas').disabled = true;
            document.getElementById('lancamentos').disabled = true;
            document.getElementById('retiradas_do_caixa').disabled = true;
            document.getElementById('despesas').disabled = true;

            document.getElementById('contas_a_pagar').disabled = true;
            document.getElementById('contas_a_receber').disabled = true;
            document.getElementById('orcamentos').disabled = true;
            document.getElementById('pedidos').disabled = true;
            document.getElementById('relatorio_dre').disabled = true;
            document.getElementById('inventario_do_estoque').disabled = true;
            document.getElementById('controle_fiscal').disabled = true;
        }
    }

    function AcoesModuloRelatorios()
    {
        if(document.getElementById('modulo_relatorios').checked)
        {
            document.getElementById('vendas').disabled = false;
            document.getElementById('estoque').disabled = false;
            document.getElementById('financeiro').disabled = false;
            document.getElementById('geral').disabled = false;
        }
        else
        {
            document.getElementById('vendas').disabled = true;
            document.getElementById('estoque').disabled = true;
            document.getElementById('financeiro').disabled = true;
            document.getElementById('geral').disabled = true;
        }
    }

    function AcoesModuloConfigs()
    {
        if(document.getElementById('modulo_configs').checked)
        {
            document.getElementById('nfe').disabled = false;
            document.getElementById('nfce').disabled = false;
            document.getElementById('empresa').disabled = false;
            document.getElementById('sistema').disabled = false;
            document.getElementById('usuarios').disabled = false;
            document.getElementById('backup_de_dados').disabled = false;
        }
        else
        {
            document.getElementById('nfe').disabled = true;
            document.getElementById('nfce').disabled = true;
            document.getElementById('empresa').disabled = true;
            document.getElementById('sistema').disabled = true;
            document.getElementById('usuarios').disabled = true;
            document.getElementById('backup_de_dados').disabled = true;
        }
    }
</script>