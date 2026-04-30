<?php

namespace App\Controllers;

use App\Models\CaixaModel;

use App\Models\ConfigEmpresaModel;
use App\Models\ClienteModel;
use App\Models\ConfigNFCeModel;
use App\Models\FormaDePagamentoModel;
use App\Models\NFCeModel;
use App\Models\ProdutoDaVendaModel;
use App\Models\ProdutoModel;
use App\Models\ProdutoPdvModel;
use App\Models\VendaModel;
use App\Models\VendedorModel;
use App\Libraries\ThirdPartyComposerLoader;
use CodeIgniter\Controller;

use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;

use stdClass;

class Pdv extends Controller
{
    private $links;

    private $config_empresa_model;
    private $produto_model;
    private $produto_pdv_model;
    private $cliente_model;
    private $venda_model;
    private $produto_da_venda_model;
    private $config_nfce_model;
    private $nfce_model;
    private $forma_de_pagamento_model;
    private $vendedor_model;

    function __construct()
    {
        ThirdPartyComposerLoader::loadWithoutPsrLog(APPPATH . "ThirdParty/sped-nfe/vendor/autoload.php");

        $this->links = [
            'menu' => '2.m',
            'item' => '2.0'
        ];

        $this->config_empresa_model     = new ConfigEmpresaModel();
        $this->produto_model            = new ProdutoModel();
        $this->produto_pdv_model        = new ProdutoPdvModel();
        $this->cliente_model            = new ClienteModel();
        $this->venda_model              = new VendaModel();
        $this->produto_da_venda_model   = new ProdutoDaVendaModel();
        $this->config_nfce_model        = new ConfigNFCeModel();
        $this->nfce_model               = new NFCeModel();
        $this->caixa_model              = new CaixaModel();
        $this->forma_de_pagamento_model = new FormaDePagamentoModel();
        $this->vendedor_model           = new VendedorModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Caixas Abertos',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Caixas Abertos", 'rota'   => "", 'active' => true]
        ];

        $data['caixas'] = $this->caixa_model->where('status', 'Aberto')->findAll();

        echo view('templates/header');
        echo view('pdv/seleciona_caixa', $data);
        echo view('templates/footer');
    }

    public function start($id_caixa)
    {
        $data['id_caixa']            = $id_caixa;
        $data['clientes']            = $this->cliente_model->select('id_cliente, tipo, nome, razao_social')->findAll();
        $data['produtos']            = $this->produto_model->select('id_produto, nome')->findAll();
        $data['produtos_do_pdv']     = $this->produto_pdv_model->where('id_caixa', $id_caixa)->findAll();
        $data['valor_a_pagar']       = $this->produto_pdv_model->selectSum('valor_final')->where('id_caixa', $id_caixa)->first();
        $data['formas_de_pagamento'] = $this->forma_de_pagamento_model->findAll();
        $data['vendedores']          = $this->vendedor_model->findAll();

        echo view('pdv/start', $data);
    }

    public function adicionaProdutoPorCodigoDeBarras($id_caixa)
    {
        $codigo_de_barras = $this->request->getvar('codigo_de_barras');

        $produto = $this->produto_model->select('id_produto, nome, unidade, codigo_de_barras, valor_de_venda, NCM, CSOSN, CFOP')->where('codigo_de_barras', $codigo_de_barras)->first();

        if (empty($produto)) {
            session()->setFlashdata('alert', 'error_produto_nao_encontrado');
            return redirect()->to("/pdv/start/$id_caixa");
        }

        $quantidade     = 1;
        $valor_unitario = $produto['valor_de_venda'];
        $subtotal       = $quantidade * $valor_unitario;
        $desconto       = 0;
        $valor_final    = $subtotal - $desconto;

        $this->produto_pdv_model->save([
            'nome'             => $produto['nome'],
            'unidade'          => $produto['unidade'],
            'codigo_de_barras' => $produto['codigo_de_barras'],
            'quantidade'       => $quantidade,
            'valor_unitario'   => $valor_unitario,
            'subtotal'         => $subtotal,
            'desconto'         => $desconto,
            'valor_final'      => $valor_final,
            'NCM'              => $produto['NCM'],
            'CSOSN'            => $produto['CSOSN'],
            'CFOP'             => $produto['CFOP'],
            'id_produto'       => $produto['id_produto'],
            'id_caixa'         => $id_caixa
        ]);

        return redirect()->to("/pdv/start/$id_caixa");
    }

    public function adicionaProdutoPorNome($id_caixa, $id_produto)
    {
        $produto = $this->produto_model->select('id_produto, nome, unidade, codigo_de_barras, valor_de_venda, NCM, CSOSN, CFOP')->where('id_produto', $id_produto)->first();

        if (empty($produto)) {
            session()->setFlashdata('alert', 'error_produto_nao_encontrado');
            return redirect()->to("/pdv/start/$id_caixa");
        }

        $quantidade     = 1;
        $valor_unitario = $produto['valor_de_venda'];
        $subtotal       = $quantidade * $valor_unitario;
        $desconto       = 0;
        $valor_final    = $subtotal - $desconto;

        $this->produto_pdv_model->save([
            'nome'             => $produto['nome'],
            'unidade'          => $produto['unidade'],
            'codigo_de_barras' => $produto['codigo_de_barras'],
            'quantidade'       => $quantidade,
            'valor_unitario'   => $valor_unitario,
            'subtotal'         => $subtotal,
            'desconto'         => $desconto,
            'valor_final'      => $valor_final,
            'NCM'              => $produto['NCM'],
            'CSOSN'            => $produto['CSOSN'],
            'CFOP'             => $produto['CFOP'],
            'id_produto'       => $produto['id_produto'],
            'id_caixa'         => $id_caixa
        ]);

        return redirect()->to("/pdv/start/$id_caixa");
    }

    public function removeProdutoDoPdv($id_caixa, $id_produto_pdv)
    {
        $this->produto_pdv_model->where('id_caixa', $id_caixa)->where('id_produto_pdv', $id_produto_pdv)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to("/pdv/start/$id_caixa");
    }

    public function alteraQtdDoProduto($id_caixa)
    {
        $id_produto_pdv = $this->request->getvar('id_produto_pdv');
        
        $produto = $this->produto_pdv_model->where('id_caixa', $id_caixa)->where('id_produto_pdv', $id_produto_pdv)->first();

        if (empty($produto)) {
            session()->setFlashdata('alert', 'error_operacao');
            return redirect()->to("/pdv/start/$id_caixa");
        }

        // Prepara os dados para alterar
        $dados = $this->request->getvar();
        $dados['subtotal'] = ($dados['quantidade'] * $produto['valor_unitario']);
        $dados['valor_final'] = (($dados['quantidade'] * $produto['valor_unitario']) - $produto['desconto']);

        // Atualiza com os novos dados
        $dados['id_caixa'] = $id_caixa;
        $this->produto_pdv_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit_qtd');

        return redirect()->to("/pdv/start/$id_caixa");
    }

    public function alteraValorUnitarioDoProduto($id_caixa)
    {
        $id_produto_pdv = $this->request->getvar('id_produto_pdv');
        $produto = $this->produto_pdv_model->where('id_caixa', $id_caixa)->where('id_produto_pdv', $id_produto_pdv)->first();

        if (empty($produto)) {
            session()->setFlashdata('alert', 'error_operacao');
            return redirect()->to("/pdv/start/$id_caixa");
        }

        // Prepara os dados para alterar
        $dados = $this->request->getvar();
        $dados['subtotal'] = ($produto['quantidade'] * $dados['valor_unitario']);
        $dados['valor_final'] = (($produto['quantidade'] * $dados['valor_unitario']) - $produto['desconto']);

        // Atualiza com os novos dados
        $dados['id_caixa'] = $id_caixa;
        $this->produto_pdv_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit_valor_unitario');

        return redirect()->to("/pdv/start/$id_caixa");
    }

    public function alteraDescontoDoProduto($id_caixa)
    {
        $id_produto_pdv = $this->request->getvar('id_produto_pdv');
        $produto = $this->produto_pdv_model->where('id_caixa', $id_caixa)->where('id_produto_pdv', $id_produto_pdv)->first();

        if (empty($produto)) {
            session()->setFlashdata('alert', 'error_operacao');
            return redirect()->to("/pdv/start/$id_caixa");
        }

        // Prepara os dados para alterar
        $dados = $this->request->getvar();
        $dados['valor_final'] = (($produto['quantidade'] * $produto['valor_unitario']) - $dados['desconto']);

        // Atualiza com os novos dados
        $dados['id_caixa'] = $id_caixa;
        $this->produto_pdv_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit_desconto');

        return redirect()->to("/pdv/start/$id_caixa");
    }

    public function format($valor)
    {
        return number_format($valor, 2, '.', '');
    }

    private function normalizaValor($valor, $padrao = 0)
    {
        if ($valor === null || $valor === '') {
            return (float) $padrao;
        }

        $valor = preg_replace('/[^0-9,.\-]/', '', (string) $valor);

        if (strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
            $valor = str_replace('.', '', $valor);
        }

        $valor = str_replace(',', '.', $valor);

        return is_numeric($valor) ? (float) $valor : (float) $padrao;
    }

    private function codigoFiscalFormaPagamento($nome)
    {
        $nome = trim((string) $nome);

        if ($nome !== '') {
            $forma = $this->forma_de_pagamento_model->where('nome', $nome)->first();
            $codigo = $forma['codigo_nfce'] ?? null;

            if (preg_match('/^\d{2}$/', (string) $codigo)) {
                return $codigo;
            }
        }

        $normalizado = strtolower($nome);
        $normalizado = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalizado);
        $normalizado = $normalizado !== false ? $normalizado : strtolower($nome);

        if (strpos($normalizado, 'dinheiro') !== false) {
            return '01';
        }

        if (strpos($normalizado, 'credito') !== false || strpos($normalizado, 'credit') !== false) {
            return '03';
        }

        if (strpos($normalizado, 'debito') !== false || strpos($normalizado, 'debit') !== false) {
            return '04';
        }

        if (strpos($normalizado, 'cheque') !== false) {
            return '02';
        }

        if (strpos($normalizado, 'boleto') !== false) {
            return '15';
        }

        if (strpos($normalizado, 'transfer') !== false) {
            return '18';
        }

        if (strpos($normalizado, 'deposit') !== false) {
            return '16';
        }

        if (strpos($normalizado, 'vale aliment') !== false) {
            return '10';
        }

        if (strpos($normalizado, 'vale refei') !== false) {
            return '11';
        }

        if (strpos($normalizado, 'vale presente') !== false) {
            return '12';
        }

        if (strpos($normalizado, 'vale combust') !== false) {
            return '13';
        }

        return '99';
    }

    private function csosnProduto(array $produto)
    {
        $csosn = preg_replace('/\D/', '', (string) ($produto['CSOSN'] ?? ''));

        return preg_match('/^\d{3}$/', $csosn) ? $csosn : '102';
    }

    private function formataMoeda($valor)
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }

    private function escapaCupom($valor)
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

    private function nomeClienteParaCupom($cliente)
    {
        if (empty($cliente)) {
            return 'Consumidor';
        }

        return !empty($cliente['nome']) ? $cliente['nome'] : ($cliente['razao_social'] ?? 'Consumidor');
    }

    private function montaCupomNaoFiscal($empresa, $cliente, $vendedor, $produtos, $venda, $id_venda)
    {
        $linhas = '';

        foreach ($produtos as $produto) {
            $subtotal = $this->normalizaValor($produto['quantidade']) * $this->normalizaValor($produto['valor_unitario']);

            $linhas .= "
                <tr>
                    <td>{$this->escapaCupom($produto['id_produto'])}</td>
                    <td>{$this->escapaCupom($produto['nome'])}</td>
                    <td>{$this->escapaCupom($produto['quantidade'])} x {$this->formataMoeda($produto['valor_unitario'])}</td>
                    <td>{$this->formataMoeda($subtotal)}</td>
                </tr>
            ";
        }

        $nomeCliente = $this->nomeClienteParaCupom($cliente);
        $nomeVendedor = !empty($vendedor['nome']) ? $vendedor['nome'] : 'Nao informado';
        $data = date('d/m/Y', strtotime($venda['data']));
        $hora = date('H:i', strtotime($venda['hora']));

        return "
            <p style='text-align: center'>
                <b>{$this->escapaCupom($empresa['nome_fantasia'] ?? '')}</b><br>
                {$this->escapaCupom($empresa['razao_social'] ?? '')}<br>
                {$this->escapaCupom($empresa['endereco'] ?? '')}<br>
                {$this->escapaCupom($empresa['telefone'] ?? '')}
            </p>

            <p style='text-align: center; font-weight: bold; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 6px 0'>
                CUPOM NAO FISCAL<br>
                NAO E DOCUMENTO FISCAL
            </p>

            <p>
                <b>CNPJ:</b> {$this->escapaCupom($empresa['cnpj'] ?? '')}<br>
                <b>Cliente:</b> {$this->escapaCupom($nomeCliente)}<br>
                {$data} as {$hora} - <b>No {$this->escapaCupom($id_venda)}</b>
            </p>

            <hr>

            <table width='100%'>
                <thead>
                    <tr>
                        <th>Cod.</th>
                        <th>Desc.</th>
                        <th>Qtd X Unit.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$linhas}
                </tbody>
            </table>

            <hr>

            <p>
                <b>Total:</b> {$this->formataMoeda($venda['valor_a_pagar'])}<br>
                <b>Recebido:</b> {$this->formataMoeda($venda['valor_recebido'])}<br>
                <b>Troco:</b> {$this->formataMoeda($venda['troco'])}<br>
                <b>Forma de PGTO:</b> {$this->escapaCupom($venda['forma_de_pagamento'])}
            </p>

            <hr>

            <p><b>Vendedor:</b> {$this->escapaCupom($nomeVendedor)}</p>

            <hr>

            <p style='text-align: center'>
                ____________________________
                <br>
                Assinatura do Cliente
            </p>
        ";
    }

    public function finalizaVenda($id_caixa)
    {
        $db = \Config\Database::connect();
        $dados = $this->request->getVar();
        $produtos_do_pdv = $this->produto_pdv_model->where('id_caixa', $id_caixa)->findAll();
        $transacao_iniciada = false;

        try {
            if (empty($produtos_do_pdv)) {
                throw new \RuntimeException('Nenhum produto foi encontrado no caixa atual.');
            }

            $cliente = $this->cliente_model->where('id_cliente', $dados['id_cliente'] ?? null)->first();
            if (empty($cliente)) {
                throw new \RuntimeException('Cliente invalido para finalizar a venda.');
            }

            $vendedor = $this->vendedor_model->where('id_vendedor', $dados['id_vendedor'] ?? null)->first();
            if (empty($vendedor)) {
                throw new \RuntimeException('Vendedor invalido para finalizar a venda.');
            }

            $desconto = $this->normalizaValor($dados['desconto'] ?? 0);
            $total_dos_itens = 0;

            foreach ($produtos_do_pdv as $produto) {
                $total_dos_itens += $this->normalizaValor($produto['valor_final']);
            }

            $valor_a_pagar = max(0, $total_dos_itens - $desconto);
            $valor_recebido = $this->normalizaValor($dados['valor_recebido'] ?? '', $valor_a_pagar);
            $troco = max(0, $valor_recebido - $valor_a_pagar);

            if ($valor_recebido < $valor_a_pagar) {
                throw new \RuntimeException('Valor recebido menor que o valor a pagar.');
            }

            $venda = [
                'valor_a_pagar' => $valor_a_pagar,
                'desconto' => $desconto,
                'valor_recebido' => $valor_recebido,
                'troco' => $troco,
                'forma_de_pagamento' => $dados['forma_de_pagamento'] ?? '',
                'data' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'id_cliente' => $cliente['id_cliente'],
                'id_vendedor' => $vendedor['id_vendedor'],
                'id_caixa' => $id_caixa
            ];

            $db->transBegin();
            $transacao_iniciada = true;

            $id_venda = $this->venda_model->insert($venda);
            if (empty($id_venda)) {
                throw new \RuntimeException('Nao foi possivel registrar a venda.');
            }

            foreach ($produtos_do_pdv as $produto) {
                $produto_do_estoque = $this->produto_model->where('id_produto', $produto['id_produto'])->first();
                if (empty($produto_do_estoque)) {
                    throw new \RuntimeException("Produto {$produto['id_produto']} nao encontrado no estoque.");
                }

                $quantidade = $this->normalizaValor($produto['quantidade']);
                if ($quantidade <= 0) {
                    throw new \RuntimeException("Quantidade invalida para o produto {$produto['nome']}.");
                }

                if ($this->normalizaValor($produto_do_estoque['quantidade']) < $quantidade) {
                    throw new \RuntimeException("Estoque insuficiente para o produto {$produto['nome']}.");
                }

                $item_da_venda = [
                    'nome' => $produto['nome'],
                    'unidade' => $produto['unidade'],
                    'codigo_de_barras' => $produto['codigo_de_barras'],
                    'quantidade' => $quantidade,
                    'valor_unitario' => $this->normalizaValor($produto['valor_unitario']),
                    'subtotal' => $this->normalizaValor($produto['subtotal']),
                    'desconto' => $this->normalizaValor($produto['desconto']),
                    'valor_final' => $this->normalizaValor($produto['valor_final']),
                    'NCM' => $produto['NCM'],
                    'CSOSN' => $produto['CSOSN'],
                    'CFOP' => $produto['CFOP'],
                    'id_venda' => $id_venda,
                    'id_produto' => $produto['id_produto']
                ];

                if (!$this->produto_da_venda_model->insert($item_da_venda)) {
                    throw new \RuntimeException("Nao foi possivel registrar o produto {$produto['nome']} na venda.");
                }

                $nova_qtd = $this->normalizaValor($produto_do_estoque['quantidade']) - $quantidade;
                if (!$this->produto_model->set('quantidade', $nova_qtd)->where('id_produto', $produto['id_produto'])->update()) {
                    throw new \RuntimeException("Nao foi possivel atualizar o estoque do produto {$produto['nome']}.");
                }
            }

            $this->produto_pdv_model->where('id_caixa', $id_caixa)->delete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Nao foi possivel concluir a venda.');
            }

            $db->transCommit();
            $transacao_iniciada = false;

            session()->setFlashdata('alert', 'success_venda');

            $empresa = $this->config_empresa_model->where('id_config', 1)->first();
            $venda['id_venda'] = $id_venda;
            $cupom = $this->montaCupomNaoFiscal($empresa, $cliente, $vendedor, $produtos_do_pdv, $venda, $id_venda);

            return $this->response->setBody($cupom);
        } catch (\Throwable $e) {
            if ($transacao_iniciada) {
                $db->transRollback();
            }

            return $this->response->setStatusCode(400)->setBody($e->getMessage());
        }
    }

    private function finalizaVendaLegado($id_caixa)
    {
        $dados = $this->request->getvar();

        $dados['data']     = date('Y-m-d');
        $dados['hora']     = date('H:i:s');
        $dados['id_caixa'] = $id_caixa;

        $id_venda = $this->venda_model->insert($dados);

        $produtos_do_pdv = $this->produto_pdv_model->findAll();

        $produtos_para_o_cupom_nao_fiscal = "";

        foreach ($produtos_do_pdv as $produto) {
            $produto['id_venda'] = $id_venda;
			
			// Decrementa da quantidade do estoque a quantidade do produto vendido
            $produto_do_estoque = $this->produto_model->where('id_produto', $produto['id_produto'])->first();
            $nova_qtd = $produto_do_estoque['quantidade'] - $produto['quantidade'];

            $this->produto_model->set('quantidade', $nova_qtd)->where('id_produto', $produto['id_produto'])->update();
			
            $this->produto_da_venda_model->insert($produto);

            // Guarda os dados dos produtos em uma variável para inserir no cupom
            $subtotal = $produto['quantidade'] * $produto['valor_unitario'];

            $produtos_para_o_cupom_nao_fiscal .= "
                <tr>
                    <td>{$produto['id_produto']}</td>
                    <td>{$produto['nome']}</td>
                    <td>{$produto['quantidade']} x {$produto['valor_unitario']}</td>
                    <td>{$subtotal}</td>
                </tr>
            ";
        }

        // Remove todos os registros da tabela produtos_do_pdv.
        $this->produto_pdv_model->emptyTable('produtos_do_pdv');

        $session = session();
        $session->setFlashdata('alert', 'success_venda');


        // ---------------------------------- MONTAGEM DO CUPOM NÃO FISCAL ------------------------------------------- //
        $empresa  = $this->config_empresa_model->where('id_config', 1)->first();
        $cliente  = $this->cliente_model->where('id_cliente', $dados['id_cliente'])->first();
        $vendedor = $this->vendedor_model->where('id_vendedor', $dados['id_vendedor'])->first();
        $data     = date('d/m/Y');
        $hora     = date('H:i');

        echo "
            <p style='text-align: center'>
                <b>{$empresa['nome_fantasia']}</b><br>
                {$empresa['razao_social']}<br>
                {$empresa['endereco']}<br>
                {$empresa['telefone']}
            </p>

            <p>
                <b>CNPJ:</b> {$empresa['cnpj']}<br>
                <b>Cliente:</b> {$cliente['nome']}<br>
                {$data} às {$hora} - <b>Nº {$id_venda}</b>
            </p>

            <hr>

            <table width='100%'>
                <thead>
                    <tr>
                        <th>Cód.</th>
                        <th>Desc.</th>
                        <th>Qtd X Unit.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$produtos_para_o_cupom_nao_fiscal}
                </tbody>
            </table>

            <hr>

            <p>
                <b>Total:</b>    {$dados['valor_a_pagar']}<br>
                <b>Recebido:</b> {$dados['valor_recebido']}<br>
                <b>Troco:</b>    {$dados['troco']}<br>
                <b>Forma de PGTO:</b> {$dados['forma_de_pagamento']}
            </p>
            
            <hr>

            <p><b>Vendedor:</b> {$vendedor['nome']}</p>

            <hr>

            <p style='text-align: center'>
                ____________________________
                <br>
                Assinatura do Cliente
            </p>
        ";
    }

    public function emiteNFCe($id_venda, $tipo) // Tipo=1 então emitir pelo PDV, Tipo=2 então emitir pelo hist. de vendas 
    {
        $dados_da_venda = $this->venda_model->where('id_venda', $id_venda)->first(); // Dados da Venda
        $dados          = $this->config_nfce_model->where('id_config', 1)->first(); // Dados da Config. da NFCe

        if (empty($dados_da_venda) || empty($dados)) {
            session()->setFlashdata('alert', 'erro_emissao_nfce');
            return redirect()->to("/vendas/show/$id_venda");
        }

        $nfe = new Make();
        // ----------- Tag INFORMAÇÕES ------------- //
        $inf           = new stdClass();
        $inf->versao   = '4.00'; //versão do layout (string)
        $inf->Id       = null; //se o Id de 44 digitos não for passado será gerado automaticamente
        $inf->pk_nItem = null; //deixe essa variavel sempre como NULL
        $nfe->taginfNFe($inf);


        // ----------- Tag IDE ------------- //
        $ide           = new stdClass();
        $ide->cUF      = $dados['cUF'];
        $ide->cNF      = rand(1, 99999999);
        $ide->natOp    = $dados['natOp'];
        // $ide->indPag   = 0; //NÃO EXISTE MAIS NA VERSÃO 4.00
        $ide->mod      = 65;
        $ide->serie    = $dados['serie'];
        $ide->nNF      = $dados['nNF'];
        $ide->dhEmi    = date('Y-m-d\TH:i:sP');
        $ide->dhSaiEnt = null;
        $ide->tpNF     = 1;
        $ide->idDest   = 1;
        $ide->cMunFG   = $dados['cMunFG'];
        $ide->tpImp    = 4;
        $ide->tpEmis   = 1;
        $ide->cDV      = 0;
        $ide->tpAmb    = $dados['tpAmb'];
        $ide->finNFe   = 1;
        $ide->indFinal = 1;
        $ide->indPres  = 1;
        $ide->procEmi  = 0;
        $ide->verProc  = $dados['verProc'];
        $ide->dhCont   = null;
        $ide->xJust    = null;
        $nfe->tagide($ide);


        // ----------- Tag EMITENTE ------------- //
        // -- Emitente -- //
        $emitente         = new stdClass();
        $emitente->CNPJ  = $dados['CNPJ'];
        $emitente->xNome = $dados['xNome'];
        $emitente->xFant = $dados['xFant'];
        $emitente->IE    = $dados['IE'];
        $emitente->CRT   = $dados['CRT'];
        $nfe->tagemit($emitente);

        // -- Endereço do emitente -- //
        $endereco_do_emitente          = new stdClass();
        $endereco_do_emitente->xLgr    = $dados['xLgr'];
        $endereco_do_emitente->nro     = $dados['nro'];
        $endereco_do_emitente->xCpl    = $dados['xCpl'];
        $endereco_do_emitente->xBairro = $dados['xBairro'];
        $endereco_do_emitente->cMun    = $dados['cMun'];
        $endereco_do_emitente->xMun    = $dados['xMun'];
        $endereco_do_emitente->UF      = $dados['UF'];
        $endereco_do_emitente->CEP     = $dados['CEP'];
        $endereco_do_emitente->cPais   = $dados['cPais'];
        $endereco_do_emitente->xPais   = $dados['xPais'];
        $endereco_do_emitente->fone    = $dados['fone'];
        $nfe->tagenderEmit($endereco_do_emitente);

        // // ----------- Tag DESTINATÁRIO ------------- //
        // // -- Destinatário -- //
        // $destinatario = new stdClass();
        // $destinatario->CNPJ      = '01172466001451';
        // $destinatario->xNome     = 'PAROQUIA NOSSA SENHORA DAS GRAÇAS';
        // $destinatario->indIEDest = 2;
        // $nfe->tagdest($destinatario);

        // // -- Endereço do destinatário -- //
        // $endereco_do_destinatario = new stdClass();
        // $endereco_do_destinatario->xLgr    = 'PRAÇA JOSE LEITÃO DE OLIVEIRA';
        // $endereco_do_destinatario->nro     = 'S/N';
        // $endereco_do_destinatario->xCpl    = 'CENTRO';
        // $endereco_do_destinatario->xBairro = 'NOVO ACORDO';
        // $endereco_do_destinatario->cMun    = '1715101';
        // $endereco_do_destinatario->xMun    = 'Novo Acordo';
        // $endereco_do_destinatario->UF      = 'TO';
        // $endereco_do_destinatario->CEP     = '77610000';
        // $endereco_do_destinatario->cPais   = '1058';
        // $endereco_do_destinatario->xPais   = 'BRASIL';
        // $nfe->tagenderDest($endereco_do_destinatario);

        if($tipo == 1)
        {
            $produtos_do_pdv = $this->produto_pdv_model->where('id_caixa', $dados_da_venda['id_caixa'] ?? null)->findAll();
        }
        else if($tipo == 2)
        {
            $produtos_do_pdv = $this->produto_da_venda_model->where('id_venda', $id_venda)->findAll();
        }
        else
        {
            $produtos_do_pdv = [];
        }

        if (empty($produtos_do_pdv)) {
            session()->setFlashdata('alert', 'erro_emissao_nfce');
            return redirect()->to("/vendas/show/$id_venda");
        }

        $i = 0;
        foreach ($produtos_do_pdv as $produto) {
            $i += 1;

            // ----------- Tag PRODUTOS ------------- //
            $std_produto         = new \stdClass();
            $std_produto->item   = $i;
            $std_produto->cProd  = $produto['id_produto'];
            $std_produto->cEAN   = "SEM GTIN";
            $std_produto->xProd  = $produto['nome'];
            $std_produto->NCM    = $produto['NCM'];
            $std_produto->CFOP   = $produto['CFOP'];
            $std_produto->uCom   = $produto['unidade'];
            $std_produto->qCom   = $produto['quantidade']; // QUANTIDADE COMPRADA -----------------------------------------------------------
            $std_produto->vUnCom = $this->format($produto['valor_unitario']); // COLOCAR O VALOR UNITÁRIO DO PRODUTO --------------------------------------------------------------

            if($produto['desconto'] != 0) // CASO HAJA DESCONTO NO PRODUTO INSERIDO AUTOMATICAMENTE CONDIÇÃO = DIFENTE DE ZERO
            {
                $std_produto->vDesc  = $this->format($produto['desconto']); // DESCONTO DO PRODUTO
            }

            $subtotal = $this->format($produto['valor_unitario']) * $produto['quantidade'];

            $std_produto->vProd    = $this->format($subtotal); // COLOCAR O VALOR TOTAL QTDxVALOR.UNITARIO --------------------------------------------------------
            $std_produto->cEANTrib = "SEM GTIN";
            $std_produto->uTrib    = $produto['unidade'];
            $std_produto->qTrib    = $produto['quantidade']; // QUANTIDADE A SER TRIBUTADA ----------------------------------------------------------------------------
            $std_produto->vUnTrib  = $this->format($produto['valor_unitario']); // COLOCAR O VALOR DA UNIDADE -----------------------------------------------------------------------
            $std_produto->indTot   = 1; // Indica se o valor do item (vProd) entra no total da NF-e. 0-não compoe, 1 compoe
            $nfe->tagprod($std_produto);

            // -- Tag imposto -- //
            $std_imposto       = new \stdClass();
            $std_imposto->item = $i;
            $nfe->tagimposto($std_imposto);

            // -- Tag ICMS -- //
            $std_icms       = new \stdClass();
            $std_icms->item = $i;
            $nfe->tagICMS($std_icms);

            // -- Tag ICMSSN -- //
            $std_icmssm                  = new stdClass();
            $std_icmssm->item            = $i; //item da NFe
            $std_icmssm->orig            = 0;
            $std_icmssm->CSOSN           = $this->csosnProduto($produto);
            $std_icmssm->pCredSN         = '0.00';
            $std_icmssm->vCredICMSSN     = '0.00';
            $std_icmssm->modBCST         = null;
            $std_icmssm->pMVAST          = null;
            $std_icmssm->pRedBCST        = null;
            $std_icmssm->vBCST           = null;
            $std_icmssm->pICMSST         = null;
            $std_icmssm->vICMSST         = null;
            $std_icmssm->vBCFCPST        = null; //incluso no layout 4.00
            $std_icmssm->pFCPST          = null; //incluso no layout 4.00
            $std_icmssm->vFCPST          = null; //incluso no layout 4.00
            $std_icmssm->vBCSTRet        = null;
            $std_icmssm->pST             = null;
            $std_icmssm->vICMSSTRet      = null;
            $std_icmssm->vBCFCPSTRet     = null; //incluso no layout 4.00
            $std_icmssm->pFCPSTRet       = null; //incluso no layout 4.00
            $std_icmssm->vFCPSTRet       = null; //incluso no layout 4.00
            $std_icmssm->modBC           = null;
            $std_icmssm->vBC             = null;
            $std_icmssm->pRedBC          = null;
            $std_icmssm->pICMS           = null;
            $std_icmssm->vICMS           = null;
            $std_icmssm->pRedBCEfet      = null;
            $std_icmssm->vBCEfet         = null;
            $std_icmssm->pICMSEfet       = null;
            $std_icmssm->vICMSEfet       = null;
            $std_icmssm->vICMSSubstituto = null;
            $nfe->tagICMSSN($std_icmssm);

            // -- Tag PIS -- //
            $std_pis = new \stdClass();
            $std_pis->item = $i;
            $std_pis->CST  = '01';
            $std_pis->vBC  = '0.00';
            $std_pis->pPIS = '0.00';
            $std_pis->vPIS = '0.00';
            $nfe->tagPIS($std_pis);

            // -- COFINS -- //
            $std_cofins             = new \stdClass();
            $std_cofins->item       = $i;
            $std_cofins->CST        = '01';
            $std_cofins->vBC        = '0.00';
            $std_cofins->pCOFINS    = '0.0000';
            $std_cofins->vCOFINS    = '00.0';
            // $std_cofins->qBCProd = 0;
            $std_cofins->vAliqProd  = 0;
            $nfe->tagCOFINS($std_cofins);
        }

        // ----------- Tag ICMS TOTAL ------------- //
        $icms_total             = new stdClass();
        $icms_total->vBC        = null;
        $icms_total->vICMS      = null;
        $icms_total->vICMSDeson = null;
        $icms_total->vFCP       = null;
        $icms_total->vBCST      = null;
        $icms_total->vST        = null;
        $icms_total->vFCPST     = null;
        $icms_total->vFCPSTRet  = null;
        $icms_total->vProd      = null;
        $icms_total->vFrete     = null;
        $icms_total->vSeg       = null;
        $icms_total->vDesc      = null;
        $icms_total->vII        = null;
        $icms_total->vIPI       = null;
        $icms_total->vIPIDevol  = null;
        $icms_total->vPIS       = null;
        $icms_total->vCOFINS    = null;
        $icms_total->vOutro     = null;
        $icms_total->vNF        = null;
        $icms_total->vTotTrib   = null;
        $nfe->tagICMSTot($icms_total);

        // ----------- Tag TRANSPORTE ------------- //
        $transporte           = new stdClass();
        $transporte->modFrete = 9;
        $nfe->tagtransp($transporte);

        // ----------- Tag PAGAMENTO ------------- //
        $valor_a_pagar = $this->normalizaValor($dados_da_venda['valor_a_pagar'] ?? 0);
        $valor_recebido = $this->normalizaValor($dados_da_venda['valor_recebido'] ?? '', $valor_a_pagar);
        $troco = $this->normalizaValor($dados_da_venda['troco'] ?? 0);

        $pagamento         = new stdClass();
        $pagamento->vTroco = $this->format($troco);
        $nfe->tagpag($pagamento);

        // -- Tipo de pagamento -- //
        $tipo_de_pagamento            = new stdClass();
        $tipo_de_pagamento->tPag      = $this->codigoFiscalFormaPagamento($dados_da_venda['forma_de_pagamento'] ?? '');
        $tipo_de_pagamento->vPag      = $this->format(max($valor_recebido, $valor_a_pagar)); //Obs: deve ser informado o valor pago pelo cliente
        $tipo_de_pagamento->indPag    = '0'; //0= Pagamento à Vista 1= Pagamento à Prazo
        $nfe->tagdetPag($tipo_de_pagamento);

        // ----------- Tag RESPONSÁVEL TÉCNICO ------------- // 
        $responsavel_tecnico           = new stdClass();
        $responsavel_tecnico->CNPJ     = $dados['CNPJ_responsavel_tecnico']; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
        $responsavel_tecnico->xContato = $dados['xContato']; //Nome da pessoa a ser contatada
        $responsavel_tecnico->email    = $dados['email_responsavel_tecnico']; //E-mail da pessoa jurídica a ser contatada
        $responsavel_tecnico->fone     = $dados['fone_responsavel_tecnico']; //Telefone da pessoa jurídica/física a ser contatada
        $responsavel_tecnico->CSRT     = ''; //Código de Segurança do Responsável Técnico
        $responsavel_tecnico->idCSRT   = '0'; //Identificador do CSRT
        $nfe->taginfRespTec($responsavel_tecnico);

        $xml   = $nfe->getXML();
        $chave = $nfe->getChave();

        // Dados da NFe emitida
        $dados_da_nfce = [
            'data' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'chave' => $chave,
            'id_venda' => $id_venda,
            'status' => "Não Emitida" // Fica já com status de Não Emitida, caso ter sucesso na emissão altera o status
        ];
        // -------------------- //

        // ------------------------------------------------------------ CONFIG
        $config  = [
            "atualizacao"=>date('Y-m-d h:i:s'),
            "tpAmb"=> intval($dados['tpAmb']),
            "razaosocial" => $dados['xNome'],
            "cnpj" => $dados['CNPJ'], // PRECISA SER VÁLIDO
            "ie" => $dados['IE'], // PRECISA SER VÁLIDO
            "siglaUF" => $dados['UF'],
            "schemes" => "PL_009_V4",
            "versao" => '4.00',
            "tokenIBPT" => "AAAAAAA",
            "CSC" => $dados['CSC'],
            "CSCid" => $dados['CSCid']
        ];

        $configJson = json_encode($config);

        /*---------------------------------------------------------------------------------------------------------------------------------------*/
        $arq_certificado = WRITEPATH . "uploads/certificado_nfce.pfx";
        if (!is_file($arq_certificado)) {
            session()->setFlashdata('alert', 'erro_emissao_nfce');
            return redirect()->to("/vendas/show/$id_venda");
        }

        $certificadoDigital = file_get_contents($arq_certificado);

        $tools = new Tools($configJson, Certificate::readPfx($certificadoDigital, $dados['senha']));
        $tools->model('65'); // Informa que será usado para emissão do NFCe mod 65. Obrigatório pela API SpedNFe

        try {
            $xmlAssinado = $tools->signNFe($xml); // O conteúdo do XML assinado fica armazenado na variável $xmlAssinado
        } catch (\Exception $e) {
            //aqui você trata possíveis exceptions da assinatura
            // $dados_da_nfce['erro'] = $e->getMessage(); // Caso haja erro, guarda no banco de dados
			exit($e->getMessage());
        }

        /*---------------------------------------------------------------------------------------------------------------------------------------*/
        $recibo = null;
        $protocolo = null;

        try {
            $idLote = str_pad(100, 15, '0', STR_PAD_LEFT);
            $resp = $tools->sefazEnviaLote([$xmlAssinado], $idLote, 1);

            $st = new Standardize();
            $std = $st->toStd($resp);

            if ((string) $std->cStat === '104') {
                $protocolo = $resp;
            }
            else if ((string) $std->cStat === '103') {
                $recibo = $std->infRec->nRec;
            }
            else {
                exit("[$std->cStat] $std->xMotivo");
            }
        } catch (\Exception $e) {
            exit($e->getMessage());
        }

        if ($recibo !== null) {
            try {
                $protocolo = $tools->sefazConsultaRecibo($recibo);
            } catch (\Exception $e) {
                exit($e->getMessage());
            }
        }

        /*---------------------------------------------------------------------------------------------------------------------------------------*/
        $request = $xmlAssinado;
        $response = $protocolo;

        try {
            $xmlProtocolado = Complements::toAuthorize($request, $response);
            
            // header('Content-type: text/xml; charset=UTF-8');
            // echo $xmlProtocolado;
            
            $dados_da_nfce['status']    = "Emitida";
            $dados_da_nfce['protocolo'] = $response;
            $dados_da_nfce['xml']       = $xmlProtocolado;

        } catch (\Exception $e) {
            //$dados_da_nfce['erro'] = $e->getMessage(); // Caso haja erro, guarda no banco de dados
			exit($e->getMessage());
        }

        $this->nfce_model->insert($dados_da_nfce); // Guarda os dados da NFe no banco de dados.

        $session = session();        
        if($dados_da_nfce['status'] == "Emitida") // Caso tenha sucesso incrementa +1 em nNF na config. da NFe
        {
            $nNF = $this->config_nfce_model->where('id_config', 1)->first()['nNF'];
            $this->config_nfce_model->set('nNF', $nNF+1)->update();

            // Redireciona de volta para Vendas/show e informa uma mensagem
            $session->setFlashdata('alert', "success_emissao_nfce");
            return redirect()->to("/vendas/show/$id_venda");
        }

        // Redireciona de volta para Vendas/show e informa uma mensagem
        $session->setFlashdata('alert', "erro_emissao_nfce");
        return redirect()->to("/vendas/show/$id_venda");
    }

    public function finalizaVendaEmiteNFCe($id_caixa)
    {
        return $this->response
            ->setStatusCode(409)
            ->setBody('Emissao NFCe direta pelo PDV esta em preparacao. Finalize pelo cupom nao fiscal e use o historico de vendas para testar a emissao fiscal.');
    }
}
