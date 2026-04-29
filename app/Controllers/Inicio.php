<?php

namespace App\Controllers;

use App\Models\CaixaModel;
use App\Models\ClienteModel;
use App\Models\ConfigEmpresaModel;
use App\Models\ContaPagarModel;
use App\Models\ContaReceberModel;
use App\Models\DespesaModel;
use App\Models\LancamentoModel;
use App\Models\LoginModel;
use App\Models\OrcamentoModel;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\VendaModel;
use CodeIgniter\Controller;

class Inicio extends Controller
{
    private $empresa_model;
    private $links;
    private $produto_model;
    private $caixa_model;
    private $conta_a_pagar_model;
    private $conta_a_receber_model;
    private $despesa_model;
    private $lancamento_model;
    private $venda_model;
    private $cliente_model;
    private $pedido_model;
    private $orcamento_model;

    function __construct()
    {
        $this->empresa_model = new ConfigEmpresaModel();

        $this->links = [
            'menu' => '1.m',
            'item' => '1.0',
        ];

        $this->produto_model         = new ProdutoModel();
        $this->caixa_model           = new CaixaModel();
        $this->conta_a_pagar_model   = new ContaPagarModel();
        $this->conta_a_receber_model = new ContaReceberModel();
        $this->despesa_model         = new DespesaModel();
        $this->lancamento_model      = new LancamentoModel();
        $this->venda_model           = new VendaModel();
        $this->cliente_model         = new ClienteModel();
        $this->pedido_model          = new PedidoModel();
        $this->orcamento_model       = new OrcamentoModel();
    }

    public function index()
    {
        $ano_e_mes_atual = date('Y-m');

        // ---------------------------------------------- INFORMAÇÕES RÁPIDAS ------------------------------------------  //
        $data['total_de_clientes']   = $this->cliente_model->selectCount('id_cliente')->first()['id_cliente'];
        $data['total_de_produtos']   = $this->produto_model->selectCount('id_produto')->first()['id_produto'];
        $data['total_de_vendas']     = $this->venda_model->selectCount('id_venda')->first()['id_venda'];
        $data['total_de_pedidos']    = $this->pedido_model->selectCount('id_pedido')->first()['id_pedido'];
        $data['total_de_orcamentos'] = $this->orcamento_model->selectCount('id_orcamento')->first()['id_orcamento'];

        // Somatório faturamento do Mês Atual
        $vendas      = $this->venda_model->selectSum('valor_a_pagar')->where('data >=', "$ano_e_mes_atual-01")->where('data <=', "$ano_e_mes_atual-31")->first()['valor_a_pagar'];
        $lancamentos = $this->lancamento_model->selectSum('valor')->where('data >=', "$ano_e_mes_atual-01")->where('data <=', "$ano_e_mes_atual-31")->first()['valor'];
        
        $data['faturamento_total'] = $vendas + $lancamentos;
        // ------------------------------------------------------------------------------------------------------------- //


        // ---------------------------------------------------- ALERTAS -----------------------------------------------  //
        $data['empresa'] = $this->empresa_model->where('id_config', 1)->first();
        $data['links']   = $this->links;
        
        $data['produtos']         = $this->produto_model->where('quantidade <= quantidade_minima')->findAll();
        $data['caixas']           = $this->caixa_model->where('status = "Aberto"')->findAll();
        $data['contas_a_pagar']   = $this->conta_a_pagar_model->where('status = "Aberta" OR status = "Vencida"')->findAll();
        $data['contas_a_receber'] = $this->conta_a_receber_model->where('status = "Aberta" OR status = "Vencida"')->findAll();

        $dataI = date('Y-m-01');
        $dataF = date('Y-m-31');
        $data['despesas'] = $this->despesa_model->where("data >= '$dataI' AND data <= '$dataF'")->selectSum('valor')->first();
        $data['receitas'] = $this->lancamento_model->where("data >= '$dataI' AND data <= '$dataF'")->selectSum('valor')->first();
        // ------------------------------------------------------------------------------------------------------------- //


        // ---------------------------------- DADOS PARA OS GRÁFICOS DE FATURAMENTO -----------------------------------  //
        $ano_atual = date('Y');

        $meses = [
            '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'
        ];

        $faturamentos = [];
        $somatorio_faturamento = 0;

        foreach($meses as $mes)
        {
            $somatorio_vendas = $this->venda_model->selectSum('valor_a_pagar')->where('data >=', "$ano_atual-$mes-01")->where('data <=', "$ano_atual-$mes-31")->first()['valor_a_pagar'];
            $somatorio_lancamentos = $this->lancamento_model->selectSum('valor')->where('data >=', "$ano_atual-$mes-01")->where('data <=', "$ano_atual-$mes-31")->first()['valor'];

            $faturamento = $somatorio_vendas + $somatorio_lancamentos; // O faturamento é a soma das vendas e dos lançamentos de cada mês

            if(empty($faturamento)) // Caso o retorno seja null informando que não teve faturamento nesse mês
            {
                $faturamentos[] = 0;
            }
            else
            {
                $faturamentos[] = $faturamento;
            }
        }

        $data['faturamentos'] = $faturamentos;
        // ---------------------------------------------------------------------------------------------------------  //


        // ------------------------------------- CONTAS À PAGAR MÊS/ANO ATUAL --------------------------------------- //
        $contas_a_pagar_do_mes_atual = $this->conta_a_pagar_model->where('data_de_vencimento >=', "$ano_e_mes_atual-01")->where('data_de_vencimento <=', "$ano_e_mes_atual-31")->where("status='Aberta' OR status='Vencida'")->find();
        $data['contas_a_pagar_do_mes_atual'] = $contas_a_pagar_do_mes_atual;
        // ---------------------------------------------------------------------------------------------------------  //


        // ------------------------------------- CONTAS À PAGAR MÊS/ANO ATUAL --------------------------------------- //
        $contas_a_receber_do_mes_atual = $this->conta_a_receber_model->where('data_de_vencimento >=', "$ano_e_mes_atual-01")->where('data_de_vencimento <=', "$ano_e_mes_atual-31")->where("status='Aberta' OR status='Vencida'")->find();
        $data['contas_a_receber_do_mes_atual'] = $contas_a_receber_do_mes_atual;
        // ---------------------------------------------------------------------------------------------------------  //

        $session = session();
        $tema = $session->get('tema');

        echo view('templates/header', $data);
        if((int) $tema === 0)
        {
            echo view('dashboard/index');
        }
        else
        {
            echo view('dashboard/index_1');
        }
        echo view('templates/footer');
    }
}
