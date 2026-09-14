<?php

namespace App\Controllers\Concerns;

use App\Libraries\AtendimentoGrafica;
use App\Libraries\AnexoAtendimento;
use App\Libraries\ConsumoAtendimento;
use App\Libraries\OrcamentoCalculo;
use App\Libraries\RecebimentoAtendimento;
use App\Models\FormaDePagamentoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

trait AtendimentoWeb
{
    public function atendimentoNovo()
    {
        return $this->formularioAtendimento();
    }

    public function atendimentoEditar($id)
    {
        return $this->formularioAtendimento((int) $id);
    }

    private function formularioAtendimento(int $id = 0)
    {
        $data = $this->cadastrosAtendimento();
        $data['ordem'] = $id ? (new AtendimentoGrafica())->obter($id, true) : [
            'itens' => [], 'parcelas' => [], 'versao' => 0,
            'status_operacional' => 'aguardando_aprovacao',
            'desconto_tipo' => 'nenhum', 'desconto_informado' => '0.00',
            'id_atendente' => (int) session('id_login'),
            'chave_criacao' => bin2hex(random_bytes(24)),
            'validade_orcamento' => date('Y-m-d', strtotime('+15 days')),
            'condicao_pagamento' => '',
        ];
        if ($id && ! empty($data['ordem']['deleted_at'])) {
            return redirect()->to('/ordensDeServicos/excluidos');
        }
        foreach (['vendedores' => ['vendedor', 'id_vendedor'], 'tecnicos' => ['tecnico', 'id_tecnico']] as $lista => [$relacao, $campo]) {
            $anterior = $data['ordem'][$relacao] ?? null;
            if ($anterior && ! in_array((int) $anterior[$campo], array_map('intval', array_column($data[$lista], $campo)), true)) {
                $anterior['inativo_no_atendimento'] = true;
                $data[$lista][] = $anterior;
            }
        }
        $old = session('_ci_old_input');
        if (! empty($old['post'])) {
            $data['ordem'] = array_replace($data['ordem'], $old['post']);
            foreach (['itens', 'parcelas'] as $key) {
                $decoded = json_decode($old['post'][$key . '_json'] ?? '[]', true);
                if (is_array($decoded)) {
                    $data['ordem'][$key] = $decoded;
                }
            }
        }
        $data['titulo_pagina'] = $id ? 'Editar orçamento' : 'Novo orçamento';
        return $this->paginaAtendimento('form_atendimento', $data);
    }

    public function atendimentoListar()
    {
        return $this->listagemAtendimento('servicos');
    }

    public function atendimentoOrcamentos()
    {
        return $this->listagemAtendimento('orcamentos');
    }

    public function atendimentoExcluidos()
    {
        return $this->listagemAtendimento('excluidos');
    }

    private function listagemAtendimento(string $grupo)
    {
        $filtros = $this->request->getGet();
        $filtros['grupo'] = $grupo;
        $ordens = (new AtendimentoGrafica())->listar($filtros, $grupo === 'excluidos');
        return $this->paginaAtendimento('lista_atendimento', [
            'ordens' => $ordens, 'grupo' => $grupo, 'filtros' => $filtros,
            'titulo_pagina' => ['servicos' => 'Ordens de serviço', 'orcamentos' => 'Orçamentos', 'excluidos' => 'Excluídos — 30 dias'][$grupo],
        ]);
    }

    public function atendimentoMostrar($id)
    {
        $ordem = (new AtendimentoGrafica())->obter((int) $id, true);
        if (! empty($ordem['deleted_at'])) {
            return redirect()->to('/ordensDeServicos/excluidos');
        }
        $ordem['consumos'] = db_connect()->table('saida_de_mercadorias s')->select('s.*, p.nome AS material_nome, p.unidade')
            ->join('produtos p', 'p.id_produto = s.id_produto', 'left')->where('s.id_ordem', (int) $id)
            ->orderBy('s.id_saida', 'DESC')->get()->getResultArray();
        return $this->paginaAtendimento('ficha_atendimento', array_merge($this->cadastrosAtendimento(), [
            'ordem' => $ordem,
            'titulo_pagina' => 'Atendimento',
        ]));
    }

    public function imprimirAtendimento($id)
    {
        $ordem = (new AtendimentoGrafica())->obter((int) $id);
        return view('ordem_de_servico/impressao_atendimento', [
            'ordem' => $ordem,
            'empresa' => db_connect()->table('config_empresa')->where('id_config', 1)->get()->getRowArray(),
        ]);
    }

    public function salvarAtendimento()
    {
        $dados = $this->request->getPost();
        try {
            foreach (['itens', 'parcelas'] as $key) {
                $dados[$key] = json_decode($dados[$key . '_json'] ?? '[]', true, 64, JSON_THROW_ON_ERROR);
                if (! is_array($dados[$key])) {
                    throw new \InvalidArgumentException('Confira os itens e o pagamento do orçamento.');
                }
            }
            $id = (new AtendimentoGrafica())->salvar($dados, (int) session('id_login'));
            session()->setFlashdata('atendimento_sucesso', 'Orçamento salvo.');
            return redirect()->to('/ordensDeServicos/show/' . $id);
        } catch (Throwable $e) {
            log_message('error', 'Salvar atendimento: {erro}', ['erro' => $e->getMessage()]);
            session()->setFlashdata('atendimento_erro', $this->erroAtendimento($e));
            $id = (int) ($dados['id_ordem'] ?? 0);
            return redirect()->to($id ? '/ordensDeServicos/edit/' . $id : '/ordensDeServicos/create')->withInput();
        }
    }

    public function calcularAtendimento()
    {
        try {
            $itens = json_decode((string) $this->request->getPost('itens_json'), true, 64, JSON_THROW_ON_ERROR);
            if (! is_array($itens) || count($itens) > 200) {
                throw new \InvalidArgumentException('Confira os serviços do orçamento.');
            }
            // Usa a mesma precisão do campo persistido pelo agregado.
            $descontoInformado = OrcamentoCalculo::decimal($this->request->getPost('desconto_informado') ?: '0', 2);
            $total = OrcamentoCalculo::total($itens,
                (string) ($this->request->getPost('desconto_tipo') ?: 'nenhum'),
                $descontoInformado,
                $this->request->getPost('frete') ?: '0',
                $this->request->getPost('outros') ?: '0'
            );
            return $this->response->setJSON($total + ['desconto_informado' => $descontoInformado]);
        } catch (Throwable $e) {
            return $this->response->setStatusCode(422)->setJSON(['erro' => $this->erroAtendimento($e)]);
        }
    }

    public function statusAtendimento()
    {
        return $this->acaoAtendimento(function ($id, $user, $dados) {
            $novo = (string) ($dados['status_operacional'] ?? '');
            if ($novo === '' && isset($dados['situacao'])) {
                $novo = AtendimentoGrafica::status(['situacao' => $dados['situacao']]);
            }
            (new AtendimentoGrafica())->mudarStatus($id, $novo, $user, (string) ($dados['motivo'] ?? ''), (string) ($dados['observacoes'] ?? ''));
        }, 'Situação atualizada e registrada no histórico.');
    }

    public function reabrirAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user, $dados) => (new AtendimentoGrafica())->reabrir($id, $user, (string) ($dados['observacoes'] ?? '')), 'Atendimento reaberto.');
    }

    public function excluirAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user) => (new AtendimentoGrafica())->lixeira($id, $user), 'Orçamento enviado para Excluídos.', '/ordensDeServicos/excluidos');
    }

    public function restaurarAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user) => (new AtendimentoGrafica())->restaurar($id, $user), 'Orçamento restaurado.');
    }

    public function apagarAtendimento()
    {
        return $this->acaoAtendimento(function ($id, $user, $dados) {
            $anexos = (new AtendimentoGrafica())->excluirDefinitivamente($id, $user, (string) ($dados['confirmacao'] ?? ''));
            foreach ($anexos as $anexo) {
                AnexoAtendimento::removerArquivo($anexo['arquivo'] ?? '');
            }
        }, 'Orçamento excluído definitivamente.', '/ordensDeServicos/excluidos');
    }

    public function receberAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user, $dados) => (new RecebimentoAtendimento())->registrar($id, $dados, $user), 'Recebimento registrado.');
    }

    public function estornarRecebimentoAtendimento()
    {
        return $this->acaoAtendimento(function ($id, $user, $dados) {
            $recebimento = db_connect()->table('pagamentos_do_cliente')->where('id_pagamento', (int) ($dados['id_recebimento'] ?? 0))->where('id_ordem', $id)->get()->getRowArray();
            if (! $recebimento) {
                throw new \InvalidArgumentException('Recebimento não encontrado neste atendimento.');
            }
            (new RecebimentoAtendimento())->estornar((int) $recebimento['id_pagamento'], $user, (string) ($dados['motivo'] ?? ''));
        }, 'Recebimento estornado. O registro foi preservado.');
    }

    public function anexarAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user, $dados) => (new AnexoAtendimento())->salvar($id, $this->request->getFile('arquivo'), $dados, $user), 'Arquivo anexado.');
    }

    public function baixarAnexoAtendimento($id)
    {
        $anexo = (new AnexoAtendimento())->obter((int) $id);
        return $this->response->download(WRITEPATH . $anexo['arquivo'], null)->setFileName($anexo['nome']);
    }

    public function consumirAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user, $dados) => (new ConsumoAtendimento())->registrar($id, $dados, $user), 'Consumo de material registrado.');
    }

    public function salvarEquipamentoAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user, $dados) => (new AtendimentoGrafica())->salvarEquipamento($id, $dados, $user), 'Equipamento salvo.');
    }

    public function removerEquipamentoAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user, $dados) => (new AtendimentoGrafica())->removerEquipamento($id, (int) ($dados['id_equipamento'] ?? 0), $user), 'Equipamento removido e registrado no histórico.');
    }

    public function atendimentoFormularioAtual()
    {
        session()->setFlashdata('atendimento_erro', 'O formulário foi atualizado. Confira o orçamento e salve novamente.');
        $id = (int) $this->request->getPost('id_ordem');
        return redirect()->to($id ? '/ordensDeServicos/edit/' . $id : '/ordensDeServicos/create');
    }

    public function estornarConsumoAtendimento()
    {
        return $this->acaoAtendimento(fn ($id, $user, $dados) => (new ConsumoAtendimento())->estornar($id, (int) ($dados['id_saida'] ?? 0), $user, (string) ($dados['motivo'] ?? '')), 'Consumo estornado. O histórico foi preservado.');
    }

    private function acaoAtendimento(callable $acao, string $sucesso, ?string $destino = null)
    {
        $dados = $this->request->getPost();
        $id = (int) ($dados['id_ordem'] ?? 0);
        try {
            $acao($id, (int) session('id_login'), $dados);
            session()->setFlashdata('atendimento_sucesso', $sucesso);
        } catch (Throwable $e) {
            session()->setFlashdata('atendimento_erro', $this->erroAtendimento($e));
        }
        return redirect()->to($destino ?? ($id > 0 ? '/ordensDeServicos/show/' . $id : '/ordensDeServicos/orcamentos'));
    }

    private function erroAtendimento(Throwable $e): string
    {
        if ($e instanceof \InvalidArgumentException || $e instanceof \RuntimeException) {
            if (! $e instanceof \CodeIgniter\Database\Exceptions\DatabaseException) {
                return $e->getMessage();
            }
        }
        log_message('error', 'Atendimento: {erro}', ['erro' => $e->getMessage()]);
        return 'Não foi possível concluir. Os dados foram preservados; confira o formulário e tente novamente.';
    }

    private function cadastrosAtendimento(): array
    {
        $db = db_connect();
        return [
            'clientes' => $db->table('clientes')->orderBy('nome')->get()->getResultArray(),
            'catalogo' => $db->table('servicos_mao_de_obra')->where('ativo', 1)->orderBy('nome')->get()->getResultArray(),
            'vendedores' => $db->table('vendedores')->where('status !=', 'Removido')->orderBy('nome')->get()->getResultArray(),
            'tecnicos' => $db->table('tecnicos')->where('status', 'Ativo')->orderBy('nome')->get()->getResultArray(),
            'usuarios' => $db->table('login')->select('id_login,primeiro_nome,foto')->get()->getResultArray(),
            'formas' => (new FormaDePagamentoModel())->paraServicos(),
            'caixas' => $db->table('caixas')->where('status', 'Aberto')->get()->getResultArray(),
            'materiais' => $db->table('produtos')->where('ativo', 1)->orderBy('nome')->get()->getResultArray(),
            'empresa' => $db->table('config_empresa')->where('id_config', 1)->get()->getRowArray(),
        ];
    }

    private function paginaAtendimento(string $view, array $data)
    {
        $grupo = $data['grupo'] ?? null;
        if ($grupo === null && isset($data['ordem'])) {
            $grupo = in_array(AtendimentoGrafica::status($data['ordem']), ['em_elaboracao', 'aguardando_aprovacao'], true)
                ? 'orcamentos' : 'servicos';
        }

        $data['links'] = ['menu' => '2.m', 'item' => '2.0'];
        if (in_array($grupo, ['servicos', 'orcamentos'], true)) {
            $data['links']['subItem'] = $grupo === 'servicos' ? '2.6' : '2.7';
        }
        $data['statuses'] = AtendimentoGrafica::STATUS;
        return view('templates/header') . view('ordem_de_servico/' . $view, $data) . view('templates/footer');
    }
}
