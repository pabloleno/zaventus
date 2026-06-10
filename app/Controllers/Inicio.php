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

    public function index()
    {
        $mes = (int) ($this->request->getGet('mes') ?: date('n'));
        $ano = (int) ($this->request->getGet('ano') ?: date('Y'));
        $mes = min(max($mes, 1), 12);
        $ano = min(max($ano, 2000), 2100);

        $data['empresa'] = $this->empresa_model->where('id_config', 1)->first();
        $data['links'] = $this->links;
        $data['dashboard'] = $this->dashboard_negocio->montar($ano, $mes);
        $data['produtos_estoque_baixo'] = $this->produto_model->where('quantidade <= quantidade_minima')->findAll();
        $data['caixas_abertos'] = $this->caixa_model->where('status', 'Aberto')->findAll();

        echo view('templates/header', $data);
        echo view('dashboard/index');
        echo view('templates/footer');
    }
}
