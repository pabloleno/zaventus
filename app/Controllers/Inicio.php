<?php

namespace App\Controllers;

use App\Libraries\FaturamentoNegocio;
use App\Models\CaixaModel;
use App\Models\ConfigEmpresaModel;
use App\Models\ContaPagarModel;
use App\Models\ContaReceberModel;
use App\Models\DespesaModel;
use App\Models\LancamentoModel;
use App\Models\OrdemDeServicoModel;
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
    private $pedido_model;
    private $ordem_de_servico_model;
    private $faturamento_negocio;

    public function __construct()
    {
        $this->empresa_model = new ConfigEmpresaModel();

        $this->links = [
            'menu' => '1.m',
            'item' => '1.0',
        ];

        $this->produto_model = new ProdutoModel();
        $this->caixa_model = new CaixaModel();
        $this->conta_a_pagar_model = new ContaPagarModel();
        $this->conta_a_receber_model = new ContaReceberModel();
        $this->despesa_model = new DespesaModel();
        $this->lancamento_model = new LancamentoModel();
        $this->venda_model = new VendaModel();
        $this->pedido_model = new PedidoModel();
        $this->ordem_de_servico_model = new OrdemDeServicoModel();
        $this->faturamento_negocio = new FaturamentoNegocio();
    }

    public function index()
    {
        $ano_e_mes_atual = date('Y-m');
        $data_inicio_mes_atual = date('Y-m-01');
        $data_fim_mes_atual = date('Y-m-t');

        $data['total_de_vendas'] = $this->venda_model->selectCount('id_venda')->first()['id_venda'];
        $data['total_de_pedidos'] = $this->pedido_model->selectCount('id_pedido')->first()['id_pedido'];
        $data['total_de_orcamentos'] = $this->ordem_de_servico_model
            ->whereIn('situacao', ['Em aberto', 'Em andamento', 'Aberto'])
            ->selectCount('id_ordem')
            ->first()['id_ordem'];
        $data['total_de_orcamentos_concretizados'] = $this->ordem_de_servico_model
            ->where('situacao', 'Concretizada')
            ->selectCount('id_ordem')
            ->first()['id_ordem'];

        $data['faturamento_produtos'] = $this->faturamento_negocio->totalProdutos($data_inicio_mes_atual, $data_fim_mes_atual);
        $data['faturamento_servicos'] = $this->faturamento_negocio->totalServicos($data_inicio_mes_atual, $data_fim_mes_atual);
        $data['faturamento_total'] = $data['faturamento_produtos'] + $data['faturamento_servicos'];

        $data['empresa'] = $this->empresa_model->where('id_config', 1)->first();
        $data['links'] = $this->links;

        $data['produtos'] = $this->produto_model->where('quantidade <= quantidade_minima')->findAll();
        $data['caixas'] = $this->caixa_model->where('status = "Aberto"')->findAll();
        $data['contas_a_pagar'] = $this->conta_a_pagar_model->where('status = "Aberta" OR status = "Vencida"')->findAll();
        $data['contas_a_receber'] = $this->conta_a_receber_model->where('status = "Aberta" OR status = "Vencida"')->findAll();

        $dataI = $data_inicio_mes_atual;
        $dataF = $data_fim_mes_atual;
        $data['despesas'] = $this->despesa_model->where("data >= '$dataI' AND data <= '$dataF'")->selectSum('valor')->first();
        $data['receitas'] = $this->lancamento_model->where("data >= '$dataI' AND data <= '$dataF'")->selectSum('valor')->first();

        $ano_atual = date('Y');
        $meses = [
            '01', '02', '03', '04', '05', '06',
            '07', '08', '09', '10', '11', '12',
        ];

        $faturamentos_produtos = [];
        $faturamentos_servicos = [];

        foreach ($meses as $mes) {
            $data_inicio_mes = "$ano_atual-$mes-01";
            $data_fim_mes = date('Y-m-t', strtotime($data_inicio_mes));

            $faturamentos_produtos[] = $this->faturamento_negocio->totalProdutos($data_inicio_mes, $data_fim_mes);
            $faturamentos_servicos[] = $this->faturamento_negocio->totalServicos($data_inicio_mes, $data_fim_mes);
        }

        $data['faturamentos_produtos'] = $faturamentos_produtos;
        $data['faturamentos_servicos'] = $faturamentos_servicos;

        $contas_a_pagar_do_mes_atual = $this->conta_a_pagar_model
            ->where('data_de_vencimento >=', "$ano_e_mes_atual-01")
            ->where('data_de_vencimento <=', $data_fim_mes_atual)
            ->where("status='Aberta' OR status='Vencida'")
            ->find();
        $data['contas_a_pagar_do_mes_atual'] = $contas_a_pagar_do_mes_atual;

        $contas_a_receber_do_mes_atual = $this->conta_a_receber_model
            ->where('data_de_vencimento >=', "$ano_e_mes_atual-01")
            ->where('data_de_vencimento <=', $data_fim_mes_atual)
            ->where("status='Aberta' OR status='Vencida'")
            ->find();
        $data['contas_a_receber_do_mes_atual'] = $contas_a_receber_do_mes_atual;

        $session = session();
        $tema = $session->get('tema');

        echo view('templates/header', $data);
        if ((int) $tema === 0) {
            echo view('dashboard/index');
        } else {
            echo view('dashboard/index_1');
        }
        echo view('templates/footer');
    }

}
