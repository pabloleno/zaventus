<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/** Regras do atendimento compartilhadas pelo orçamento e pela ordem de serviço. */
final class AtendimentoGrafica
{
    public const STATUS = [
        'em_elaboracao' => 'Em elaboração',
        'aguardando_aprovacao' => 'Aguardando aprovação',
        'aprovado' => 'Aprovado',
        'falta_arte' => 'Falta arte',
        'arte_aprovacao' => 'Arte em aprovação',
        'aguardando_producao' => 'Aguardando produção',
        'em_producao' => 'Em produção',
        'pronto' => 'Pronto',
        'aguardando_retirada' => 'Aguardando retirada',
        'instalacao_agendada' => 'Instalação agendada',
        'concluido' => 'Concluído',
        'cancelado' => 'Cancelado',
    ];

    private const TEXTOS = [
        'desconto_motivo' => 255, 'condicao_pagamento' => 8000,
        'execucao_endereco' => 8000, 'execucao_referencia' => 255,
        'execucao_responsavel' => 128, 'execucao_telefone' => 32,
        'execucao_observacoes' => 8000, 'observacoes' => 2048,
        'observacoes_internas' => 2048, 'centro_de_custo' => 128,
    ];

    private BaseConnection $db;
    private RecebimentoAtendimento $recebimentos;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
        $this->recebimentos = new RecebimentoAtendimento($this->db);
    }

    public static function status(array $ordem): string
    {
        if (isset(self::STATUS[$ordem['status_operacional'] ?? ''])) {
            return $ordem['status_operacional'];
        }

        return [
            'Em aberto' => 'aguardando_aprovacao', 'Aberto' => 'aguardando_aprovacao',
            'Em andamento' => 'em_producao', 'Concretizada' => 'concluido', 'Cancelada' => 'cancelado',
        ][$ordem['situacao'] ?? ''] ?? 'em_elaboracao';
    }

    public static function totais(array $ordem, array $itens): array
    {
        $itens = array_values(array_filter($itens, static fn (array $item): bool => empty($item['removido_at'])));
        $novo = ! empty($ordem['status_operacional']);

        return OrcamentoCalculo::total(
            $itens,
            $novo ? (string) ($ordem['desconto_tipo'] ?? 'valor') : 'valor',
            $novo ? ($ordem['desconto_informado'] ?? '0') : ($ordem['desconto'] ?? '0'),
            $ordem['frete'] ?? '0',
            $ordem['outros'] ?? '0'
        );
    }

    public function obter(int $id, bool $excluidos = false): array
    {
        $ordem = $this->ordem($id);
        if (! $excluidos && $this->excluida($ordem)) {
            throw new RuntimeException('O atendimento está na lixeira. Restaure-o para continuar.');
        }

        $ordem['itens'] = $this->itens($id);
        $ordem['parcelas'] = $this->parcelas($id);
        $ordem['equipamentos'] = $this->db->table('equipamentos_os')->where('id_ordem', $id)
            ->where('deleted_at', null)->orderBy('id_equipamento')->get()->getResultArray();
        $ordem['anexos'] = $this->db->table('anexos_os')->where('id_ordem', $id)->orderBy('id_anexo')->get()->getResultArray();
        $ordem['historico'] = $this->db->table('ordens_de_servicos_historico h')
            ->select('h.*, login.primeiro_nome AS usuario_nome')
            ->join('login', 'login.id_login = h.id_login', 'left')
            ->where('h.id_ordem', $id)->orderBy('h.id_historico', 'DESC')->get()->getResultArray();
        $ordem['cliente'] = $this->linha('clientes', 'id_cliente', (int) $ordem['id_cliente']);
        $ordem['atendente'] = $this->db->table('login')->select('id_login, usuario, primeiro_nome, foto')
            ->where('id_login', (int) ($ordem['id_atendente'] ?? 0))->get()->getRowArray();
        $ordem['vendedor'] = $this->linha('vendedores', 'id_vendedor', (int) $ordem['id_vendedor']);
        $ordem['tecnico'] = empty($ordem['id_tecnico']) ? null : $this->linha('tecnicos', 'id_tecnico', (int) $ordem['id_tecnico']);
        $ordem['financeiro'] = $this->recebimentos->resumo($id);
        $ordem['totais'] = self::totais($ordem, $ordem['itens']);
        $ordem['itens'] = $ordem['totais']['itens'];
        if (empty($ordem['status_operacional'])) {
            $ordem['desconto_tipo'] = 'valor';
            $ordem['desconto_informado'] = $ordem['desconto'];
        }
        // O fallback é apenas de apresentação: não inventa um evento de migração.
        $ordem['status_operacional'] = self::status($ordem);

        return $ordem;
    }

    public function listar(array $filtros = [], bool $lixeira = false): array
    {
        $consulta = $this->db->table('ordens_de_servicos os')
            ->select('os.*, clientes.nome AS cliente_nome')
            ->join('clientes', 'clientes.id_cliente = os.id_cliente', 'left');
        $lixeira ? $consulta->where('os.deleted_at !=', null) : $consulta->where('os.deleted_at', null);

        // O grupo precisa ser filtrado no banco antes do limite de resultados.
        if (! $lixeira && empty($filtros['todos']) && in_array($filtros['grupo'] ?? '', ['orcamentos', 'servicos'], true)) {
            $validos = implode(',', array_map(fn (string $status): string => $this->db->escape($status), array_keys(self::STATUS)));
            $statusEfetivo = "CASE WHEN os.status_operacional IN ($validos) THEN os.status_operacional "
                . "WHEN os.situacao IN ('Em aberto','Aberto') THEN 'aguardando_aprovacao' "
                . "WHEN os.situacao = 'Em andamento' THEN 'em_producao' "
                . "WHEN os.situacao = 'Concretizada' THEN 'concluido' "
                . "WHEN os.situacao = 'Cancelada' THEN 'cancelado' ELSE 'em_elaboracao' END";
            $operador = $filtros['grupo'] === 'orcamentos' ? ' IN ' : ' NOT IN ';
            $consulta->where($statusEfetivo . $operador . "('em_elaboracao','aguardando_aprovacao')", null, false);
        }

        if (! empty($filtros['id_cliente'])) {
            $consulta->where('os.id_cliente', $this->id($filtros['id_cliente']));
        }
        if (! empty($filtros['id_ordem'])) {
            $consulta->where('os.id_ordem', $this->id($filtros['id_ordem']));
        }
        foreach (['data_inicio' => '>=', 'data_final' => '<='] as $campo => $operador) {
            if (! empty($filtros[$campo])) {
                $consulta->where('os.data_de_entrada ' . $operador, $this->data($filtros[$campo]));
            }
        }
        if (! empty($filtros['term'])) {
            $termo = $this->texto($filtros['term'], 128);
            $consulta->groupStart()->like('os.numero', $termo)->orLike('clientes.nome', $termo)->groupEnd();
        }
        if (! empty($filtros['status'])) {
            $status = $this->validaStatus($filtros['status']);
            $legados = [
                'aguardando_aprovacao' => ['Em aberto', 'Aberto'],
                'em_producao' => ['Em andamento'], 'concluido' => ['Concretizada'], 'cancelado' => ['Cancelada'],
            ][$status] ?? [];
            $consulta->groupStart()->where('os.status_operacional', $status);
            if ($legados !== []) {
                $consulta->orGroupStart()->groupStart()->where('os.status_operacional', null)
                    ->orWhere('os.status_operacional', '')->groupEnd()->whereIn('os.situacao', $legados)->groupEnd();
            }
            $consulta->groupEnd();
        }

        $ordens = $consulta->orderBy('os.id_ordem', 'DESC')->limit(200)->get()->getResultArray();
        foreach ($ordens as &$ordem) {
            $ordem['totais'] = self::totais($ordem, $this->itens((int) $ordem['id_ordem']));
            $financeiro = $this->recebimentos->resumo((int) $ordem['id_ordem']);
            $ordem['total_pago'] = $financeiro['pago'];
            $ordem['saldo'] = $financeiro['saldo'];
            $ordem['total'] = $ordem['totais']['total'];
            $ordem['status_operacional'] = self::status($ordem);
        }
        unset($ordem);

        return $ordens;
    }

    public function salvar(array $dados, int $idLogin): int
    {
        $id = $this->id($dados['id_ordem'] ?? null, true);
        $chave = $id === null ? $this->texto($dados['chave_criacao'] ?? '', 64) : null;
        if ($id === null && preg_match('/\A[A-Za-z0-9._:-]{16,64}\z/', $chave) !== 1) {
            throw new InvalidArgumentException('Reabra o novo orçamento para obter uma identificação de criação válida.');
        }
        $hash = $this->hashCriacao($dados);

        return $this->transacao(function () use ($dados, $idLogin, $id, $chave, $hash): int {
            $this->validaUsuario($idLogin);
            if ($id === null) {
                // Serializa criações do mesmo operador, inclusive dois envios da mesma chave.
                $this->linha('login', 'id_login', $idLogin, true);
                $existente = $this->db->table('ordens_de_servicos')->where('chave_criacao', $chave)->get()->getRowArray();
                if ($existente !== null) {
                    if ((int) $existente['created_by'] !== $idLogin || ! hash_equals((string) $existente['hash_criacao'], $hash)) {
                        throw new RuntimeException('Esta identificação de criação já foi utilizada com outros dados.');
                    }
                    return (int) $existente['id_ordem'];
                }
            }

            $atual = $id === null ? null : $this->ordem($id, true);
            if ($atual !== null) {
                $this->exigeDisponivel($atual);
                if (! isset($dados['versao']) || (string) $dados['versao'] !== (string) $atual['versao']) {
                    throw new RuntimeException('Este atendimento foi atualizado em outra tela. Recarregue antes de salvar.');
                }
                if (self::status($atual) === 'cancelado') {
                    throw new RuntimeException('Reabra o atendimento cancelado antes de alterá-lo.');
                }
            }

            if (! isset($dados['itens']) || ! is_array($dados['itens']) || count($dados['itens']) < 1 || count($dados['itens']) > 200) {
                throw new InvalidArgumentException('Adicione entre um e duzentos serviços do catálogo.');
            }
            $itens = $this->preparaItens($dados['itens'], $atual);
            $ordem = $this->preparaOrdem($dados, $atual, $itens, $idLogin);
            $totais = self::totais($ordem, $itens);
            $ordem['desconto'] = $totais['desconto'];
            if (bccomp($ordem['valor_entrada'], $totais['total'], 2) > 0) {
                throw new InvalidArgumentException('A entrada não pode superar o total do orçamento.');
            }

            if ($atual !== null) {
                $financeiro = $this->recebimentos->resumo($id);
                if (bccomp($financeiro['pago'], $totais['total'], 2) > 0) {
                    throw new RuntimeException('O total não pode ficar abaixo do valor já recebido. Registre o estorno antes.');
                }
                if ((int) $atual['id_cliente'] !== (int) $ordem['id_cliente'] && $this->temRecebimentos($id)) {
                    throw new RuntimeException('Não é possível trocar o cliente de um atendimento com recebimentos registrados.');
                }
            }

            $novoStatus = $this->validaStatus($dados['status_operacional'] ?? ($atual === null ? 'aguardando_aprovacao' : self::status($atual)));
            $agora = date('Y-m-d H:i:s');
            if ($atual === null) {
                $ordem += [
                    'created_at' => $agora, 'updated_at' => $agora, 'deleted_at' => null,
                    'data_de_entrada' => substr($agora, 0, 10), 'hora_de_entrada' => substr($agora, 11),
                    'data_de_saida' => null, 'hora_de_saida' => null, 'versao' => 0,
                    'chave_criacao' => $chave, 'hash_criacao' => $hash,
                ];
                $ordem['status_operacional'] = $novoStatus;
                $ordem['situacao'] = $this->situacaoLegada($novoStatus);
                $this->validaTransicao($ordem, $novoStatus, $dados['motivo'] ?? '');
                if ($novoStatus === 'concluido') {
                    $ordem['data_de_saida'] = substr($agora, 0, 10);
                    $ordem['hora_de_saida'] = substr($agora, 11);
                }
                $id = $this->inserir('ordens_de_servicos', $ordem);
                $numero = 'ORC-' . substr($agora, 0, 4) . '-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
                $this->atualizar('ordens_de_servicos', 'id_ordem', $id, ['numero' => $numero]);
            } else {
                // O status muda exclusivamente pelo caminho auditado abaixo.
                $ordem['status_operacional'] = self::status($atual);
                $ordem['situacao'] = $this->situacaoLegada($ordem['status_operacional']);
                $ordem['versao'] = (int) $atual['versao'] + 1;
                $ordem['updated_at'] = $agora;
                $this->atualizar('ordens_de_servicos', 'id_ordem', $id, $ordem);
            }

            $this->salvaItens($id, $itens, $agora);
            $this->salvaParcelas($id, $dados['parcelas'] ?? [], $totais['total'], $agora, $atual === null);
            if ($atual === null) {
                $this->evento($id, 'criado', null, $novoStatus, $idLogin, $dados['motivo'] ?? '', 'Orçamento criado.');
            } else {
                $this->evento($id, 'editado', self::status($atual), self::status($atual), $idLogin, '', 'Dados do atendimento atualizados. Total: R$ ' . $totais['total']);
                if ($novoStatus !== self::status($atual)) {
                    $this->alteraStatus($this->ordem($id), $novoStatus, $idLogin, $dados['motivo'] ?? '', $dados['observacao_status'] ?? '', false);
                }
            }
            $this->sincronizaConta($this->ordem($id));

            return $id;
        });
    }

    public function mudarStatus(int $id, string $novo, int $idLogin, string $motivo = '', string $observacoes = ''): void
    {
        $this->transacao(function () use ($id, $novo, $idLogin, $motivo, $observacoes): void {
            $this->validaUsuario($idLogin);
            $ordem = $this->ordem($id, true);
            $this->exigeDisponivel($ordem);
            if (self::status($ordem) === 'cancelado' && $novo !== 'cancelado') {
                throw new RuntimeException('Utilize Reabrir para retomar o atendimento cancelado.');
            }
            $this->alteraStatus($ordem, $novo, $idLogin, $motivo, $observacoes);
            $this->sincronizaConta($this->ordem($id));
        });
    }

    public function reabrir(int $id, int $idLogin, string $observacoes = ''): void
    {
        $this->transacao(function () use ($id, $idLogin, $observacoes): void {
            $this->validaUsuario($idLogin);
            $ordem = $this->ordem($id, true);
            $this->exigeDisponivel($ordem);
            if (self::status($ordem) !== 'cancelado') {
                throw new RuntimeException('Somente um atendimento cancelado pode ser reaberto.');
            }
            $evento = $this->db->table('ordens_de_servicos_historico')->where('id_ordem', $id)
                ->where('evento', 'cancelado')->orderBy('id_historico', 'DESC')->get()->getRowArray();
            $anterior = $evento['status_anterior'] ?? null;
            $novo = isset(self::STATUS[$anterior ?? '']) && ! in_array($anterior, ['cancelado', 'concluido'], true)
                ? $anterior : 'aguardando_aprovacao';
            $this->alteraStatus($ordem, $novo, $idLogin, '', $observacoes, true, 'reaberto');
            $this->sincronizaConta($this->ordem($id));
        });
    }

    public function lixeira(int $id, int $idLogin): void
    {
        $this->transacao(function () use ($id, $idLogin): void {
            $this->validaUsuario($idLogin);
            $ordem = $this->ordem($id, true);
            if ($this->excluida($ordem)) {
                return;
            }
            $agora = new DateTimeImmutable();
            $this->atualizar('ordens_de_servicos', 'id_ordem', $id, [
                'deleted_at' => $agora->format('Y-m-d H:i:s'), 'deleted_by' => $idLogin,
                'purge_at' => $agora->modify('+30 days')->format('Y-m-d H:i:s'),
                'updated_at' => $agora->format('Y-m-d H:i:s'), 'versao' => (int) $ordem['versao'] + 1,
            ]);
            $this->evento($id, 'excluido', self::status($ordem), self::status($ordem), $idLogin);
            $this->sincronizaConta($this->ordem($id));
        });
    }

    public function restaurar(int $id, int $idLogin): void
    {
        $this->transacao(function () use ($id, $idLogin): void {
            $this->validaUsuario($idLogin);
            $ordem = $this->ordem($id, true);
            if (! $this->excluida($ordem)) {
                throw new RuntimeException('O atendimento não está na lixeira.');
            }
            $this->atualizar('ordens_de_servicos', 'id_ordem', $id, [
                'deleted_at' => null, 'deleted_by' => null, 'purge_at' => null,
                'updated_at' => date('Y-m-d H:i:s'), 'versao' => (int) $ordem['versao'] + 1,
            ]);
            $this->evento($id, 'restaurado', self::status($ordem), self::status($ordem), $idLogin);
            $this->sincronizaConta($this->ordem($id));
        });
    }

    public function salvarEquipamento(int $id, array $dados, int $idLogin): int
    {
        return $this->transacao(function () use ($id, $dados, $idLogin): int {
            $this->validaUsuario($idLogin);
            $ordem = $this->ordem($id, true);
            $this->exigeDisponivel($ordem);
            if (self::status($ordem) === 'cancelado') {
                throw new RuntimeException('Reabra o atendimento cancelado antes de alterar seus equipamentos.');
            }
            if (isset($dados['versao']) && (string) $dados['versao'] !== (string) $ordem['versao']) {
                throw new RuntimeException('Este atendimento foi atualizado em outra tela. Recarregue antes de salvar.');
            }
            $idEquipamento = $this->id($dados['id_equipamento'] ?? null, true);
            $anterior = $idEquipamento === null ? null : $this->linha('equipamentos_os', 'id_equipamento', $idEquipamento);
            if ($idEquipamento !== null && ($anterior === null || (int) $anterior['id_ordem'] !== $id || ! empty($anterior['deleted_at']))) {
                throw new InvalidArgumentException('O equipamento não pertence a este atendimento ou foi removido.');
            }
            $campos = [];
            foreach (['equipamento', 'marca', 'modelo', 'serie', 'condicoes', 'defeitos', 'acessorios', 'solucao', 'laudo_tecnico', 'termos_de_garantia'] as $campo) {
                $limite = in_array($campo, ['equipamento', 'marca', 'modelo', 'serie'], true) ? 128 : 2048;
                $campos[$campo] = $this->texto($dados[$campo] ?? ($anterior[$campo] ?? ''), $limite);
            }
            if ($campos['equipamento'] === '') {
                throw new InvalidArgumentException('Informe o nome do equipamento.');
            }
            $agora = date('Y-m-d H:i:s');
            $campos['updated_at'] = $agora;
            if ($idEquipamento === null) {
                $idEquipamento = $this->inserir('equipamentos_os', $campos + ['id_ordem' => $id, 'created_at' => $agora, 'deleted_at' => null]);
            } else {
                $this->atualizar('equipamentos_os', 'id_equipamento', $idEquipamento, $campos);
            }
            $this->atualizar('ordens_de_servicos', 'id_ordem', $id, ['versao' => (int) $ordem['versao'] + 1, 'updated_at' => $agora]);
            $this->evento($id, $anterior === null ? 'equipamento_adicionado' : 'equipamento_alterado', self::status($ordem), self::status($ordem), $idLogin, '', $campos['equipamento']);

            return $idEquipamento;
        });
    }

    public function removerEquipamento(int $id, int $idEquipamento, int $idLogin): void
    {
        $this->transacao(function () use ($id, $idEquipamento, $idLogin): void {
            $this->validaUsuario($idLogin);
            $ordem = $this->ordem($id, true);
            $this->exigeDisponivel($ordem);
            if (self::status($ordem) === 'cancelado') {
                throw new RuntimeException('Reabra o atendimento cancelado antes de alterar seus equipamentos.');
            }
            $equipamento = $this->linha('equipamentos_os', 'id_equipamento', $idEquipamento);
            if ($equipamento === null || (int) $equipamento['id_ordem'] !== $id) {
                throw new InvalidArgumentException('O equipamento não pertence a este atendimento.');
            }
            if (! empty($equipamento['deleted_at'])) {
                return;
            }
            $agora = date('Y-m-d H:i:s');
            $this->atualizar('equipamentos_os', 'id_equipamento', $idEquipamento, ['deleted_at' => $agora, 'updated_at' => $agora]);
            $this->atualizar('ordens_de_servicos', 'id_ordem', $id, ['versao' => (int) $ordem['versao'] + 1, 'updated_at' => $agora]);
            $this->evento($id, 'equipamento_removido', self::status($ordem), self::status($ordem), $idLogin, '', $equipamento['equipamento']);
        });
    }

    /** Retorna metadados dos arquivos para remoção privada segura somente após o commit. */
    public function excluirDefinitivamente(int $id, int $idLogin, string $numero): array
    {
        return $this->transacao(function () use ($id, $idLogin, $numero): array {
            $this->validaUsuario($idLogin);
            $ordem = $this->ordem($id, true);
            if (! $this->excluida($ordem)) {
                throw new RuntimeException('Envie o atendimento à lixeira antes de excluir definitivamente.');
            }
            $identificacao = $ordem['numero'] ?: (string) $id;
            if (! hash_equals($identificacao, trim($numero))) {
                throw new InvalidArgumentException('Digite exatamente o número do atendimento para confirmar.');
            }
            if ($this->temRecebimentos($id)
                || $this->db->table('saida_de_mercadorias')->where('id_ordem', $id)->countAllResults() > 0
                || $this->db->table('contas_a_receber')->where('id_ordem', $id)->countAllResults() > 0) {
                throw new RuntimeException('O atendimento possui registros financeiros ou consumo de material e deve permanecer preservado na lixeira.');
            }
            $anexos = $this->db->table('anexos_os')->where('id_ordem', $id)->get()->getResultArray();
            $this->evento($id, 'expurgado', self::status($ordem), null, $idLogin, '', 'Exclusão definitiva confirmada: ' . $identificacao);
            if (! $this->db->table('anexos_os')->where('id_ordem', $id)->delete()
                || ! $this->db->table('ordens_de_servicos')->where('id_ordem', $id)->delete()) {
                throw new RuntimeException('Não foi possível excluir o atendimento.');
            }
            return $anexos;
        });
    }

    private function preparaOrdem(array $dados, ?array $atual, array $itens, int $idLogin): array
    {
        $cliente = $this->id($dados['id_cliente'] ?? ($atual['id_cliente'] ?? null));
        if ($this->linha('clientes', 'id_cliente', $cliente) === null) {
            throw new InvalidArgumentException('Selecione um cliente cadastrado.');
        }
        $vendedor = $this->id($dados['id_vendedor'] ?? ($atual['id_vendedor'] ?? null), true);
        if ($vendedor === null) {
            $geral = $this->db->table('vendedores')->where('nome', 'GERAL')->where('status !=', 'Removido')->get()->getRowArray();
            $vendedor = (int) ($geral['id_vendedor'] ?? 0);
        }
        $cadastroVendedor = $vendedor > 0 ? $this->linha('vendedores', 'id_vendedor', $vendedor) : null;
        if ($cadastroVendedor === null || (($cadastroVendedor['status'] ?? '') === 'Removido' && $vendedor !== (int) ($atual['id_vendedor'] ?? 0))) {
            throw new InvalidArgumentException('Selecione um vendedor cadastrado.');
        }
        $tecnico = $this->id(array_key_exists('id_tecnico', $dados) ? $dados['id_tecnico'] : ($atual['id_tecnico'] ?? null), true);
        $cadastroTecnico = $tecnico === null ? null : $this->linha('tecnicos', 'id_tecnico', $tecnico);
        if ($tecnico !== null && ($cadastroTecnico === null || (($cadastroTecnico['status'] ?? '') !== 'Ativo' && $tecnico !== (int) ($atual['id_tecnico'] ?? 0)))) {
            throw new InvalidArgumentException('Selecione um técnico cadastrado ou deixe a definir.');
        }
        $legado = $atual !== null && empty($atual['status_operacional']);
        $ordem = [
            'id_cliente' => $cliente, 'id_vendedor' => $vendedor, 'id_tecnico' => $tecnico,
            'status_operacional' => $atual === null ? 'aguardando_aprovacao' : self::status($atual),
            'desconto_tipo' => $this->opcao($dados['desconto_tipo'] ?? ($atual['desconto_tipo'] ?? 'valor'), ['nenhum', 'valor', 'percentual']),
            'desconto_informado' => OrcamentoCalculo::decimal($dados['desconto_informado'] ?? ($legado ? $atual['desconto'] : ($atual['desconto_informado'] ?? 0)), 2),
            'frete' => OrcamentoCalculo::decimal($dados['frete'] ?? ($atual['frete'] ?? 0), 2),
            'outros' => OrcamentoCalculo::decimal($dados['outros'] ?? ($atual['outros'] ?? 0), 2),
            'entrada_necessaria' => $this->booleano($dados['entrada_necessaria'] ?? ($atual['entrada_necessaria'] ?? 0)),
            'canal_de_venda' => $this->opcao($dados['canal_de_venda'] ?? ($atual['canal_de_venda'] ?? 'Presencial'), ['Presencial', 'Remoto']),
        ];
        $ordem['valor_entrada'] = $ordem['entrada_necessaria']
            ? OrcamentoCalculo::decimal($dados['valor_entrada'] ?? ($atual['valor_entrada'] ?? 0), 2) : '0.00';
        foreach (self::TEXTOS as $campo => $limite) {
            $ordem[$campo] = $this->texto($dados[$campo] ?? ($atual[$campo] ?? ''), $limite);
        }
        if (preg_match('/\d/', $ordem['execucao_telefone']) !== 1) {
            $ordem['execucao_telefone'] = '';
        }
        foreach (['validade_orcamento', 'previsao_conclusao'] as $campo) {
            $ordem[$campo] = $this->data($dados[$campo] ?? ($atual[$campo] ?? null));
        }
        $ordem['execucao_prevista'] = $this->data($dados['execucao_prevista'] ?? ($atual['execucao_prevista'] ?? null), true);
        $externa = count(array_filter($itens, static fn (array $item): bool => $item['tipo_execucao'] !== 'interna' || (int) $item['necessita_instalacao'] === 1)) > 0;
        if (! $externa) {
            foreach (array_keys($ordem) as $campo) {
                if (strpos($campo, 'execucao_') === 0) {
                    $ordem[$campo] = null;
                }
            }
        }
        if ($atual === null) {
            $ordem['created_by'] = $idLogin;
            $ordem['id_atendente'] = $idLogin;
        }

        return $ordem;
    }

    private function preparaItens(array $dados, ?array $ordem): array
    {
        $anteriores = [];
        foreach ($ordem === null ? [] : $this->itens((int) $ordem['id_ordem']) as $item) {
            $anteriores[(int) $item['id_servico']] = $item;
        }
        $itens = [];
        $vistos = [];
        foreach ($dados as $entrada) {
            if (! is_array($entrada)) {
                throw new InvalidArgumentException('O serviço informado é inválido.');
            }
            $id = $this->id($entrada['id_servico'] ?? null, true);
            if ($id !== null && (! isset($anteriores[$id]) || isset($vistos[$id]))) {
                throw new InvalidArgumentException('Um serviço foi repetido ou não pertence ao atendimento.');
            }
            $antigo = $id === null ? null : $anteriores[$id];
            $catalogoId = $this->id($entrada['id_servico_catalogo'] ?? ($antigo['id_servico_catalogo'] ?? null), true);
            $trocaCatalogo = $antigo === null || $catalogoId !== $this->id($antigo['id_servico_catalogo'] ?? null, true);
            $base = $antigo;
            if ($trocaCatalogo) {
                $catalogo = $catalogoId === null ? null : $this->linha('servicos_mao_de_obra', 'id_servico', $catalogoId);
                if ($catalogo === null || (int) $catalogo['ativo'] !== 1) {
                    throw new InvalidArgumentException('Adicione um serviço ativo do catálogo.');
                }
                $base = [
                    'nome' => $catalogo['nome'], 'descricao' => $catalogo['descricao'], 'valor' => $catalogo['valor'],
                    'valor_catalogo' => $catalogo['valor'], 'tipo_preco' => $catalogo['tipo_preco'],
                    'unidade' => $catalogo['unidade'], 'largura' => $catalogo['largura_padrao'], 'altura' => $catalogo['altura_padrao'],
                    'unidade_dimensao' => $catalogo['unidade_dimensao'], 'tipo_execucao' => $catalogo['tipo_execucao'],
                    'arte' => $catalogo['arte_padrao'], 'necessita_instalacao' => $catalogo['necessita_instalacao'],
                    'quantidade' => 1, 'cortesia' => 0,
                ];
            }
            $item = array_intersect_key($base, array_flip([
                'nome', 'descricao', 'valor', 'valor_catalogo', 'quantidade', 'tipo_preco', 'unidade',
                'largura', 'altura', 'unidade_dimensao', 'tipo_execucao', 'arte', 'necessita_instalacao', 'cortesia',
            ]));
            foreach (['valor', 'quantidade', 'tipo_preco', 'unidade', 'largura', 'altura', 'unidade_dimensao', 'tipo_execucao', 'arte', 'necessita_instalacao', 'cortesia'] as $campo) {
                if (array_key_exists($campo, $entrada) && ($entrada[$campo] !== '' || in_array($campo, ['largura', 'altura'], true))) {
                    $item[$campo] = $entrada[$campo];
                }
            }
            $item['nome'] = $this->texto($item['nome'], 128);
            $item['descricao'] = $this->texto($item['descricao'] ?? '', 1024);
            $item['unidade'] = $this->texto($item['unidade'] ?? 'un', 16);
            $item['tipo_execucao'] = $this->opcao($item['tipo_execucao'] ?? 'interna', ['interna', 'externa', 'mista']);
            $item['arte'] = $this->opcao($item['arte'] ?? 'nao_necessita', ['nao_necessita', 'cliente', 'grafica']);
            $item['necessita_instalacao'] = $this->booleano($item['necessita_instalacao'] ?? 0);
            $item['id_servico_catalogo'] = $catalogoId;
            $item = OrcamentoCalculo::item($item);
            if ($id !== null) {
                $vistos[$id] = true;
                $item['id_servico'] = $id;
                if ($this->itemConsumido($id)) {
                    foreach (['id_servico_catalogo', 'quantidade', 'largura', 'altura', 'unidade_dimensao', 'tipo_preco'] as $campo) {
                        if ((string) ($item[$campo] ?? '') !== (string) ($antigo[$campo] ?? '')) {
                            throw new RuntimeException('Um serviço com consumo registrado deve preservar catálogo, quantidade e medidas.');
                        }
                    }
                }
            }
            $itens[] = $item;
        }
        foreach ($anteriores as $id => $item) {
            if (! isset($vistos[$id]) && $this->itemConsumido($id)) {
                throw new RuntimeException('Não remova um serviço com consumo de material registrado.');
            }
        }
        return $itens;
    }

    private function salvaItens(int $id, array $itens, string $agora): void
    {
        $mantidos = [];
        foreach ($itens as $item) {
            $idItem = $item['id_servico'] ?? null;
            unset($item['id_servico'], $item['total'], $item['subtotal']);
            $item['id_ordem'] = $id;
            $item['updated_at'] = $agora;
            $item['desconto'] = '0.00';
            if ($idItem === null) {
                $item += ['created_at' => $agora, 'deleted_at' => null, 'removido_at' => null];
                $idItem = $this->inserir('servicos_mao_de_obra_da_os', $item);
            } else {
                $this->atualizar('servicos_mao_de_obra_da_os', 'id_servico', (int) $idItem, $item);
            }
            $mantidos[] = $idItem;
        }
        foreach ($this->itens($id) as $anterior) {
            if (! in_array((int) $anterior['id_servico'], array_map('intval', $mantidos), true)) {
                $this->atualizar('servicos_mao_de_obra_da_os', 'id_servico', (int) $anterior['id_servico'], ['removido_at' => $agora, 'updated_at' => $agora]);
            }
        }
    }

    private function salvaParcelas(int $id, $entradas, string $total, string $agora, bool $novo): void
    {
        if (! is_array($entradas) || count($entradas) > 120) {
            throw new InvalidArgumentException('Informe no máximo 120 parcelas válidas.');
        }
        $anteriores = $this->parcelas($id);
        if ($entradas === []) {
            if (! $novo) {
                $entradas = $anteriores;
            } else {
                $forma = $this->db->table('formas_de_pagamento')->where('nome', 'Dinheiro')->where('disponivel_servicos', 1)->get()->getRowArray()
                    ?? $this->db->table('formas_de_pagamento')->where('disponivel_servicos', 1)->orderBy('id_forma')->get()->getRowArray();
                if ($forma === null) {
                    throw new InvalidArgumentException('Cadastre uma forma de pagamento antes de salvar.');
                }
                $entradas = [['valor_da_parcela' => $total, 'data_de_vencimento' => substr($agora, 0, 10), 'forma_de_pagamento' => $forma['nome']]];
            }
        }
        $mapa = array_column($anteriores, null, 'id_parcela');
        $soma = '0.00';
        $parcelas = [];
        $vistos = [];
        foreach ($entradas as $entrada) {
            if (! is_array($entrada)) {
                throw new InvalidArgumentException('Informe uma parcela válida.');
            }
            $idParcela = $this->id($entrada['id_parcela'] ?? null, true);
            if ($idParcela !== null && (! isset($mapa[$idParcela]) || isset($vistos[$idParcela]))) {
                throw new InvalidArgumentException('A parcela foi repetida ou não pertence ao atendimento.');
            }
            $valor = OrcamentoCalculo::decimal($entrada['valor_da_parcela'] ?? ($entrada['valor'] ?? ''), 2);
            $forma = $this->texto($entrada['forma_de_pagamento'] ?? '', 128);
            $formaCadastrada = $this->db->table('formas_de_pagamento')->where('nome', $forma)->get()->getRowArray();
            if ($formaCadastrada === null) {
                throw new InvalidArgumentException('Selecione uma forma de pagamento cadastrada.');
            }
            $mantemFormaAnterior = $idParcela !== null && $mapa[$idParcela]['forma_de_pagamento'] === $forma;
            if ((int) $formaCadastrada['disponivel_servicos'] !== 1 && ! $mantemFormaAnterior) {
                throw new InvalidArgumentException('Selecione uma forma de pagamento habilitada para serviços.');
            }
            $vencimento = $this->data($entrada['data_de_vencimento'] ?? null);
            if ($vencimento === null) {
                throw new InvalidArgumentException('Informe o vencimento de cada parcela.');
            }
            if ($idParcela !== null) {
                $vistos[$idParcela] = true;
                $pago = $this->db->table('pagamentos_do_cliente')->selectSum('valor', 'pago')
                    ->where('id_parcela', $idParcela)->where('estornado_at', null)->get()->getRowArray();
                if (bccomp((string) ($pago['pago'] ?? '0.00'), $valor, 2) > 0) {
                    throw new RuntimeException('Uma parcela não pode ficar abaixo do valor recebido nela.');
                }
            }
            $parcelas[] = [
                'id_parcela' => $idParcela, 'data_de_vencimento' => $vencimento, 'valor_da_parcela' => $valor,
                'forma_de_pagamento' => $forma, 'observacoes' => $this->texto($entrada['observacoes'] ?? '', 512),
            ];
            $soma = bcadd($soma, $valor, 2);
        }
        if (bccomp($soma, $total, 2) !== 0) {
            throw new InvalidArgumentException('A soma das parcelas deve ser igual ao total. Atualize a condição de pagamento.');
        }
        foreach ($mapa as $idParcela => $parcela) {
            if (! isset($vistos[$idParcela]) && $this->db->table('pagamentos_do_cliente')->where('id_parcela', $idParcela)->countAllResults() > 0) {
                throw new RuntimeException('Preserve a parcela que possui recebimentos registrados, inclusive estornados.');
            }
        }
        $pagamento = $this->db->table('pagamentos_os')->where('id_ordem', $id)->get()->getRowArray();
        $plano = ['tipo' => count($parcelas) > 1 ? 'Parcelado' : 'À Vista', 'updated_at' => $agora];
        if ($pagamento === null) {
            $idPagamento = $this->inserir('pagamentos_os', $plano + ['id_ordem' => $id, 'created_at' => $agora, 'deleted_at' => null]);
        } else {
            $idPagamento = (int) $pagamento['id_pagamento'];
            $this->atualizar('pagamentos_os', 'id_pagamento', $idPagamento, $plano);
        }
        foreach ($parcelas as $parcela) {
            $idParcela = $parcela['id_parcela'];
            unset($parcela['id_parcela']);
            $parcela += ['id_pagamento' => $idPagamento, 'updated_at' => $agora];
            if ($idParcela === null) {
                $this->inserir('parcelas_do_pagamento_os', $parcela + ['created_at' => $agora, 'deleted_at' => null, 'removido_at' => null]);
            } else {
                $this->atualizar('parcelas_do_pagamento_os', 'id_parcela', $idParcela, $parcela);
            }
        }
        foreach ($mapa as $idParcela => $parcela) {
            if (! isset($vistos[$idParcela])) {
                $this->atualizar('parcelas_do_pagamento_os', 'id_parcela', (int) $idParcela, ['removido_at' => $agora, 'updated_at' => $agora]);
            }
        }
    }

    private function alteraStatus(array $ordem, string $novo, int $idLogin, string $motivo, string $observacoes, bool $incrementa = true, ?string $evento = null): void
    {
        $novo = $this->validaStatus($novo);
        $anterior = self::status($ordem);
        if ($anterior === $novo) {
            return;
        }
        $this->validaTransicao($ordem, $novo, $motivo);
        $agora = date('Y-m-d H:i:s');
        $dados = ['status_operacional' => $novo, 'situacao' => $this->situacaoLegada($novo), 'updated_at' => $agora];
        // Migrar o status de uma OS antiga não pode zerar seu desconto já combinado.
        if (empty($ordem['status_operacional'])) {
            $dados['desconto_tipo'] = 'valor';
            $dados['desconto_informado'] = $ordem['desconto'];
        }
        if ($incrementa) {
            $dados['versao'] = (int) $ordem['versao'] + 1;
        }
        if ($novo === 'concluido') {
            $dados['data_de_saida'] = substr($agora, 0, 10);
            $dados['hora_de_saida'] = substr($agora, 11);
        }
        $this->atualizar('ordens_de_servicos', 'id_ordem', (int) $ordem['id_ordem'], $dados);
        $this->evento((int) $ordem['id_ordem'], $evento ?? ($novo === 'cancelado' ? 'cancelado' : 'status_alterado'), $anterior, $novo, $idLogin, $motivo, $observacoes);
    }

    private function validaTransicao(array $ordem, string $novo, string $motivo): void
    {
        if ($novo === 'cancelado' && trim($motivo) === '') {
            throw new InvalidArgumentException('Informe o motivo do cancelamento.');
        }
        if ($novo === 'instalacao_agendada' && (empty($ordem['execucao_endereco']) || empty($ordem['execucao_prevista']))) {
            throw new InvalidArgumentException('Informe o endereço e a data prevista para agendar a instalação.');
        }
    }

    private function sincronizaConta(array $ordem): void
    {
        $totais = self::totais($ordem, $this->itens((int) $ordem['id_ordem']));
        $ativa = ! $this->excluida($ordem) && ! in_array(self::status($ordem), ['em_elaboracao', 'aguardando_aprovacao', 'cancelado'], true);
        $parcelas = $this->parcelas((int) $ordem['id_ordem']);
        $vencimento = $parcelas[0]['data_de_vencimento'] ?? null;
        // Uma entrada recebida antes da aprovação mantém seu saldo a receber exigível.
        if (! $ativa && ! $this->excluida($ordem) && self::status($ordem) !== 'cancelado' && $this->temRecebimentos((int) $ordem['id_ordem'])) {
            $ativa = true;
        }
        $this->recebimentos->sincronizarConta((int) $ordem['id_ordem'], $totais['total'], $vencimento, (int) $ordem['id_cliente'], $ativa);
    }

    private function evento(int $id, string $evento, ?string $anterior, ?string $novo, int $idLogin, string $motivo = '', string $observacoes = ''): void
    {
        $this->inserir('ordens_de_servicos_historico', [
            'id_ordem' => $id, 'evento' => $evento, 'status_anterior' => $anterior, 'status_novo' => $novo,
            'id_login' => $idLogin, 'motivo' => $this->texto($motivo, 8000),
            'observacoes' => $this->texto($observacoes, 8000), 'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function itens(int $id): array
    {
        return $this->db->table('servicos_mao_de_obra_da_os')->where('id_ordem', $id)->where('removido_at', null)
            ->orderBy('id_servico')->get()->getResultArray();
    }

    private function parcelas(int $id): array
    {
        return $this->db->table('parcelas_do_pagamento_os p')->select('p.*')
            ->join('pagamentos_os plano', 'plano.id_pagamento = p.id_pagamento')->where('plano.id_ordem', $id)
            ->where('p.removido_at', null)->orderBy('p.data_de_vencimento')->orderBy('p.id_parcela')->get()->getResultArray();
    }

    private function itemConsumido(int $id): bool
    {
        return $this->db->table('saida_de_mercadorias')->where('id_servico_os', $id)->countAllResults() > 0;
    }

    private function temRecebimentos(int $id): bool
    {
        return $this->db->table('pagamentos_do_cliente')->where('id_ordem', $id)->countAllResults() > 0;
    }

    private function ordem(int $id, bool $bloqueada = false): array
    {
        $ordem = $this->linha('ordens_de_servicos', 'id_ordem', $id, $bloqueada);
        if ($id <= 0 || $ordem === null) {
            throw new RuntimeException('Atendimento não encontrado.');
        }
        return $ordem;
    }

    private function linha(string $tabela, string $chave, int $id, bool $bloqueada = false): ?array
    {
        $sql = $this->db->table($tabela)->where($chave, $id)->getCompiledSelect();
        if ($bloqueada && $this->db->getPlatform() !== 'SQLite3') {
            $sql .= ' FOR UPDATE';
        }
        return $this->db->query($sql)->getRowArray();
    }

    private function validaUsuario(int $id): void
    {
        if ($id <= 0 || $this->linha('login', 'id_login', $id) === null) {
            throw new RuntimeException('Usuário responsável não encontrado.');
        }
    }

    private function exigeDisponivel(array $ordem): void
    {
        if ($this->excluida($ordem)) {
            throw new RuntimeException('Restaure o atendimento da lixeira antes de alterá-lo.');
        }
    }

    private function excluida(array $ordem): bool
    {
        return ! empty($ordem['deleted_at']) && $ordem['deleted_at'] !== '0000-00-00 00:00:00';
    }

    private function situacaoLegada(string $status): string
    {
        return in_array($status, ['em_elaboracao', 'aguardando_aprovacao'], true) ? 'Em aberto'
            : ($status === 'concluido' ? 'Concretizada' : ($status === 'cancelado' ? 'Cancelada' : 'Em andamento'));
    }

    private function validaStatus($status): string
    {
        if (! is_string($status) || ! isset(self::STATUS[$status])) {
            throw new InvalidArgumentException('Selecione um status válido.');
        }
        return $status;
    }

    private function opcao($valor, array $opcoes): string
    {
        if (! is_string($valor) || ! in_array($valor, $opcoes, true)) {
            throw new InvalidArgumentException('Uma das opções selecionadas é inválida.');
        }
        return $valor;
    }

    private function booleano($valor): int
    {
        if (! in_array($valor, [0, 1, '0', '1', true, false], true)) {
            throw new InvalidArgumentException('Informe uma opção sim ou não válida.');
        }
        return (int) $valor;
    }

    private function texto($valor, int $limite): string
    {
        if ($valor !== null && ! is_scalar($valor)) {
            throw new InvalidArgumentException('Informe um texto válido.');
        }
        $texto = trim((string) $valor);
        if (mb_strlen($texto) > $limite) {
            throw new InvalidArgumentException('Um dos textos excede o limite de ' . $limite . ' caracteres.');
        }
        return $texto;
    }

    private function id($valor, bool $opcional = false): ?int
    {
        if ($opcional && ($valor === null || $valor === '' || $valor === 0 || $valor === '0')) {
            return null;
        }
        if (filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]) === false) {
            throw new InvalidArgumentException('Um dos cadastros selecionados é inválido.');
        }
        return (int) $valor;
    }

    private function data($valor, bool $hora = false): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }
        $texto = $this->texto($valor, 19);
        if ($hora) {
            $texto = str_replace('T', ' ', $texto);
            if (strlen($texto) === 16) {
                $texto .= ':00';
            }
        }
        $formato = $hora ? 'Y-m-d H:i:s' : 'Y-m-d';
        $data = DateTimeImmutable::createFromFormat('!' . $formato, $texto);
        if ($data === false || $data->format($formato) !== $texto || substr($texto, 0, 4) < '1900' || substr($texto, 0, 4) > '2100') {
            throw new InvalidArgumentException('Informe uma data válida entre 1900 e 2100.');
        }
        return $texto;
    }

    private function hashCriacao(array $dados): string
    {
        $campos = array_merge(array_keys(self::TEXTOS), [
            'id_cliente', 'id_vendedor', 'id_tecnico', 'itens', 'parcelas', 'status_operacional',
            'desconto_tipo', 'desconto_informado', 'frete', 'outros', 'entrada_necessaria', 'valor_entrada',
            'canal_de_venda', 'validade_orcamento', 'previsao_conclusao', 'execucao_prevista', 'motivo',
        ]);
        $dados = array_intersect_key($dados, array_flip($campos));
        $ordenar = static function (array $valor) use (&$ordenar): array {
            if (! array_is_list($valor)) {
                ksort($valor);
            }
            foreach ($valor as &$item) {
                if (is_array($item)) {
                    $item = $ordenar($item);
                }
            }
            return $valor;
        };
        return hash('sha256', json_encode($ordenar($dados), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    private function inserir(string $tabela, array $dados): int
    {
        if (! $this->db->table($tabela)->insert($dados)) {
            throw new RuntimeException('Não foi possível salvar os dados do atendimento.');
        }
        return (int) $this->db->insertID();
    }

    private function atualizar(string $tabela, string $chave, int $id, array $dados): void
    {
        if (! $this->db->table($tabela)->where($chave, $id)->update($dados)) {
            throw new RuntimeException('Não foi possível atualizar os dados do atendimento.');
        }
    }

    private function transacao(callable $acao)
    {
        if (! $this->db->transStatus()) {
            throw new RuntimeException('A transação anterior falhou e deve ser desfeita antes de continuar.');
        }
        $savepoint = $this->db->transDepth > 0 ? 'atendimento_' . bin2hex(random_bytes(8)) : null;
        if ($savepoint !== null ? ! $this->db->query('SAVEPOINT ' . $savepoint) : ! $this->db->transBegin()) {
            throw new RuntimeException('Não foi possível iniciar a transação do atendimento.');
        }
        try {
            $resultado = $acao();
            if (! $this->db->transStatus()
                || ($savepoint !== null ? ! $this->db->query('RELEASE SAVEPOINT ' . $savepoint) : ! $this->db->transCommit())) {
                throw new RuntimeException('Não foi possível concluir a transação do atendimento.');
            }
            return $resultado;
        } catch (Throwable $exception) {
            if ($savepoint !== null) {
                $this->db->query('ROLLBACK TO SAVEPOINT ' . $savepoint);
                $this->db->query('RELEASE SAVEPOINT ' . $savepoint);
            } else {
                $this->db->transRollback();
                $this->db->resetTransStatus();
            }
            throw $exception;
        }
    }
}
