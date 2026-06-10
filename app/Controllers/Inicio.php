<?php

namespace App\Controllers;

use App\Libraries\DashboardNegocio;
use App\Models\CaixaModel;
use App\Models\ConfigEmpresaModel;
use App\Models\ProdutoModel;
use CodeIgniter\Controller;

class Inicio extends Controller
{
    private $empresa_model;
    private $links;
    private $produto_model;
    private $caixa_model;
    private $dashboard_negocio;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    public function __construct()
    {
        $this->empresa_model = new ConfigEmpresaModel();

        $this->links = [
            'menu' => '1.m',
            'item' => '1.0',
        ];

        $this->produto_model = new ProdutoModel();
        $this->caixa_model = new CaixaModel();
        $this->dashboard_negocio = new DashboardNegocio();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $mesInformado = $this->request->getGet('mes');
        $anoInformado = $this->request->getGet('ano');
        $periodoAutomatico = $mesInformado === null && $anoInformado === null;

        $mesAtual = (int) date('n');
        $anoAtual = (int) date('Y');
        $mes = (int) ($mesInformado ?: $mesAtual);
        $ano = (int) ($anoInformado ?: $anoAtual);
        $mes = min(max($mes, 1), 12);
        $ano = min(max($ano, 2000), 2100);

        $data['empresa'] = $this->empresa_model->where('id_config', 1)->first();
        $data['links'] = $this->links;
        $data['periodo_automatico'] = $periodoAutomatico;
        $data['segundos_ate_proxima_virada'] = max(1, strtotime('first day of next month 00:00:05') - time());
        $data['dashboard'] = $this->dashboard_negocio->montar($ano, $mes);
        $data['produtos_estoque_baixo'] = $this->produto_model->where('quantidade <= quantidade_minima')->findAll();
        $data['caixas_abertos'] = $this->caixa_model->where('status', 'Aberto')->findAll();

        echo view('templates/header', $data);
        echo view('dashboard/index');
        echo view('templates/footer');
    }
}
