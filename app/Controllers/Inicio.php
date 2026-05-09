<?php

namespace App\Controllers;

use App\Models\CaixaModel;
use App\Models\ConfigEmpresaModel;
use App\Models\ContaPagarModel;
use App\Models\ContaReceberModel;
use App\Models\DespesaModel;
use App\Models\LancamentoModel;
use App\Models\OrdemDeServicoModel;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\ServicoMaoDeObraOsModel;
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
    private $servico_mao_de_obra_os_model;

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
        $this->servico_mao_de_obra_os_model = new ServicoMaoDeObraOsModel();
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

        $data['faturamento_produtos'] = (float) ($this->venda_model
            ->selectSum('valor_a_pagar')
            ->where('data >=', $data_inicio_mes_atual)
            ->where('data <=', $data_fim_mes_atual)
            ->first()['valor_a_pagar'] ?? 0);
        $data['faturamento_servicos'] = $this->calculaFaturamentoServicosConcretizados($data_inicio_mes_atual, $data_fim_mes_atual);
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

            $faturamento_produtos = $this->venda_model
                ->selectSum('valor_a_pagar')
                ->where('data >=', $data_inicio_mes)
                ->where('data <=', $data_fim_mes)
                ->first()['valor_a_pagar'];

            $faturamentos_produtos[] = empty($faturamento_produtos) ? 0 : (float) $faturamento_produtos;
            $faturamentos_servicos[] = $this->calculaFaturamentoServicosConcretizados($data_inicio_mes, $data_fim_mes);
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

    private function calculaFaturamentoServicosConcretizados(string $data_inicio, string $data_fim): float
    {
        $ordens = $this->ordem_de_servico_model
            ->where('situacao', 'Concretizada')
            ->where('data_de_saida >=', $data_inicio)
            ->where('data_de_saida <=', $data_fim)
            ->findAll();

        $faturamento = 0.0;

        foreach ($ordens as $ordem) {
            $servicos = $this->servico_mao_de_obra_os_model
                ->where('id_ordem', $ordem['id_ordem'])
                ->findAll();

            foreach ($servicos as $servico) {
                $quantidade = (float) ($servico['quantidade'] ?? 0);
                $valor = (float) ($servico['valor'] ?? 0);
                $faturamento += $quantidade * $valor;
            }

            $faturamento += (float) ($ordem['frete'] ?? 0);
            $faturamento += (float) ($ordem['outros'] ?? 0);
            $faturamento -= (float) ($ordem['desconto'] ?? 0);
        }

        return $faturamento;
    }
}
