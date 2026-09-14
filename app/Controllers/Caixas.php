<?php

namespace App\Controllers;

use App\Libraries\FaturamentoNegocio;
use App\Models\CaixaModel;
use App\Models\LancamentoModel;
use App\Models\RetiradaModel;
use App\Models\VendaModel;
use CodeIgniter\Controller;

class Caixas extends Controller
{
    private $links;
    private $caixa_model;
    private $lancamento_model;
    private $venda_model;
    private $retirada_model;

    /**
     * Inicializa as dependencias usadas por este componente.
     */
    function __construct()
    {
        $this->links = [
            'menu' => '5.m',
            'item' => '5.0',
            'subItem' => '5.1'
        ];

        $this->caixa_model = new CaixaModel();
        $this->lancamento_model = new LancamentoModel();
        $this->venda_model = new VendaModel();
        $this->retirada_model = new RetiradaModel();
    }

    /**
     * Carrega os dados e exibe a tela principal deste modulo.
     */
    public function index()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Caixas',
            'icone'  => 'fa fa-book-open'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Caixas", 'rota'   => "", 'active' => true]
        ];

        // ---------------------------------------- FILTRAR ----------------------------------------- //
        $dados = $this->request->getvar();

        $session = session();

        if(!empty($dados))
        {
            $id_caixa = $dados['id_caixa'];

            $data_inicio = $dados['data_inicio'];
            $data_final = $dados['data_final'];

            $status = $dados['status'];

            if($dados['id_caixa'] != "") // Filtra somente pelo Cód do caixa
            {
                $caixas = $this->caixa_model->where('id_caixa', $id_caixa)->findAll();

                $data['id_caixa'] = $id_caixa;
            }
            else if($dados['data_inicio'] != "" && $dados['data_final'] != "" && $status == "") // Filtra somente pela data inicial e data final, quando não tem status definido
            {
                $caixas = $this->caixa_model->where('data_de_abertura >=', $data_inicio)->where('data_de_abertura <=', $data_final)->find();
                
                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else if($status != "" && $data_inicio == "" && $data_final == "") // Filtra pelo Status, quando não tem data de inicio e fim definida
            {
                if($status == "Todos")
                {
                    $caixas = $this->caixa_model->findAll();
                }
                else if($status == "Aberto")
                {
                    $caixas = $this->caixa_model->where('status', 'Aberto')->findAll();
                }
                else if($status == "Fechado")
                {
                    $caixas = $this->caixa_model->where('status', 'Fechado')->findAll();
                }

                $data['status'] = $status;
            }
            else if($status != "" && $data_inicio != "" && $data_final != "") // Filtra em conjunto, status e data inicio e final
            {
                if($status == "Todos") // Pega todos os abertos e fechados
                {
                    $caixas = $this->caixa_model->where('data_de_abertura >=', $data_inicio)->where('data_de_abertura <=', $data_final)->find();
                }
                else // Pega de acordo com o status
                {
                    $caixas = $this->caixa_model->where('status', $status)->where('data_de_abertura >=', $data_inicio)->where('data_de_abertura <=', $data_final)->find();
                }

                $data['status'] = $status;
                $data['data_inicio'] = $data_inicio;
                $data['data_final'] = $data_final;
            }
            else // Caso desfaça todos os filtros sem clicar no botão REMOVER FILTROS mostra os 5 últimos caixas cadastrados
            {
                $caixas = $this->caixa_model->orderBy('id_caixa', 'DESC')->findAll();
            }

            $session->setFlashdata('alert', 'success_filter');
        }
        else
        {
            $caixas = $this->caixa_model->orderBy('id_caixa', 'DESC')->findAll();
        }
        // ------------------------------------------------------------------------------------------ //

        $data['caixas'] = $caixas;

        // $data['caixas'] = $this->caixa_model->findAll();

        echo view('templates/header');
        echo view('caixas/index', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega e exibe os detalhes do registro solicitado.
     */
    public function show($id_caixa)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Dados do Caixa',
            'icone'  => 'fa fa-book-open'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Caixas", 'rota' => "/caixas", 'active' => false],
            ['titulo' => "Dados", 'rota'   => "", 'active' => true]
        ];

        $data['caixa']       = $this->caixa_model->where('id_caixa', $id_caixa)->first();
        if (empty($data['caixa'])) {
            return redirect()->to('/caixas');
        }
        $data['lancamentos'] = (new FaturamentoNegocio())->consultaLancamentos()->select('l.*')->where('l.id_caixa', $id_caixa)->get()->getResultArray();
        $data['vendas']      = $this->venda_model->where('id_caixa', $id_caixa)->findAll();
        $data['retiradas']   = $this->retirada_model->where('id_caixa', $id_caixa)->findAll();

        // Recebimentos de OS ja estao nos lancamentos; nao somar a OS novamente.
        $sum_lancamentos = array_sum(array_column($data['lancamentos'], 'valor'));
        $sum_vendas = array_sum(array_column($data['vendas'], 'valor_a_pagar'));
        $sum_retiradas = array_sum(array_column($data['retiradas'], 'valor'));
        $data['somatorio'] = (float) $data['caixa']['valor_inicial'] + $sum_lancamentos + $sum_vendas - $sum_retiradas;

        echo view('templates/header');
        echo view('caixas/show', $data);
        echo view('templates/footer');
    }

    /**
     * Abre um novo caixa para registrar as movimentacoes do operador.
     */
    public function abrir()
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Abrir Caixa',
            'icone'  => 'fa fa-plus-circle'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Caixas", 'rota' => "/caixas", 'active' => false],
            ['titulo' => "Abrir", 'rota'   => "", 'active' => true]
        ];

        echo view('templates/header');
        echo view('caixas/form', $data);
        echo view('templates/footer');
    }

    /**
     * Carrega o registro solicitado e exibe o formulario de edicao.
     */
    public function edit($id_caixa)
    {
        $data['links'] = $this->links;

        $data['titulo'] = [
            'modulo' => 'Editar Caixa',
            'icone'  => 'fa fa-edit'
        ];

        $data['caminhos'] = [
            ['titulo' => "Início", 'rota' => "/inicio", 'active' => false],
            ['titulo' => "Caixas", 'rota' => "/caixas", 'active' => false],
            ['titulo' => "Editar", 'rota'   => "", 'active' => true]
        ];

        $data['caixa'] = $this->caixa_model->where('id_caixa', $id_caixa)->first();

        echo view('templates/header');
        echo view('caixas/form', $data);
        echo view('templates/footer');
    }

    /**
     * Fecha o caixa informado depois de validar seus valores finais.
     */
    public function fechar($id_caixa)
    {
        $dados = $this->request->getvar();
        
        $this->caixa_model->save([
            'id_caixa'            => $id_caixa,
            'data_de_fechamento'  => date('Y-m-d'),
            'hora_de_fechamento'  => date('H:i:s'),
            'valor_de_fechamento' => $dados['valor_de_fechamento'],
            'observacoes'         => $dados['observacoes'],
            'status'              => 'Fechado'
        ]);

        $session = session();
        $session->setFlashdata('alert', 'success_fechar');

        return redirect()->to("/caixas/show/$id_caixa");
    }

    /**
     * Reabre o caixa informado para permitir novas movimentacoes.
     */
    public function reabrir($id_caixa)
    {
        $this->caixa_model->save([
            'id_caixa' => $id_caixa,
            'status'   => 'Aberto'
        ]);

        $session = session();
        $session->setFlashdata('alert', 'success_reabrir');

        return redirect()->to("/caixas/show/$id_caixa");
    }

    /**
     * Valida e persiste os dados enviados pelo formulario.
     */
    public function store()
    {
        $dados = $this->request->getvar();
        
        if(!isset($dados['id_caixa'])) // Caso a ação seja abrir o caixa, adiciona o status de Aberto
        {
            $dados['status'] = "Aberto";
        }

        $this->caixa_model->save($dados);

        $session = session();
        if(isset($dados['id_caixa'])) // Caso a ação seja editar caixa
        {
            $session->setFlashdata('alert', 'success_edit');
            return redirect()->to("/caixas/show/{$dados['id_caixa']}");
        }

        $session->setFlashdata('alert', 'success_open');
        return redirect()->to('/caixas');
    }

    /**
     * Remove o registro solicitado e retorna para a listagem.
     */
    public function delete($id_caixa)
    {
        $db = db_connect();
        $db->transBegin();
        try {
            $db->query($db->table('caixas')->where('id_caixa', $id_caixa)->getCompiledSelect() . ' FOR UPDATE');
            foreach (['lancamentos', 'retiradas', 'vendas'] as $tabela) {
                if ($db->table($tabela)->where('id_caixa', $id_caixa)->countAllResults() > 0) {
                    throw new \RuntimeException('Este caixa possui movimentacoes e deve ser preservado. Voce pode fecha-lo.');
                }
            }
            if ($db->fieldExists('id_caixa', 'pagamentos_do_cliente')
                && $db->table('pagamentos_do_cliente')->where('id_caixa', $id_caixa)->countAllResults() > 0) {
                throw new \RuntimeException('Este caixa possui recebimentos vinculados e deve ser preservado.');
            }
            $this->caixa_model->where('id_caixa', $id_caixa)->delete();
            if (! $db->transStatus()) {
                throw new \RuntimeException('Nao foi possivel excluir o caixa.');
            }
            $db->transCommit();
        } catch (\Throwable $exception) {
            $db->transRollback();
            session()->setFlashdata('errors', ['O caixa nao pode ser excluido enquanto possuir movimentacoes.']);
            return redirect()->to('/caixas/show/' . (int) $id_caixa);
        }

        $session = session();
        $session->setFlashdata('alert', 'success_delete');

        return redirect()->to('/caixas');
    }
}
