<?php

namespace App\Controllers;

require 'mysqldump/autoload.php';

use Ifsnop\Mysqldump as IMysqldump;

use App\Models\LoginModel;
use App\Models\ConfigEmpresaModel;
use App\Models\ConfigNFeNFCeModel;
use App\Models\ConfigNFCeModel;
use App\Models\FormaDePagamentoModel;
use CodeIgniter\Controller;

class Configs extends Controller
{
    private $config_nfe_nfce_model;
    private $config_nfce_model;
    private $config_empresa_model;
    private $forma_de_pagamento_model;
    private $login_model;

    function __construct()
    {
        $this->config_nfe_nfce_model = new ConfigNFeNFCeModel();
        $this->config_nfce_model = new ConfigNFCeModel();
        $this->config_empresa_model = new ConfigEmpresaModel();
        $this->forma_de_pagamento_model = new FormaDePagamentoModel();
        $this->login_model = new LoginModel();
    }

    public function nfe()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.1'
        ];

        $data['titulo'] = [
            'modulo' => 'Config. NFe',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "NFe", 'rota'   => "", 'active' => true]
        ];

        $data['dados'] = $this->config_nfe_nfce_model->where('id_config', 1)->first();

        echo view('templates/header');
        echo view('configs/nfe', $data);
        echo view('templates/footer');
    }

    public function store_nfe()
    {
        $dados = $this->request->getvar();
        $dados['id_config'] = 1; // Só tem uma configuração para NFe / NFCe

        $file = $this->request->getFile('arquivo');

        if ($file->isValid()) // Verifica se foi selecionado o certificado.
        {
            $local = WRITEPATH . "uploads\certificado_nfe.pfx";
            unlink($local);
            $file->store('../../writable/uploads/', "certificado_nfe.pfx");

            $dados['certificado'] = 1;
        }

        $this->config_nfe_nfce_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/nfe');
    }

    public function nfce()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.2'
        ];

        $data['titulo'] = [
            'modulo' => 'Config. NFCe',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "NFCe", 'rota'   => "", 'active' => true]
        ];

        $data['dados'] = $this->config_nfce_model->where('id_config', 1)->first();

        echo view('templates/header');
        echo view('configs/nfce', $data);
        echo view('templates/footer');
    }

    public function store_nfce()
    {
        $file = $this->request->getFile('arquivo');
        $dados = $this->request->getvar();

        if ($file->isValid()) // Verifica se foi selecionado o certificado.
        {
            $local = WRITEPATH . "uploads\certificado_nfce.pfx";
            unlink($local);
            $file->store('../../writable/uploads/', "certificado_nfce.pfx");

            $dados['certificado'] = 1;
        }

        $dados['id_config'] = 1; // Só tem uma configuração para NFe / NFCe

        $this->config_nfce_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/nfce');
    }

    public function empresa()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.3'
        ];

        $data['titulo'] = [
            'modulo' => 'Config. Empresa',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Empresa", 'rota'   => "", 'active' => true]
        ];

        $data['empresa'] = $this->config_empresa_model->where('id_config', 1)->first();

        echo view('templates/header');
        echo view('configs/empresa', $data);
        echo view('templates/footer');
    }

    public function sistema()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.4'
        ];

        $data['titulo'] = [
            'modulo' => 'Config. Sistema',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Sistema", 'rota'   => "", 'active' => true]
        ];

        $session      = session();
        $id_login     = $session->get('id_login');
        $data['tema'] = $this->login_model->where('id_login', $id_login)->first()['tema'];

        $data['formas_de_pagamento'] = $this->forma_de_pagamento_model->findAll();

        echo view('templates/header');
        echo view('configs/sistema', $data);
        echo view('templates/footer');
    }

    public function alteraTema()
    {
        $tema = $this->request->getvar('tema');
        
        $session  = session();
        $id_login = $session->get('id_login');
        $this->login_model->set('tema', $tema)->where('id_login', $id_login)->update();

        return redirect()->to('/login/logout');
    }

    public function store_empresa()
    {
        $dados = $this->request->getvar();
        $dados['id_config'] = 1; // Só tem uma configuração para a Empresa

        $this->config_empresa_model->save($dados);

        $session = session();
        $session->setFlashdata('alert', 'success_edit');

        return redirect()->to('/configs/empresa');
    }

    // ------------------------------ FORMA DE PAGAMENTO -------------------------------- //
    public function createFormaDePagamento()
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.4'
        ];

        $data['titulo'] = [
            'modulo' => 'Nova Forma de Pagamento',
            'icone'  => 'fa fa-circle-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Sistema", 'rota' => "/configs/sistema", 'active' => false],
            ['titulo' => "Nova Forma de Pagamento", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('configs/form_forma_de_pagamento', $data);
        echo view('templates/footer');
    }

    public function editFormaDePagamento($id_forma)
    {
        $data['links'] = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.4'
        ];

        $data['titulo'] = [
            'modulo' => 'Editar Forma de Pagamento',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Sistema", 'rota' => "/configs/sistema", 'active' => false],
            ['titulo' => "Nova Forma de Pagamento", 'rota'   => "", 'active' => true]
        ];

        $data['forma_de_pagamento'] = $this->forma_de_pagamento_model->where('id_forma', $id_forma)->first();

        echo view('templates/header');
        echo view('configs/form_forma_de_pagamento', $data);
        echo view('templates/footer');
    }

    public function store_forma_de_pagamento()
    {
        $dados = $this->request->getvar();
        $this->forma_de_pagamento_model->save($dados);

        $session = session();

        if(isset($dados['id_forma']))
        {
            $session->setFlashdata('alert', 'success_edit_forma_de_pagamento');
            return redirect()->to('/configs/sistema');
        }

        $session->setFlashdata('alert', 'success_create_forma_de_pagamento');
        return redirect()->to('/configs/sistema');
    }

    public function delete_forma_de_pagamento($id_forma)
    {
        $this->forma_de_pagamento_model->where('id_forma', $id_forma)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete_forma_de_pagamento');

        return redirect()->to('/configs/sistema');
    }

    public function backupDataBase()
    {
        try {
            $dump = new IMysqldump\Mysqldump('mysql:host=localhost;dbname=nxgestao', 'root', '');
            $dump->start(WRITEPATH.'backup_mysql/BACKUP_DATABASE_SISTEMA.sql');

            header("Content-Type: application/sql");
            // informa o tipo do arquivo ao navegador
            header("Content-Length: " . filesize(WRITEPATH . 'backup_mysql/BACKUP_DATABASE_SISTEMA.sql'));
            // informa o tamanho do arquivo ao navegador
            header("Content-Disposition: attachment; filename=" . basename(WRITEPATH . 'backup_mysql/BACKUP_DATABASE_SISTEMA.sql'));
            // informa ao navegador que é tipo anexo e faz abrir a janela de download, 
            //tambem informa o nome do arquivo
            readfile(WRITEPATH . 'backup_mysql/BACKUP_DATABASE_SISTEMA.sql'); // lê o arquivo
            exit; // aborta pós-ações

            $session = session();
            $session->setFlashdata('alert', 'success_bkp_database');
            
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }
}
