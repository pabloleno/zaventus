<?php

namespace App\Controllers;

use App\Libraries\CredencialIntegracaoPagamento;
use App\Libraries\ProvedoresPagamento;
use App\Models\FormaDePagamentoModel;
use App\Models\IntegracaoPagamentoModel;
use CodeIgniter\Controller;

class Desenvolvedor extends Controller
{
    private IntegracaoPagamentoModel $integracaoPagamentoModel;
    private FormaDePagamentoModel $formaDePagamentoModel;

    /**
     * Inicializa os modelos usados pelo painel de integracoes.
     */
    public function __construct()
    {
        $this->integracaoPagamentoModel = new IntegracaoPagamentoModel();
        $this->formaDePagamentoModel = new FormaDePagamentoModel();
    }

    /**
     * Exibe provedores, estado de configuracao e formas vinculadas.
     */
    public function index()
    {
        $data = $this->dadosBase();
        $data['integracoes'] = array_map(
            fn (array $integracao): array => $this->prepararIntegracaoExibicao($integracao),
            $this->integracaoPagamentoModel->orderBy('nome')->findAll()
        );
        $data['formas_de_pagamento'] = $this->formaDePagamentoModel->comIntegracao();

        echo view('templates/header');
        echo view('desenvolvedor/index', $data);
        echo view('templates/footer');
    }

    /**
     * Exibe o formulario de configuracao do provedor escolhido.
     */
    public function edit($idIntegracao)
    {
        $integracao = $this->integracaoPagamentoModel->find((int) $idIntegracao);

        if (! $integracao) {
            return redirect()->to('/desenvolvedor');
        }

        $data = $this->dadosBase();
        $data['integracao'] = $this->prepararIntegracaoExibicao($integracao);
        $data['detalhes_provedor'] = ProvedoresPagamento::detalhes((string) $integracao['provedor']);

        echo view('templates/header');
        echo view('desenvolvedor/form', $data);
        echo view('templates/footer');
    }

    /**
     * Salva somente configuracoes; nenhuma cobranca e disparada por esta acao.
     */
    public function store()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        $idIntegracao = (int) $this->request->getPost('id_integracao');
        $integracao = $this->integracaoPagamentoModel->find($idIntegracao);

        if (! $integracao) {
            return $this->redirecionarErro('Provedor de pagamento nao encontrado.');
        }

        $ambiente = (string) $this->request->getPost('ambiente');
        $ativo = $this->request->getPost('ativo') === '1' ? 1 : 0;
        $credencialPublica = trim((string) $this->request->getPost('credencial_publica'));
        $credencialSecreta = trim((string) $this->request->getPost('credencial_secreta'));

        if (! in_array($ambiente, ['sandbox', 'producao'], true)) {
            return $this->redirecionarErro('Selecione um ambiente valido.');
        }

        if ($ativo === 1 && (int) $integracao['api_publica'] !== 1) {
            return $this->redirecionarErro('Este provedor nao possui API publica suportada para ativacao.');
        }

        if ($ativo === 1 && in_array($integracao['provedor'], ['paypal', 'banco_inter'], true) && $credencialPublica === '') {
            return $this->redirecionarErro('Informe o identificador publico exigido pelo provedor antes de ativa-lo.');
        }

        $dados = [
            'id_integracao' => $idIntegracao,
            'ambiente' => $ambiente,
            'credencial_publica' => $credencialPublica !== '' ? $credencialPublica : null,
            'ativo' => $ativo,
            'observacoes' => trim((string) $this->request->getPost('observacoes')),
        ];

        if ($this->request->getPost('remover_credencial_secreta') === '1') {
            $dados['credencial_secreta'] = null;
        } elseif ($credencialSecreta !== '') {
            try {
                $dados['credencial_secreta'] = CredencialIntegracaoPagamento::criptografar($credencialSecreta);
            } catch (\RuntimeException $exception) {
                return $this->redirecionarErro($exception->getMessage());
            }
        }

        $segredoConfigurado = ! empty($dados['credencial_secreta'])
            || (! array_key_exists('credencial_secreta', $dados) && ! empty($integracao['credencial_secreta']));

        if ($ativo === 1 && ! $segredoConfigurado) {
            return $this->redirecionarErro('Informe a credencial secreta antes de ativar o provedor.');
        }

        $this->integracaoPagamentoModel->save($dados);
        session()->setFlashdata('alert', 'success_integracao_pagamento');

        return redirect()->to('/desenvolvedor');
    }

    /**
     * Monta os dados comuns do menu e cabecalho.
     */
    private function dadosBase(): array
    {
        return [
            'links' => [
                'menu' => '11.m',
                'item' => '11.0',
                'subItem' => '11.7',
            ],
            'titulo' => [
                'modulo' => 'Desenvolvedor - APIs de Pagamento',
                'icone' => 'fas fa-code',
            ],
            'caminhos' => [
                ['titulo' => lang('App.menu.home'), 'rota' => '/inicio', 'active' => false],
                ['titulo' => lang('App.menu.developer'), 'rota' => '', 'active' => true],
            ],
        ];
    }

    /**
     * Acrescenta estado legivel sem expor a credencial secreta.
     */
    private function prepararIntegracaoExibicao(array $integracao): array
    {
        $integracao['segredo_configurado'] = ! empty($integracao['credencial_secreta']);
        $integracao['detalhes'] = ProvedoresPagamento::detalhes((string) $integracao['provedor']);

        if ((int) $integracao['api_publica'] !== 1) {
            $integracao['status_integracao'] = 'Sem API publica suportada';
            $integracao['status_classe'] = 'secondary';
        } elseif ((int) $integracao['ativo'] !== 1) {
            $integracao['status_integracao'] = 'Inativa';
            $integracao['status_classe'] = 'secondary';
        } elseif (! $integracao['segredo_configurado']) {
            $integracao['status_integracao'] = 'Credenciais pendentes';
            $integracao['status_classe'] = 'warning';
        } else {
            $integracao['status_integracao'] = 'Credenciais configuradas';
            $integracao['status_classe'] = 'success';
        }

        return $integracao;
    }

    /**
     * Retorna ao formulario exibindo uma mensagem segura.
     */
    private function redirecionarErro(string $mensagem)
    {
        session()->setFlashdata('errors', [$mensagem]);
        session()->setFlashdata('alert', 'error_integracao_pagamento');

        return redirect()->back()->withInput();
    }
}
