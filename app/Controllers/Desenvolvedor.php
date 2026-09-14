<?php

namespace App\Controllers;

use App\Libraries\CredencialIntegracaoPagamento;
use App\Libraries\ProvedoresPagamento;
use App\Libraries\TesteConexaoIntegracaoPagamento;
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
        $data['formas_de_pagamento'] = $this->formaDePagamentoModel->comIntegracao();
        $data['integracoes'] = array_map(
            fn (array $integracao): array => $this->prepararIntegracaoExibicao($integracao, $data['formas_de_pagamento']),
            $this->integracaoPagamentoModel->orderBy('nome')->findAll()
        );

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
        $data['integracao'] = $this->prepararIntegracaoExibicao(
            $integracao,
            $this->formaDePagamentoModel->where('id_integracao', $integracao['id_integracao'])->findAll()
        );
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
        $detalhes = ProvedoresPagamento::detalhes((string) $integracao['provedor']);

        if (! in_array($ambiente, ['sandbox', 'producao'], true)) {
            return $this->redirecionarErro('Selecione um ambiente valido.');
        }

        if ($ativo === 1 && ! ($detalhes['ativacao_suportada'] ?? false)) {
            return $this->redirecionarErro(
                (string) ($detalhes['motivo_teste_indisponivel'] ?? 'Este provedor ainda nao possui conector suportado para ativacao.')
            );
        }

        if ($ativo === 1 && ($detalhes['credencial_publica_obrigatoria'] ?? false) && $credencialPublica === '') {
            return $this->redirecionarErro('Informe o identificador publico exigido pelo provedor antes de ativa-lo.');
        }

        $credenciaisAlteradas = $ambiente !== (string) $integracao['ambiente']
            || $credencialPublica !== (string) ($integracao['credencial_publica'] ?? '')
            || $credencialSecreta !== ''
            || $this->request->getPost('remover_credencial_secreta') === '1';

        if ($ativo === 1 && ($credenciaisAlteradas || ($integracao['ultimo_teste_status'] ?? '') !== 'sucesso')) {
            return $this->redirecionarErro('Salve as credenciais como inativas, execute o teste de conexao e somente depois ative o provedor.');
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

        if ($credenciaisAlteradas) {
            $dados['ultimo_teste_em'] = null;
            $dados['ultimo_teste_status'] = null;
            $dados['ultimo_teste_mensagem'] = null;
        }

        $this->integracaoPagamentoModel->save($dados);
        session()->setFlashdata('alert', 'success_integracao_pagamento');

        return redirect()->to('/desenvolvedor');
    }

    /**
     * Executa diagnostico de autenticacao sem criar cobrancas.
     */
    public function testar($idIntegracao)
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        $integracao = $this->integracaoPagamentoModel->find((int) $idIntegracao);

        if (! $integracao) {
            return $this->redirecionarErro('Provedor de pagamento nao encontrado.');
        }

        $resultado = TesteConexaoIntegracaoPagamento::executar($integracao);
        $this->integracaoPagamentoModel->save([
            'id_integracao' => $integracao['id_integracao'],
            'ultimo_teste_em' => date('Y-m-d H:i:s'),
            'ultimo_teste_status' => $resultado['status'],
            'ultimo_teste_mensagem' => $resultado['mensagem'],
            'ativo' => $resultado['status'] === 'sucesso' ? $integracao['ativo'] : 0,
        ]);

        session()->setFlashdata(
            'alert',
            $resultado['status'] === 'sucesso' ? 'success_teste_integracao_pagamento' : 'error_teste_integracao_pagamento'
        );
        session()->setFlashdata('resultado_teste_integracao', $resultado['mensagem']);

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
    private function prepararIntegracaoExibicao(array $integracao, array $formasPagamento = []): array
    {
        $integracao['segredo_configurado'] = ! empty($integracao['credencial_secreta']);
        $integracao['detalhes'] = ProvedoresPagamento::detalhes((string) $integracao['provedor']);
        $formasVinculadas = array_values(array_filter(
            $formasPagamento,
            static fn (array $forma): bool => (int) ($forma['id_integracao'] ?? 0) === (int) $integracao['id_integracao']
        ));
        $integracao['formas_vinculadas'] = count($formasVinculadas);
        $integracao['formas_servicos'] = count($formasVinculadas);
        $identificadorConfigurado = ! ($integracao['detalhes']['credencial_publica_obrigatoria'] ?? false)
            || ! empty($integracao['credencial_publica']);
        $integracao['pode_testar'] = ($integracao['detalhes']['teste_conexao_suportado'] ?? false)
            && $integracao['segredo_configurado']
            && $identificadorConfigurado;

        if ((int) $integracao['api_publica'] !== 1) {
            $integracao['status_integracao'] = 'Operacao manual';
            $integracao['status_classe'] = 'secondary';
        } elseif (! ($integracao['detalhes']['ativacao_suportada'] ?? false)) {
            $integracao['status_integracao'] = 'Conector pendente';
            $integracao['status_classe'] = 'warning';
        } elseif ((int) $integracao['ativo'] !== 1) {
            $integracao['status_integracao'] = 'Inativa';
            $integracao['status_classe'] = 'secondary';
        } elseif (($integracao['ultimo_teste_status'] ?? '') === 'sucesso') {
            $integracao['status_integracao'] = 'Autenticacao validada';
            $integracao['status_classe'] = 'success';
        } else {
            $integracao['status_integracao'] = 'Teste pendente';
            $integracao['status_classe'] = 'warning';
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

        // withInput() serializa todo o POST e poderia guardar a credencial secreta na sessao.
        return redirect()->back();
    }
}
