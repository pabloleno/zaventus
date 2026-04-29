<?php

namespace App\Controllers;

use App\Models\ConfigEmpresaModel;
use App\Models\LoginModel;
use CodeIgniter\Controller;

class Login extends Controller
{
    private $links;
    private $empresa_model;
    private $login_model;

    function __construct()
    {
        $this->links = [
            'menu' => '11.m',
            'item' => '11.0',
            'subItem' => '11.5'
        ];

        $this->empresa_model = new ConfigEmpresaModel();
        $this->login_model = new LoginModel();
    }

    public function index()
    {
        $data['empresa'] = $this->empresa_model->where('id_config', 1)->first();

        echo view('login/index', $data);
    }

    public function usuarios()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Usuários',
            'icone'  => 'fa fa-users'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Lançamentos", 'rota'   => "", 'active' => true]
        ];

        $data['usuarios'] = $this->login_model->findAll();

        echo view('templates/header');
        echo view('login/usuarios', $data);
        echo view('templates/footer');
    }

    public function create()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Novo Usuário',
            'icone'  => 'fa fa-circle-plus'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Usuários", 'rota' => "/login/usuarios", 'active' => false],
            ['titulo' => "Novo", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('login/form', $data);
        echo view('templates/footer');
    }

    public function edit($id_login)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Usuário',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Usuários", 'rota' => "/login/usuarios", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['usuario'] = $this->login_model->where('id_login', $id_login)->first();

        echo view('templates/header');
        echo view('login/form', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $dados = $this->request->getvar();

        $aux = "";
        if(isset($dados['modulo_vendas']))
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '{"vendas":{"modulo":1,"venda_rapida":'.$dados['venda_rapida'].',"pdv":'.$dados['pdv'].',"pesq_produto":'.$dados['pesq_produto'].',"hist_de_vendas":'.$dados['hist_de_vendas'].'}';
        }
        else
        {
            if($aux != ""){$aux .= ",";}
            $aux = '{"vendas":{"modulo":0,"venda_rapida":1,"pdv":1,"pesq_produto":1,"hist_de_vendas":1}';
        }

        if(isset($dados['modulo_controle_geral']))
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"controle_geral":{"modulo":1,"clientes":'.$dados['clientes'].',"fornecedores":'.$dados['fornecedores'].',"funcionarios":'.$dados['funcionarios'].',"vendedores":'.$dados['vendedores'].'}';
        }
        else
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"controle_geral":{"modulo":0,"clientes":1,"fornecedores":1,"funcionarios":1,"vendedores":1}';
        }

        if(isset($dados['modulo_estoque']))
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"estoque":{"modulo":1,"produtos":'.$dados['produtos'].',"reposicoes":'.$dados['reposicoes'].',"saida_de_mercadorias":'.$dados['saida_de_mercadorias'].',"categorias_do_produto":'.$dados['categorias_do_produto'].'}';
        }
        else
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"estoque":{"modulo":0,"produtos":1,"reposicoes":1,"saida_de_mercadorias":1,"categorias_do_produto":1}';
        }
        
        if(isset($dados['modulo_financeiro']))
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"financeiro":{"modulo":1,"caixas":'.$dados['caixas'].',"lancamentos":'.$dados['lancamentos'].',"retiradas_do_caixa":'.$dados['retiradas_do_caixa'].',"despesas":'.$dados['despesas'].', "contas_a_pagar":'.$dados['contas_a_pagar'].',"contas_a_receber":'.$dados['contas_a_receber'].',"orcamentos":'.$dados['orcamentos'].',"pedidos":'.$dados['pedidos'].',"relatorio_dre":'.$dados['relatorio_dre'].',"inventario_do_estoque":'.$dados['inventario_do_estoque'].',"controle_fiscal":'.$dados['controle_fiscal'].'}';
        }
        else
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"financeiro":{"modulo":0,"caixas":1,"lancamentos":1,"retiradas_do_caixa":1,"despesas":1,"contas_a_pagar":1,"contas_a_receber":1,"orcamentos":1,"pedidos":1,"relatorio_dre":1,"inventario_do_estoque":1,"controle_fiscal":1}';
        }

        if(isset($dados['modulo_relatorios']))
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"relatorios":{"modulo":1,"vendas":'.$dados['vendas'].',"estoque":'.$dados['estoque'].',"financeiro":'.$dados['financeiro'].',"geral":'.$dados['geral'].'}';
        }
        else
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"relatorios":{"modulo":0,"vendas":1,"estoque":1,"financeiro":1,"geral":1}';
        }

        if(isset($dados['modulo_configs']))
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"configs":{"modulo":1,"nfe":'.$dados['nfe'].',"nfce":'.$dados['nfce'].',"empresa":'.$dados['empresa'].',"sistema":'.$dados['sistema'].',"usuarios":'.$dados['usuarios'].',"backup_de_dados":'.$dados['backup_de_dados'].'}';
        }
        else
        {
            if($aux != ""){$aux .= ",";}
            $aux .= '"configs":{"modulo":0,"nfe":1,"nfce":1,"empresa":1,"sistema":1,"usuarios":1,"backup_de_dados":1}';
        }

        $aux .= "}";

        $dados['controle_de_acesso'] = $aux;
        $this->login_model->save($dados);

        $session = session();

        // Se o usuário estiver editando
        if(isset($dados['id_login']))
        {
            $session->setFlashdata('alert', 'success_edit');

            return redirect()->to('/login/usuarios');
        }

        $session->setFlashdata('alert', 'success_create');

        return redirect()->to('/login/usuarios');
    }

    public function autenticar()
    {
        $dados = $this->request->getvar();

        $empresa = $this->empresa_model->where('id_config', 1)->first();
        $login = $this->login_model->where('usuario', $dados['usuario'])->where('senha', $dados['senha'])->first();

        $session = session();
        if(!empty($login))
        {
            // Alerta de succeso de autenticação
            $session->setFlashdata('alert', 'success_autentication');

            // Insere variáveis na sessão
            $session->set('id_login', $login['id_login']);
            $session->set('usuario', $login['usuario']);
            $session->set('primeiro_nome', $login['primeiro_nome']);
            $session->set('nome_fantasia', $empresa['nome_fantasia']);
            $session->set('tema', $login['tema']);
            $session->set('controle_de_acesso', $login['controle_de_acesso']);

            // Guarda o último acesso do usuário
            // $ultimo_acesso = date('d/m/Y') . " às " . date('H:i:s');
            // $this->login_model->set('ultimo_acesso', $ultimo_acesso)->where('id_login', $session->get('id_login'))->update();

            // Redireciona para a Dashboard do sistema
            return redirect()->to('/inicio');
        }
        else
        {
            // Informa que os dados estão errados
            $session->setFlashdata('alert', 'error_autentication');

            // Retorna para o login
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();

        return redirect()->to('/login');
    }

    public function delete($id_login)
    {
        $this->login_model->where('id_login', $id_login)->delete();

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/login/usuarios');
    }
}