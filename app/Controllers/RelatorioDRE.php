<?php

namespace App\Controllers;

use App\Libraries\FaturamentoNegocio;
use App\Libraries\TipoNegocio;
use App\Models\ConfigEmpresaModel;
use App\Models\DespesaModel;
use CodeIgniter\Controller;

class RelatorioDRE extends Controller
{
    private $links;
    private $config_empresa_model;
    private $despesa_model;
    private $faturamento_negocio;

    public function __construct()
    {
        $this->links = ['menu' => '5.m', 'item' => '5.0', 'subItem' => '5.10'];
        $this->despesa_model = new DespesaModel();
        $this->config_empresa_model = new ConfigEmpresaModel();
        $this->faturamento_negocio = new FaturamentoNegocio();
    }

    public function index()
    {
        $data = [
            'links' => $this->links,
            'titulo' => ['modulo' => 'Relatorio DRE', 'icone' => 'fa fa-list'],
            'caminhos' => [
                ['titulo' => 'Inicio', 'rota' => '/inicio', 'active' => false],
                ['titulo' => 'Relatorio DRE', 'rota' => '', 'active' => true],
            ],
            'empresa' => $this->config_empresa_model->where('id_config', 1)->first(),
        ];

        $dados = $this->request->getVar();
        $dados['data_inicio'] = $dados['data_inicio'] ?? date('Y-m-01');
        $dados['data_final'] = $dados['data_final'] ?? date('Y-m-t');

        $data['tipo_negocio'] = TipoNegocio::normalizar(
            $dados['tipo_negocio'] ?? TipoNegocio::TODOS,
            TipoNegocio::TODOS,
            true
        );
        $data['tipos_negocio'] = TipoNegocio::opcoes(true);

        $resumo = $this->faturamento_negocio->resumo(
            $dados['data_inicio'],
            $dados['data_final'],
            $data['tipo_negocio']
        );
        $data['faturamento_produtos'] = $resumo['produtos'];
        $data['faturamento_servicos'] = $resumo['servicos'];
        $data['faturamento_lancamentos'] = $resumo['lancamentos'];
        $data['faturamento'] = $resumo['total'];

        $data['impostos'] = $this->totalDespesa('Impostos', $dados, $data['tipo_negocio']);
        $data['despesas_variaveis'] = $this->totalDespesa('Despesas variáveis', $dados, $data['tipo_negocio']);
        $data['despesas_fixas'] = $this->totalDespesa('Despesas fixas', $dados, $data['tipo_negocio']);
        $data['gastos_com_pessoas'] = $this->totalDespesa('Gastos com pessoas', $dados, $data['tipo_negocio']);
        $data['prolabore'] = $this->totalDespesa('Prolabore', $dados, $data['tipo_negocio']);
        $data['data_inicio'] = $dados['data_inicio'];
        $data['data_final'] = $dados['data_final'];

        session()->setFlashdata('alert', 'success_gerar_relatorio_dre');

        echo view('templates/header');
        echo view('relatorio_dre/index', $data);
        echo view('templates/footer');
    }

    private function totalDespesa(string $tipo, array $periodo, string $tipoNegocio): float
    {
        $query = $this->despesa_model
            ->where('data >=', $periodo['data_inicio'])
            ->where('data <=', $periodo['data_final'])
            ->where('tipo', $tipo);

        if ($tipoNegocio !== TipoNegocio::TODOS) {
            $query->where('tipo_negocio', $tipoNegocio);
        }

        return (float) ($query->selectSum('valor')->first()['valor'] ?? 0);
    }
}
