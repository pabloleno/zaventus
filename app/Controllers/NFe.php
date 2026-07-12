<?php

namespace App\Controllers;

// require_once "vendor/autoload.php";

use App\Models\ClienteModel;
use App\Models\ConfigNFeNFCeModel;
use App\Models\FormaDePagamentoModel;
use App\Models\NFeModel;
use App\Models\ProdutoDaVendaModel;
use App\Models\VendaModel;
use App\Libraries\Moeda;
use App\Libraries\SefazFiscalService;
use App\Libraries\ThirdPartyComposerLoader;
use CodeIgniter\Controller;

use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;

use stdClass;

class NFe extends Controller
{
    private $links;

    private $config_nfe_nfce_model;
    private $cliente_model;
    private $venda_model;
    private $produtos_da_venda_model;
    private $nfe_model;
    private $forma_de_pagamento_model;

    private $status_da_nfe = "";
    private $xml_protocolado = "";

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '6.m',
            'item' => '6.0',
            'subItem' => '6.3'
        ];

        ThirdPartyComposerLoader::loadWithoutPsrLog(APPPATH . "ThirdParty/sped-nfe/vendor/autoload.php");

        $this->config_nfe_nfce_model = new ConfigNFeNFCeModel();
        $this->cliente_model = new ClienteModel();
        $this->venda_model = new VendaModel();
        $this->produtos_da_venda_model = new ProdutoDaVendaModel();
        $this->nfe_model = new NFeModel();
        $this->forma_de_pagamento_model = new FormaDePagamentoModel();
    }

    /**
     * Formata o valor recebido conforme a configuracao da aplicacao.
     */
    public function format($valor)
    {
        return Moeda::decimal($valor);
    }

    /**
     * Normaliza valor.
     */
    private function normalizaValor($valor, $padrao = 0)
    {
        if ($valor === null || $valor === '') {
            return (float) $padrao;
        }

        return Moeda::normalizar($valor);
    }

    /**
     * Converte a forma de pagamento no codigo exigido pelo documento fiscal.
     */
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

    /**
     * Normaliza o CSOSN utilizado na emissao fiscal do produto.
     */
    private function csosnProduto(array $produto)
    {
        $csosn = preg_replace('/\D/', '', (string) ($produto['CSOSN'] ?? ''));

        return preg_match('/^\d{3}$/', $csosn) ? $csosn : '102';
    }

    /**
     * Exibe a mensagem retornada pela operacao fiscal.
     */
    public function mostraErro($id_venda, $erro)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Erro NFe',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Dashboard", 'rota' => "/dashboard", 'active' => false],
            ['titulo' => "Venda", 'rota' => "/vendas/show/$id_venda", 'active' => false],
            ['titulo' => "Erro NFe", 'rota'   => "", 'active' => true]
        ];

        $data['erro'] = $erro;
        $data['id_venda'] = $id_venda;

        echo view('templates/header');
        echo view('nfe/erros', $data);
        echo view('templates/footer');
    }

    /**
     * Exibe os campos obrigatorios ausentes na operacao fiscal.
     */
    public function mostraErroCamposObr($id_venda, $erros)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Ops, não foi possível emitir a NFe. Veja os erros!',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Venda", 'rota' => "/vendas/show/$id_venda", 'active' => false],
            ['titulo' => "Erro NFe", 'rota'   => "", 'active' => true]
        ];

        $data['erros'] = $erros;
        $data['id_venda'] = $id_venda;

        echo view('templates/header');
        echo view('nfe/erros_campos_obrigatorio', $data);
        echo view('templates/footer');
    }

    /**
     * Inicia a emissao da NFe para a venda selecionada.
     */
    public function emiteNFe($id_cliente, $id_venda)
    {
        if(!@fsockopen('www.google.com.br', 80, $num, $error, 5)) // Verifica se existe conexao com internet
        {
            $session = session();
            $session->setFlashdata('alert', 'error_sem_conexao_com_a_internet');

            return redirect()->to("/vendas/show/$id_venda");
        }

        $dados = $this->config_nfe_nfce_model->where('id_config', 1)->first();

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
        $ide->mod      = 55;
        $ide->serie    = $dados['serie'];
        $ide->nNF      = $dados['nNF'];
        $ide->dhEmi    = date('Y-m-d\TH:i:sP');
        $ide->dhSaiEnt = date('Y-m-d\TH:i:sP');
        $ide->tpNF     = 1; // Tipo de operação: 0-entrada, 1-saida
        $ide->idDest   = 1;
        $ide->cMunFG   = $dados['cMunFG'];
        $ide->tpImp    = 1;
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
        $emitente        = new stdClass();
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

        // ----------- Tag DESTINATÁRIO ------------- //
        $dados_dest = $this->cliente_model->where('id_cliente', $id_cliente)->first();

        // -- Destinatário -- //
        if($dados_dest['tipo'] == 1) // Caso seja pessoa física
        {
            $destinatario = new stdClass();
            $destinatario->CPF       = $dados_dest['cpf'];
            $destinatario->xNome     = $dados_dest['nome'];
            $destinatario->indIEDest = 2;
        }
        else // Caso seja pessoa juridica
        {
            if($dados_dest['ie'] == 0) // Caso seja isento de imposto de renda
            {
                $destinatario = new stdClass();
                $destinatario->CNPJ      = $dados_dest['cnpj'];
                $destinatario->xNome     = $dados_dest['razao_social'];
                $destinatario->indIEDest = 2;
            }
            else // Caso não seja isento de imposto de renda
            {
                $destinatario = new stdClass();
                $destinatario->CNPJ      = $dados_dest['cnpj'];
                $destinatario->xNome     = $dados_dest['razao_social'];
                $destinatario->IE        = $dados_dest['ie'];
                $destinatario->indIEDest = 1;
            }
        }

        $nfe->tagdest($destinatario);

        // -- Endereço do destinatário -- //
        $endereco_do_destinatario = new stdClass();
        $endereco_do_destinatario->xLgr    = $dados_dest['logradouro'];
        
        if($dados_dest['numero'] == "" || $dados_dest['numero'] == 0)
        {
            $endereco_do_destinatario->nro = "S/N"; 
        }
        else
        {
            $endereco_do_destinatario->nro = $dados_dest['numero'];
        }

        $endereco_do_destinatario->xCpl    = $dados_dest['complemento'];
        $endereco_do_destinatario->xBairro = $dados_dest['bairro'];
        $endereco_do_destinatario->cMun    = $dados_dest['codigo_do_municipio'];
        $endereco_do_destinatario->xMun    = $dados_dest['municipio'];
        $endereco_do_destinatario->UF      = $dados_dest['UF'];
        $endereco_do_destinatario->CEP     = $dados_dest['cep'];
        $endereco_do_destinatario->cPais   = '1058';
        $endereco_do_destinatario->xPais   = 'BRASIL';
        $nfe->tagenderDest($endereco_do_destinatario);


        // ----------------------------------------------------------------------------------------- //
        $produtos = $this->produtos_da_venda_model->where('id_venda', $id_venda)->findAll();

        $i = 0;
        foreach ($produtos as $produto) {
            $i += 1;

            // ----------- Tag PRODUTOS ------------- //
            $std_produto         = new \stdClass();
            $std_produto->item   = $i;
            $std_produto->cProd  = $produto['id_produto'];

            // Verifica e configura o código de barras
            if($produto['codigo_de_barras'] == 0)
            {
                $codigo_de_barras = "SEM GTIN"; // Caso o produto não possua código de barras
            }
            else
            {
                $codigo_de_barras = $produto['codigo_de_barras']; // Caso possua
            }

            $std_produto->cEAN   = $codigo_de_barras;
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
            $std_produto->cEANTrib = $codigo_de_barras;
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


        // ----------------------------------------------------------------------------- //
        $venda = $this->venda_model->where('id_venda', $id_venda)->first();
        // ----------------------------------------------------------------------------- //


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
        $valor_a_pagar = $this->normalizaValor($venda['valor_a_pagar'] ?? 0);
        $valor_recebido = $this->normalizaValor($venda['valor_recebido'] ?? '', $valor_a_pagar);
        $troco = $this->normalizaValor($venda['troco'] ?? 0);

        $pagamento         = new stdClass();
        $pagamento->vTroco = $this->format($troco);
        $nfe->tagpag($pagamento);

        // -- Tipo de pagamento -- //
        $tipo_de_pagamento            = new stdClass();
        $tipo_de_pagamento->tPag      = $this->codigoFiscalFormaPagamento($venda['forma_de_pagamento'] ?? '');
        $tipo_de_pagamento->vPag      = $this->format(max($valor_recebido, $valor_a_pagar)); //Obs: deve ser informado o valor pago pelo cliente
        $tipo_de_pagamento->indPag    = '0'; //0= Pagamento à Vista 1= Pagamento à Prazo
        $nfe->tagdetPag($tipo_de_pagamento);
        
        //$informacoes_adicionais_da_nfe = new stdClass();
        //$informacoes_adicionais_da_nfe->infAdFisco = '';
        //$informacoes_adicionais_da_nfe->infCpl = 'Nota referente a entrega de GLP 13 KG do Mês de Março de 2020.';
        //$nfe->taginfAdic($informacoes_adicionais_da_nfe);

        // ----------- Tag RESPONSÁVEL TÉCNICO ------------- // 
        $responsavel_tecnico           = new stdClass();
        $responsavel_tecnico->CNPJ     = $dados['CNPJ_responsavel_tecnico']; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
        $responsavel_tecnico->xContato = $dados['xContato']; //Nome da pessoa a ser contatada
        $responsavel_tecnico->email    = $dados['email_responsavel_tecnico']; //E-mail da pessoa jurídica a ser contatada
        $responsavel_tecnico->fone     = $dados['fone_responsavel_tecnico']; //Telefone da pessoa jurídica/física a ser contatada
        $responsavel_tecnico->CSRT     = ''; //Código de Segurança do Responsável Técnico
        $responsavel_tecnico->idCSRT   = '0'; //Identificador do CSRT
        $nfe->taginfRespTec($responsavel_tecnico);

        // Verifica se todos os campos foram preenchidos corretamente e depois gera o XML
        try
        {
            $xml = $nfe->getXML();
            $chave = $nfe->getChave();
        }
        catch (\Exception $e)
        {
            $erros = $nfe->getErrors();
            
            $this->mostraErroCamposObr($id_venda, $erros);
            exit();
        }

        // Dados da NFe emitida
        $dados_da_nfe = [
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
            "CSC" => "AD6A9D2E-3F93-437F-BE5B-E8FA800A08F4",
            "CSCid" => "000001"
        ];

        $configJson = json_encode($config);

        /*---------------------------------------------------------------------------------------------------------------------------------------*/
        $arq_certificado = WRITEPATH . "uploads/certificado_nfe.pfx";
        if (!is_file($arq_certificado)) {
            $this->mostraErro($id_venda, 'Certificado digital NFe nao encontrado. Envie o arquivo .pfx em Configuracoes > NFe antes de emitir.');
            exit();
        }

        $certificadoDigital = file_get_contents($arq_certificado);

        $tools = new Tools($configJson, Certificate::readPfx($certificadoDigital, $dados['senha']));
        $tools->model('55');
        try {
            $xmlAssinado = $tools->signNFe($xml); // O conteúdo do XML assinado fica armazenado na variável $xmlAssinado
        } catch (\Exception $e) {
            //aqui você trata possíveis exceptions da assinatura
            // $dados_da_nfe['erro'] = $e->getMessage(); // Caso haja erro, guarda no banco de dados

            $this->mostraErro($id_venda, $e->getMessage());
            exit();
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
            $this->mostraErro($id_venda, $e->getMessage());
            exit();
        }

        if ($recibo !== null) {
            try {
                $protocolo = $tools->sefazConsultaRecibo($recibo);
            } catch (\Exception $e) {
                $this->mostraErro($id_venda, $e->getMessage());
                exit();
            }
        }

        /*---------------------------------------------------------------------------------------------------------------------------------------*/
        $request = $xmlAssinado;
        $response = $protocolo;

        try {
            $xmlProtocolado = Complements::toAuthorize($request, $response);
            
            // header('Content-type: text/xml; charset=UTF-8');
            // echo $xmlProtocolado;
            
            $dados_da_nfe['status']    = "Emitida";
            $dados_da_nfe['protocolo'] = $response;
            $dados_da_nfe['xml']       = $xmlProtocolado;

        } catch (\Exception $e) {
            // $dados_da_nfe['erro'] = $e->getMessage(); // Caso haja erro, guarda no banco de dados
            
            $this->mostraErro($id_venda, $e->getMessage());
            exit();
        }

        $this->nfe_model->insert($dados_da_nfe); // Guarda os dados da NFe no banco de dados.

        $session = session();        
        if($dados_da_nfe['status'] == "Emitida") // Caso tenha sucesso incrementa +1 em nNF na config. da NFe
        {
            $nNF = $this->config_nfe_nfce_model->where('id_config', 1)->first()['nNF'];
            $this->config_nfe_nfce_model->set('nNF', $nNF+1)->update();

            // Redireciona de volta para Vendas/show e informa uma mensagem
            $session->setFlashdata('alert', "success_emissao_nfe");
            return redirect()->to("/vendas/show/$id_venda");
        }

        // Redireciona de volta para Vendas/show e informa uma mensagem
        $session->setFlashdata('alert', "erro_emissao_nfe");
        return redirect()->to("/vendas/show/$id_venda");
    }

    /**
     * Reemite o documento fiscal solicitado e registra o retorno da SEFAZ.
     */
    public function reemitir($id_cliente, $id_venda, $id_nfe)
    {
        // Primeiro exclui a NFe atual que tentou ser emitida e deu erro
        $this->nfe_model->where('id_nfe', $id_nfe)->delete();

        // Depois tenta reemitir a NFe
        $this->emiteNFe($id_cliente, $id_venda);

        return redirect()->to("/vendas/show/$id_venda");
    }

    /**
     * Cancela o documento fiscal solicitado e registra o retorno da SEFAZ.
     */
    public function cancelarLegado()
    {
        // Dados
        $id_nfe = $this->request->getvar('id_nfe');
        $id_venda = $this->request->getvar('id_venda');

        // Dados da NFe
        $nfe = $this->nfe_model->where('id_nfe', $id_nfe)->first();

        // Justificativa para o cancelamento
        $justificativa = $this->request->getvar('justificativa');

        // Pega o numero do protocolo da XML
        $string_1 = explode('<nProt>', $nfe['protocolo']);
        $string_2 = explode('</nProt>', $string_1[1]);

        $num_do_protocolo = $string_2[0];
        // ---------------------------------

        try {

            // Dados da config da NFe
            $dados = $this->config_nfe_nfce_model->where('id_config', 1)->first();

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
                "CSC" => "AD6A9D2E-3F93-437F-BE5B-E8FA800A08F4",
                "CSCid" => "000001"
            ];

            $configJson = json_encode($config);
            // ----------------------------------------------------------------------

            // Certificado
            $arq_certificado = WRITEPATH . "uploads/certificado_nfe.pfx";
            $certificadoDigital = file_get_contents($arq_certificado);
            // -----------

            $certificate = Certificate::readPfx($certificadoDigital, $dados['senha']);
            $tools = new Tools($configJson, $certificate);
            $tools->model('55');

            $chave = $nfe['chave'];
            $xJust = $justificativa;
            $nProt = $num_do_protocolo;

            $response = $tools->sefazCancela($chave, $xJust, $nProt);

            //você pode padronizar os dados de retorno atraves da classe abaixo
            //de forma a facilitar a extração dos dados do XML
            //NOTA: mas lembre-se que esse XML muitas vezes será necessário, 
            //      quando houver a necessidade de protocolos
            $stdCl = new Standardize($response);
            //nesse caso $std irá conter uma representação em stdClass do XML
            $std = $stdCl->toStd();
            //nesse caso o $arr irá conter uma representação em array do XML
            $arr = $stdCl->toArray();
            //nesse caso o $json irá conter uma representação em JSON do XML
            $json = $stdCl->toJson();
            
            // Cria sessão para mostrar os alertas
            $session = session();

            //verifique se o evento foi processado
            if ($std->cStat != 128)
            {
                //houve alguma falha e o evento não foi processado
                //TRATAR
                $session->setFlashdata('alert', 'erro_cancelamento_nfe');
            }
            else
            {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '101' || $cStat == '135' || $cStat == '155')
                {
                    //SUCESSO PROTOCOLAR A SOLICITAÇÂO ANTES DE GUARDAR
                    $xml = Complements::toAuthorize($tools->lastRequest, $response);
                    
                    // Adiciona o XML Protocolado no banco de dados e altera o status
                    $this->nfe_model->save([
                        'id_nfe' => $id_nfe,
                        'xml_protocolado_cancelamento' => $xml,
                        'status' => 'Cancelada'
                    ]);

                    $session->setFlashdata('alert', 'success_cancelamento_nfe');
                }
                else
                {
                    //houve alguma falha no evento 
                    //TRATAR
                    $session->setFlashdata('alert', 'erro_cancelamento_nfe');
                }
            }
            
            // Retorna para a página de vendas com o alerta
            return redirect()->to("/vendas/show/$id_venda");
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
        }
    }

    /**
     * Cancela uma NFe usando a gestao fiscal centralizada.
     */
    public function cancelar()
    {
        $id_nfe = (int) $this->request->getvar('id_nfe');
        $id_venda = (int) $this->request->getvar('id_venda');
        $justificativa = (string) $this->request->getvar('justificativa');
        $session = session();

        try {
            $resultado = (new SefazFiscalService())->cancelarDocumento(SefazFiscalService::MODELO_NFE, $id_nfe, $justificativa);
            $session->setFlashdata('alert', $resultado['sucesso'] ? 'success_cancelamento_nfe' : 'erro_cancelamento_nfe');
            $session->setFlashdata('fiscal_message', trim((string) ($resultado['cstat'] ?? '') . ' ' . (string) ($resultado['xmotivo'] ?? '')));
        } catch (\Throwable $exception) {
            $session->setFlashdata('alert', 'erro_cancelamento_nfe');
            $session->setFlashdata('fiscal_message', $exception->getMessage());
        }

        return redirect()->to("/vendas/show/$id_venda");
    }
}
