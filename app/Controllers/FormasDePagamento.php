<?php

namespace App\Controllers;

use App\Models\FormaDePagamentoModel;
use CodeIgniter\Controller;

class FormasDePagamento extends Controller
{
    private $links;
    private $forma_de_pagamento_model;

    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.2'
        ];

        $this->forma_de_pagamento_model = new FormaDePagamentoModel();
    }

    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Formas de Pagamento',
            'icone'  => 'fa fa-database'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Formas de Pagamento", 'rota'   => "", 'active' => true]
        ];

        $data['formas_de_pagamento'] = $this->forma_de_pagamento_model->findAll();

        echo view('templates/header');
        echo view('lancamentos/index', $data);
        echo view('templates/footer');
    }
}
