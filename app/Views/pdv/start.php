<?php
$nomeEmpresa = trim((string) ($empresa['nome_fantasia'] ?? '')) ?: 'PDV';
$logoPdv = trim((string) ($empresa['logo_login'] ?? '')) ?: 'assets/img/zaventus-logo-completa-353079a01cd5.png';
$faviconPdv = trim((string) ($empresa['favicon'] ?? '')) ?: 'assets/img/favicon-cmy-7f5ab7a5892e.png';
$fusoHorarioPdv = trim((string) ($empresa['fuso_horario'] ?? '')) ?: 'America/Manaus';
$finalizarComNfce = ($empresa['finalizacao_pdv'] ?? 'cupom_nao_fiscal') === 'nfce';
$totalPdv = (float) ($valor_a_pagar['valor_final'] ?? 0);
$security = config('Security');
?>
<!DOCTYPE html>
<html lang="pt_BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token-name" content="<?= esc(csrf_token()) ?>">
    <meta name="csrf-token-value" content="<?= esc(csrf_hash()) ?>">
    <meta name="csrf-header-name" content="<?= esc($security->headerName) ?>">

    <title>PDV | <?= esc($nomeEmpresa) ?></title>

    <link rel="icon" href="<?= esc(base_url($faviconPdv)) ?>" sizes="any">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/fontawesome-free/css/all.css') ?>">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.css') ?>">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css') ?>">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url('theme/plugins/select2/css/select2.css') ?>">
    <link rel="stylesheet" href="<?= base_url('theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('theme/dist/css/adminlte.css') ?>">
    <!-- Style -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
    <script src="<?= base_url('assets/js/tema-cor.js?v=' . filemtime(FCPATH . 'assets/js/tema-cor.js')) ?>"></script>
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- ========= Scripts com prioridade ============= -->
    <!-- jQuery -->
    <script src="<?= base_url('theme/plugins/jquery/jquery.js') ?>"></script>
    <!-- SweetAlert2 -->
    <script src="<?= base_url('theme/plugins/sweetalert2/sweetalert2.js') ?>"></script>
    <!-- OPTIONAL SCRIPTS -->
    <script src="<?= base_url('theme/plugins/chart.js/Chart.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/moeda-padrao.js?v=' . filemtime(FCPATH . 'assets/js/moeda-padrao.js')) ?>"></script>
    <style>
        .pdv-topo {
            align-items: center;
            background: #ffffff;
            border-bottom: 3px solid #0f766e;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
            display: flex;
            justify-content: space-between;
            min-height: 92px;
            padding: 12px 24px;
        }

        .pdv-marca {
            align-items: center;
            display: flex;
            gap: 16px;
        }

        .pdv-marca img {
            max-height: 62px;
            max-width: 180px;
            object-fit: contain;
        }

        .pdv-marca-titulo span,
        .pdv-relogio span {
            color: #6c757d;
            display: block;
            font-size: 13px;
            text-transform: uppercase;
        }

        .pdv-marca-titulo strong {
            color: #1f2937;
            display: block;
            font-size: 24px;
            line-height: 1.1;
        }

        .pdv-relogio {
            text-align: right;
        }

        .pdv-relogio strong {
            color: #0f766e;
            display: block;
            font-size: 32px;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }

        .cupom-nao-fiscal-conteudo {
            color: #000;
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.35;
        }

        .cupom-cabecalho,
        .cupom-titulo,
        .cupom-rodape {
            text-align: center;
        }

        .cupom-cabecalho span,
        .cupom-titulo span {
            display: block;
        }

        .cupom-logo {
            display: block;
            margin: 0 auto 6px;
            max-height: 54px;
            max-width: 150px;
            object-fit: contain;
        }

        .cupom-empresa {
            display: block;
            font-size: 15px;
        }

        .cupom-titulo,
        .cupom-separador {
            border-bottom: 1px dashed #000;
            border-top: 1px dashed #000;
            margin: 8px 0;
            padding: 6px 0;
        }

        .cupom-titulo strong {
            display: block;
            font-size: 14px;
        }

        .cupom-separador {
            font-weight: bold;
            text-align: center;
        }

        .cupom-item {
            border-bottom: 1px dotted #777;
            padding: 5px 0;
        }

        .cupom-linha {
            display: flex;
            gap: 8px;
            justify-content: space-between;
        }

        .cupom-item-desconto {
            font-size: 10px;
            text-align: right;
        }

        .cupom-totais {
            margin-top: 8px;
        }

        .cupom-total-final {
            border-bottom: 1px dashed #000;
            border-top: 1px dashed #000;
            font-size: 15px;
            margin: 5px 0;
            padding: 5px 0;
        }

        .cupom-pagamento {
            border-top: 1px dotted #777;
            margin-top: 5px;
            padding-top: 5px;
        }

        .cupom-rodape {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 8px;
        }

        @media (max-width: 767px) {
            .pdv-topo {
                align-items: flex-start;
                padding: 10px 14px;
            }

            .pdv-marca img {
                max-height: 46px;
                max-width: 110px;
            }

            .pdv-marca-titulo strong {
                font-size: 18px;
            }

            .pdv-relogio strong {
                font-size: 24px;
            }
        }

        @media print {
            body {
                background: #fff !important;
            }

            .modal-backdrop {
                display: none !important;
            }

            #modal-cupom-nao-fiscal {
                display: block !important;
                position: static !important;
            }

            #modal-cupom-nao-fiscal .modal-dialog {
                margin: 0 !important;
                width: 80mm !important;
            }

            #modal-cupom-nao-fiscal .modal-content,
            #modal-cupom-nao-fiscal .modal-body {
                border: 0 !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>

<body class="pdv-page" style="background: lightgrey">
    <header class="pdv-topo no-print">
        <div class="pdv-marca">
            <img src="<?= esc(base_url($logoPdv)) ?>" alt="<?= esc($nomeEmpresa) ?>">
            <div class="pdv-marca-titulo">
                <span>Ponto de Venda</span>
                <strong><?= esc($nomeEmpresa) ?></strong>
                <small>Caixa #<?= esc($id_caixa) ?></small>
            </div>
        </div>
        <?= view('templates/tema_cor_toggle', ['classe' => 'sistema-theme-toggle-pdv']) ?>
        <div class="pdv-relogio" aria-label="Data e horário atual">
            <span id="pdv-data-atual"><?= date('d/m/Y') ?></span>
            <strong id="pdv-horario-atual"><?= date('H:i:s') ?></strong>
        </div>
    </header>
    <!-- Modal Altera Quantidade -->
    <div class="modal fade" id="alterar-qtd-do-produto">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Alterar QTD</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/pdv/alteraQtdDoProduto/<?= $id_caixa ?>" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">Nova QTD</label>
                                    <input type="text" class="form-control" id="altera_qtd_do_produto_quantidade" name="quantidade" onkeyup="trocaVirguraPorPonto('altera_qtd_do_produto_quantidade')">
                                    <input type="hidden" class="form-control" id="altera_qtd_do_produto_id_pdv_produto" name="id_produto_pdv" onkeyup="trocaVirguraPorPonto('altera_qtd_do_produto_id_pdv_produto')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Continuar</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal Altera Valor Unitário -->
    <div class="modal fade" id="alterar-valor-unitario-do-produto">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Alterar Valor Unitário</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/pdv/alteraValorUnitarioDoProduto/<?= $id_caixa ?>" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">Valor Unitário</label>
                                    <input type="text" class="form-control" id="altera_valor_unitario_do_produto_valor_unitario" name="valor_unitario" onkeyup="trocaVirguraPorPonto('altera_valor_unitario_do_produto_valor_unitario')">
                                    <input type="hidden" class="form-control" id="altera_valor_unitario_do_produto_id_pdv_produto" name="id_produto_pdv" onkeyup="trocaVirguraPorPonto('altera_valor_unitario_do_produto_id_pdv_produto')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Continuar</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal Altera Desconto -->
    <div class="modal fade" id="alterar-desconto-do-produto">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Alterar Desconto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/pdv/alteraDescontoDoProduto/<?= $id_caixa ?>" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">Desconto</label>
                                    <input type="text" class="form-control" id="altera_desconto_do_produto_valor_unitario" name="desconto" onkeyup="trocaVirguraPorPonto('altera_desconto_do_produto_valor_unitario')">
                                    <input type="hidden" class="form-control" id="altera_desconto_do_produto_id_pdv_produto" name="id_produto_pdv" onkeyup="trocaVirguraPorPonto('altera_desconto_do_produto_id_pdv_produto')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Continuar</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal Finalizar Venda -->
    <div class="modal fade" id="finalizar-venda">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Valor da Compra: <b>R$ <?= decimal_monetario($totalPdv) ?></b> | Valor a Pagar: <b>R$ <span id="valor_a_pagar_informativo"><?= decimal_monetario($totalPdv) ?></span></b></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Valor Recebido</label>
                                    <input type="text" class="form-control" id="valor_recebido" onkeyup="calculaTroco()" value="<?= number_format($totalPdv, 2, '.', '') ?>" style="height: 70px; font-size: 50px; text-align: center">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="">Troco</label>
                                    <input type="text" class="form-control" id="troco" value="0.00" disabled="" style="height: 70px; font-size: 50px; text-align: center">
                                </div>
                            </div>
                            <!-- <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="">Desconto</label>
                                    <input type="text" class="form-control" id="desconto" onkeyup="calculaDescontoGeral()" value="0">
                                </div>
                            </div> -->
                            <input type="hidden" class="form-control" id="desconto" onkeyup="calculaDescontoGeral()" value="0.00">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Forma de Pagamento</label>
                                    <select class="form-control select2" id="forma_de_pagamento" style="width: 100%;">
                                        <?php foreach ($formas_de_pagamento as $forma) : ?>
                                            <option value="<?= $forma['nome'] ?>"><?= $forma['nome'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Vendedor</label>
                                    <select class="form-control select2" id="id_vendedor" style="width: 100%;">
                                        <?php foreach ($vendedores as $vendedor) : ?>
                                            <option value="<?= $vendedor['id_vendedor'] ?>" <?= (int) $vendedor['id_vendedor'] === (int) $id_vendedor_padrao ? 'selected' : '' ?>><?= $vendedor['nome'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select class="form-control select2" id="id_cliente" style="width: 100%;">
                                        <?php foreach ($clientes as $cliente) : ?>
                                            <?php if ($cliente['tipo'] == 1) : ?>
                                                <option value="<?= $cliente['id_cliente'] ?>" <?= (int) $cliente['id_cliente'] === (int) $id_cliente_padrao ? 'selected' : '' ?>><?= $cliente['nome'] ?></option>
                                            <?php else : ?>
                                                <option value="<?= $cliente['id_cliente'] ?>" <?= (int) $cliente['id_cliente'] === (int) $id_cliente_padrao ? 'selected' : '' ?>><?= $cliente['razao_social'] ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" id="btn-finalizar-venda" class="btn btn-primary" onclick="finalizaVenda()"><i class="fas fa-check"></i> Finalizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal LOADING -->
    <div class="modal fade no-print" id="modal-loading">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- <div class="modal-header">
                    <h4 class="modal-title">Valor da Compra: <?= decimal_monetario($totalPdv) ?> | Valor a Pagar: <span id="valor_a_pagar_informativo"><?= decimal_monetario($totalPdv) ?></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> -->
                <div class="modal-body">
                    <img src="<?= base_url('assets/img/loading.gif') ?>" alt="Aguarde carregando.." style="width: 100%">
                </div>
                <!-- <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="finalizaVenda()">Finalizar</button>
                </div> -->
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal DANFCE -->
    <div class="modal fade" id="modal-danfce">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Imprimir Cupom Fiscal - DANFCe</h4>
                    <button type="button" class="close" onclick="location.reload()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <a id="a-danfce" href="" target="_blank" class="btn btn-success">Imprimir Cupom Fiscal - DANFCe</a>
                    <button type="button" class="btn btn-primary" onclick="location.reload()">Fechar</button>
                </div>
                <!-- <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="finalizaVenda()">Finalizar</button>
                </div> -->
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal CUPOM NÃO FISCAL -->
    <div class="modal fade" id="modal-cupom-nao-fiscal">
        <div class="modal-dialog modal-md" style="width: 300px"> <!-- 300px = 80mm -->
            <div class="modal-content">
                <div class="modal-header no-print">
                    <h4 class="modal-title">Cupom não fiscal <button type="button" class="btn btn-success style-action" onclick="print()"><i class="fas fa-print"></i></button></h4>
                    <button type="button" class="close" onclick="location.reload()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="cupom-nao-fiscal"></div>
                </div>
                <div class="modal-footer justify-content-between no-print">
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="location.reload()">Fechar</button>
                    <button type="button" class="btn btn-success" onclick="print()"><i class="fas fa-print"></i> Imprimir Cupom</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="container-fluid no-print" style="margin-top: 15px">
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Pesq. produto</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="/pdv/adicionaProdutoPorCodigoDeBarras/<?= $id_caixa ?>" method="post">
                                    <div class="form-group">
                                        <label for="">Cód. de barras</label>
                                        <input type="text" class="form-control" name="codigo_de_barras" autofocus="">
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Pesq. produto</label>
                                    <select class="form-control select2" id="pesq_de_produto_por_nome" onchange="adicionaProdutoPorNome()" style="width: 100%;">
                                        <option value="" selected="selected">Selecione</option>
                                        <?php if (!empty($produtos)) : ?>
                                            <?php foreach ($produtos as $produto) : ?>
                                                <option value="<?= $produto['id_produto'] ?>"><?= $produto['nome'] ?></option>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <option value="">Nenhum produto cadastrado!</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="">Valor à Pagar</label>
                                    <input type="text" class="form-control" style="height: 65px; text-align: center; font-size: 50px" value="<?= (!empty($valor_a_pagar['valor_final'])) ? number_format($valor_a_pagar['valor_final'], 2, ',', '.') : "R$ 0,00" ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#finalizar-venda" <?= (!empty($valor_a_pagar['valor_final'])) ? "" : "disabled" ?>>Finalizar</button>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-lg-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Produtos</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cód.</th>
                                            <th>Nome</th>
                                            <th>Qtd</th>
                                            <th>Valor Unit.</th>
                                            <th>Subtotal</th>
                                            <th>Desconto</th>
                                            <th>Valor Final</th>
                                            <th style="width: 10px; text-align: center">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($produtos_do_pdv)) : ?>
                                            <?php foreach ($produtos_do_pdv as $produto) : ?>
                                                <tr>
                                                    <td><?= $produto['id_produto'] ?></td>
                                                    <td><?= $produto['nome'] ?></td>
                                                    <td>
                                                        <a href="#" data-toggle="modal" data-target="#alterar-qtd-do-produto" onclick="preparaParaAlterarQtdDoProduto(<?= $produto['id_produto_pdv'] ?>, <?= $produto['quantidade'] ?>)">
                                                            <?= $produto['quantidade'] ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="#" data-toggle="modal" data-target="#alterar-valor-unitario-do-produto" onclick="preparaParaAlterarValoUnitarioDoProduto(<?= $produto['id_produto_pdv'] ?>, <?= $produto['valor_unitario'] ?>)">
                                                            <?= number_format($produto['valor_unitario'], 2, ',', '.') ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?= number_format($produto['subtotal'], 2, ',', '.') ?>
                                                    </td>
                                                    <td>
                                                        <a href="#" data-toggle="modal" data-target="#alterar-desconto-do-produto" onclick="preparaParaAlterarDescontoDoProduto(<?= $produto['id_produto_pdv'] ?>, <?= $produto['desconto'] ?>)">
                                                            <?= number_format($produto['desconto'], 2, ',', '.') ?>
                                                        </a>
                                                    </td>
                                                    <td><?= number_format($produto['valor_final'], 2, ',', '.') ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger style-action" onclick="confirmaAcaoExcluir('Deseja realmente excluir esse produto da venda?', '/pdv/removeProdutoDoPdv/<?= $id_caixa ?>/<?= $produto['id_produto_pdv'] ?>')"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="8">Nenhum produto!</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <!-- <div class="card-footer">
                        Qtd de produtos: 10, Valor final: 110,65.
                    </div> -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>


    <!-- REQUIRED SCRIPTS -->
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
        var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content') || '';
        var csrfTokenValue = $('meta[name="csrf-token-value"]').attr('content') || '';
        var csrfHeaderName = $('meta[name="csrf-header-name"]').attr('content') || 'X-CSRF-TOKEN';

        function aplicaTokenCsrf(form) {
            if (!csrfTokenName || !csrfTokenValue || !form) {
                return;
            }

            var $form = $(form);
            var method = ($form.attr('method') || 'get').toLowerCase();

            if (method !== 'post' || $form.find('input[name="' + csrfTokenName + '"]').length) {
                return;
            }

            $('<input>', {
                type: 'hidden',
                name: csrfTokenName,
                value: csrfTokenValue
            }).appendTo($form);
        }

        function enviaPostComCsrf(rota) {
            var form = document.createElement('form');
            form.method = 'post';
            form.action = rota;
            form.style.display = 'none';
            document.body.appendChild(form);
            aplicaTokenCsrf(form);
            form.submit();
        }

        $(document).on('submit', 'form', function() {
            aplicaTokenCsrf(this);
        });

        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                var method = (settings.type || settings.method || 'GET').toUpperCase();
                var safeMethods = ['GET', 'HEAD', 'OPTIONS', 'TRACE'];

                if (csrfHeaderName && csrfTokenValue && safeMethods.indexOf(method) === -1) {
                    xhr.setRequestHeader(csrfHeaderName, csrfTokenValue);
                }
            }
        });

        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()
            $('.select2-1').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            atualizaRelogioPdv();
            window.setInterval(atualizaRelogioPdv, 1000);

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
                <?php if ($alert == "success_delete") : ?>
                    Toast.fire({
                        type: 'success',
                        title: 'Produto removido com sucesso!'
                    })
                <?php elseif ($alert == "success_venda") : ?>
                    Toast.fire({
                        type: 'success',
                        title: 'Venda realizada com sucesso!'
                    })
                <?php elseif ($alert == "success_edit_qtd") : ?>
                    Toast.fire({
                        type: 'success',
                        title: 'Quantidade alterada com sucesso!'
                    })
                <?php elseif ($alert == "success_edit_valor_unitario") : ?>
                    Toast.fire({
                        type: 'success',
                        title: 'Valor Unitário alterado com sucesso!'
                    })
                <?php elseif ($alert == "success_edit_desconto") : ?>
                    Toast.fire({
                        type: 'success',
                        title: 'Desconto alterado com sucesso!'
                    })
                <?php elseif ($alert == "error_produto_nao_encontrado") : ?>
                    Toast.fire({
                        type: 'error',
                        title: 'Produto nao encontrado!'
                    })
                <?php elseif ($alert == "error_operacao") : ?>
                    Toast.fire({
                        type: 'error',
                        title: 'Nao foi possivel concluir a operacao!'
                    })
                <?php endif; ?>
            <?php endif; ?>
        });

        /**
         * Atualiza relogio pdv.
         */
        function atualizaRelogioPdv() {
            var agora = new Date();
            var horario = agora.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: <?= json_encode($fusoHorarioPdv) ?>
            });
            var data = agora.toLocaleDateString('pt-BR', {
                timeZone: <?= json_encode($fusoHorarioPdv) ?>
            });

            document.getElementById('pdv-horario-atual').textContent = horario;
            document.getElementById('pdv-data-atual').textContent = data;
        }

        /**
         * Controla a interacao de troca virgura por ponto na interface.
         */
        function trocaVirguraPorPonto(id) {
            var valor = document.getElementById(id).value;
            document.getElementById(id).value = valor.replace(',', '.')
        }

        /**
         * Controla a interacao de confirma acao excluir na interface.
         */
        function confirmaAcaoExcluir(msg, rota) {
            if (confirm(msg)) {
                enviaPostComCsrf(rota);
            }
        }

        /**
         * Adiciona produto por nome.
         */
        function adicionaProdutoPorNome() {
            var id_produto = document.getElementById('pesq_de_produto_por_nome').value;
            enviaPostComCsrf("/pdv/adicionaProdutoPorNome/<?= $id_caixa ?>/" + encodeURIComponent(id_produto));
        }

        /**
         * Calcula troco.
         */
        function calculaTroco() {
            trocaVirguraPorPonto('valor_recebido'); // Troca a virgula pelo ponto se ouver

            var valor_recebido = numeroMonetario(document.getElementById('valor_recebido').value);
            var valor_a_pagar = numeroMonetario(document.getElementById('valor_a_pagar_informativo').textContent);

            document.getElementById('troco').value = decimalMonetario(valor_recebido - valor_a_pagar);
        }

        /**
         * Calcula desconto geral.
         */
        function calculaDescontoGeral() {
            var desconto, valor_a_pagar;

            trocaVirguraPorPonto('desconto'); // Troca a virgula por ponto se tiver

            desconto = numeroMonetario(document.getElementById('desconto').value);
            valor_a_pagar = <?= json_encode((float) $totalPdv) ?>;

            document.getElementById('valor_a_pagar_informativo').textContent = decimalMonetario(valor_a_pagar - desconto);

            // Altera o troco
            calculaTroco();
        }

        /**
         * Prepara para alterar qtd do produto.
         */
        function preparaParaAlterarQtdDoProduto(id_produto_pdv, quantidade) {
            document.getElementById('altera_qtd_do_produto_quantidade').value = quantidade;
            document.getElementById('altera_qtd_do_produto_id_pdv_produto').value = id_produto_pdv;
        }

        /**
         * Prepara para alterar valo unitario do produto.
         */
        function preparaParaAlterarValoUnitarioDoProduto(id_produto_pdv, valor_unitario) {
            document.getElementById('altera_valor_unitario_do_produto_valor_unitario').value = decimalMonetario(valor_unitario);
            document.getElementById('altera_valor_unitario_do_produto_id_pdv_produto').value = id_produto_pdv;
        }

        /**
         * Prepara para alterar desconto do produto.
         */
        function preparaParaAlterarDescontoDoProduto(id_produto_pdv, desconto) {
            document.getElementById('altera_desconto_do_produto_valor_unitario').value = decimalMonetario(desconto);
            document.getElementById('altera_desconto_do_produto_id_pdv_produto').value = id_produto_pdv;
        }

        /**
         * Finaliza venda.
         */
        function finalizaVenda() {
            var valor_a_pagar, desconto, valor_recebido, troco, forma_de_pagamento, id_cliente, id_vendedor, btn_finalizar;

            valor_a_pagar = decimalMonetario(<?= json_encode((float) $totalPdv) ?>);
            desconto = decimalMonetario(document.getElementById('desconto').value);
            valor_recebido = decimalMonetario(document.getElementById('valor_recebido').value);
            troco = decimalMonetario(document.getElementById('troco').value);
            forma_de_pagamento = document.getElementById('forma_de_pagamento').value;
            id_cliente = document.getElementById('id_cliente').value;
            id_vendedor = document.getElementById('id_vendedor').value;
            btn_finalizar = document.getElementById('btn-finalizar-venda');

            btn_finalizar.disabled = true;

            $('#finalizar-venda').modal('hide');
            $('#modal-loading').modal('show');

            $.ajax({
                url: <?= json_encode($finalizarComNfce ? "/pdv/finalizaVendaEmiteNFCe/$id_caixa" : "/pdv/finalizaVenda/$id_caixa") ?>,
                type: 'POST',
                dataType: <?= json_encode($finalizarComNfce ? 'json' : 'html') ?>,
                data: {
                    valor_a_pagar: valor_a_pagar,
                    desconto: desconto,
                    valor_recebido: valor_recebido,
                    troco: troco,
                    forma_de_pagamento: forma_de_pagamento,
                    id_vendedor: id_vendedor,
                    id_cliente: id_cliente
                }
            }).done(function(data) {
                <?php if ($finalizarComNfce) : ?>
                    if (!data.redirect) {
                        throw new Error('A venda foi registrada, mas o emissor fiscal nao foi localizado.');
                    }

                    enviaPostComCsrf(data.redirect);
                <?php else : ?>
                    $('#modal-loading').modal('hide');
                    document.getElementById('cupom-nao-fiscal').innerHTML = data;
                    $('#modal-cupom-nao-fiscal').modal('show');
                <?php endif; ?>
            }).fail(function(xhr) {
                $('#modal-loading').modal('hide');
                $('#finalizar-venda').modal('show');
                btn_finalizar.disabled = false;

                Swal.fire({
                    type: 'error',
                    title: 'Nao foi possivel finalizar a venda',
                    text: xhr.responseText || 'Confira os dados e tente novamente.'
                });
            });
        }

        $(function() {
            <?php if ($finalizarComNfce) : ?>
                $('#btn-finalizar-venda').attr('title', 'Finalizar com NFC-e');
            <?php else : ?>
                $('#btn-finalizar-venda').attr('title', 'Finalizar com cupom nao fiscal');
            <?php endif; ?>
        });
    </script>
</body>

</html>
