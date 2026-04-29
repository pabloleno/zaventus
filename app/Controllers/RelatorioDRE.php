<?php

namespace App\Controllers;

use App\Models\ConfigEmpresaModel;
use App\Models\DespesaModel;
use App\Models\LancamentoModel;
use App\Models\VendaModel;
use CodeIgniter\Controller;

class RelatorioDRE extends Controller
{
    private $links;
    private $config_empresa_model;
    private $lancamento_model;
    private $venda_model;
    private $despesa_model;

    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.10'
        ];

        $this->lancamento_model     = new LancamentoModel();
        $this->venda_model          = new VendaModel();
        $this->despesa_model        = new DespesaModel();
        $this->config_empresa_model = new ConfigEmpresaModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Relatório DRE',
            'icone'  => 'fa fa-list'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Relatório DRE", 'rota'   => "", 'active' => true]
        ];

        $data['empresa']            = $this->config_empresa_model->where('id_config', 1)->first();

        $dados = $this->request->getvar();

        if(!isset($dados['data_inicio']))
        {
            $dados['data_inicio'] = date('Y-m-01');
            $dados['data_final'] = date('Y-m-31');
        }

        // ----------------------------------------------------------------------------------------------------- //
        $lancamentos = $this->lancamento_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final'])->selectSum('valor')->first()['valor'];
        $vendas = $this->venda_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final'])->selectSum('valor_a_pagar')->first()['valor_a_pagar'];

        $faturamento = $lancamentos + $vendas;

        $data['faturamento'] = $faturamento; 
        // ----------------------------------------------------------------------------------------------------- //

        $data['impostos']           = $this->despesa_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final'])->selectSum('valor')->where('tipo', 'Impostos')->first()['valor'];
        $data['despesas_variaveis'] = $this->despesa_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final'])->selectSum('valor')->where('tipo', 'Despesas variáveis')->first()['valor'];
        $data['despesas_fixas']     = $this->despesa_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final'])->selectSum('valor')->where('tipo', 'Despesas fixas')->first()['valor'];
        $data['gastos_com_pessoas'] = $this->despesa_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final'])->selectSum('valor')->where('tipo', 'Gastos com pessoas')->first()['valor'];
        $data['prolabore'] = $this->despesa_model->where('data >=', $dados['data_inicio'])->where('data <=', $dados['data_final'])->selectSum('valor')->where('tipo', 'Prolabore')->first()['valor'];

        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final']  = $dados['data_final'];

        $session = session();
        $session->setFlashdata('alert', 'success_gerar_relatorio_dre');

        echo view('templates/header');
        echo view('relatorio_dre/index', $data);
        echo view('templates/footer');
    }
}
