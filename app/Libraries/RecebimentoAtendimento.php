<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Recebimentos efetivos usam pagamentos_do_cliente; o plano continua nas parcelas da OS.
 * Valores sao somados em centavos inteiros e persistidos como strings DECIMAL(15,2).
 */
class RecebimentoAtendimento
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function resumo(int $idOrdem): array
    {
        $ordem = $this->ordem($idOrdem);
        $total = $this->total($ordem);
        $recebimentos = $this->recebimentos($idOrdem);
        $pago = $this->pago($recebimentos);

        return [
            'total' => self::decimal($total),
            'pago' => self::decimal($pago),
            'saldo' => self::decimal(max(0, $total - $pago)),
            'recebimentos' => $recebimentos,
            'conta' => $this->conta($idOrdem),
        ];
    }

    /**
     * Deve ser chamado na mesma transacao que altera/aprova/cancela a OS.
     * O campo legado valor representa o saldo exigivel para manter os indicadores atuais.
     */
    public function sincronizarConta(int $idOrdem, string $total, ?string $vencimento, int $idCliente, bool $ativa): void
    {
        $ordem = $this->ordem($idOrdem, true);
        if ($idCliente <= 0 || $idCliente !== (int) $ordem['id_cliente']) {
            throw new InvalidArgumentException('O cliente da conta deve ser o cliente do atendimento.');
        }

        $totalCentavos = self::centavos($total);
        $pago = $this->pago($this->recebimentos($idOrdem));
        if ($ativa && $totalCentavos < $pago) {
            throw new RuntimeException('O total nao pode ficar abaixo do valor recebido. Estorne o recebimento antes de reduzir o total.');
        }

        $conta = $this->conta($idOrdem);
        if (! $ativa && $conta === null) {
            return;
        }

        $vencimento = $vencimento ?: ($conta['data_de_vencimento'] ?? date('Y-m-d'));
        $data = DateTimeImmutable::createFromFormat('!Y-m-d', $vencimento);
        if ($data === false || $data->format('Y-m-d') !== $vencimento) {
            throw new InvalidArgumentException('Informe uma data de vencimento valida.');
        }

        $saldo = $ativa ? max(0, $totalCentavos - $pago) : 0;
        $agora = date('Y-m-d H:i:s');
        $dados = [
            'id_ordem' => $idOrdem,
            'id_cliente' => $idCliente,
            'tipo_negocio' => TipoNegocio::SERVICOS,
            'status' => ! $ativa ? 'Cancelada' : ($saldo === 0 ? 'Paga' : 'Aberta'),
            'nome' => $this->descricao($ordem),
            'data_de_vencimento' => $vencimento,
            'valor' => self::decimal($saldo),
            'valor_pago' => self::decimal($pago),
            'updated_at' => $agora,
            'deleted_at' => null,
        ];
        if ($conta === null) {
            $dados['created_at'] = $agora;
            $dados['observacoes'] = 'Saldo vinculado ao atendimento. Os recebimentos sao registrados na ficha do atendimento.';
            $this->inserir('contas_a_receber', $dados);
        } else {
            $this->atualizar('contas_a_receber', 'id_conta', (int) $conta['id_conta'], $dados);
        }
    }

    /**
     * Aceita valor, forma_de_pagamento (ou forma), chave_operacao e caixa/parcela opcionais.
     * A mesma chave com os mesmos dados retorna o recebimento original, inclusive estornado.
     */
    public function registrar(int $idOrdem, array $dados, int $idLogin): int
    {
        $valor = self::centavos($dados['valor'] ?? '');
        if ($valor <= 0) {
            throw new InvalidArgumentException('O valor recebido deve ser maior que zero.');
        }
        $forma = trim((string) ($dados['forma_de_pagamento'] ?? $dados['forma'] ?? ''));
        $chave = trim((string) ($dados['chave_operacao'] ?? ''));
        if (! preg_match('/\A[A-Za-z0-9._:-]{16,64}\z/', $chave)) {
            throw new InvalidArgumentException('A identificacao da operacao e invalida. Reabra o formulario de recebimento.');
        }
        $idCaixa = $this->idOpcional($dados['id_caixa'] ?? null);
        $idParcela = $this->idOpcional($dados['id_parcela'] ?? null);
        $observacoes = trim((string) ($dados['observacoes'] ?? ''));
        if (mb_strlen($observacoes) > 512) {
            throw new InvalidArgumentException('As observacoes devem ter no maximo 512 caracteres.');
        }

        $savepoint = $this->iniciarTransacao();
        try {
            $ordem = $this->ordem($idOrdem, true);
            $this->validarAutor($idLogin);
            $existente = $this->db->table('pagamentos_do_cliente')->where('chave_operacao', $chave)->get()->getRowArray();
            if ($existente !== null) {
                if ((int) $existente['id_ordem'] !== $idOrdem
                    || self::centavos($existente['valor']) !== $valor
                    || (string) $existente['forma_de_pagamento'] !== $forma
                    || (int) $existente['id_caixa'] !== (int) $idCaixa
                    || (int) $existente['id_parcela'] !== (int) $idParcela) {
                    throw new RuntimeException('Esta identificacao de operacao ja foi usada com outros dados.');
                }
                $this->concluirTransacao($savepoint);
                return (int) $existente['id_pagamento'];
            }

            if (! $this->ordemAtiva($ordem)) {
                throw new RuntimeException('Nao e possivel receber em atendimento cancelado ou excluido.');
            }
            if ($forma === '' || $this->db->table('formas_de_pagamento')->where('nome', $forma)
                ->where('disponivel_servicos', 1)->get()->getRowArray() === null) {
                throw new InvalidArgumentException('Selecione uma forma de pagamento cadastrada para servicos.');
            }
            $idCliente = (int) $ordem['id_cliente'];
            if ($idCliente <= 0 || $this->db->table('clientes')->where('id_cliente', $idCliente)->get()->getRowArray() === null) {
                throw new RuntimeException('O atendimento precisa de um cliente valido para registrar o recebimento.');
            }
            if ($idCaixa !== null) {
                $caixa = $this->linhaBloqueada('caixas', 'id_caixa', $idCaixa);
                if ($caixa === null || $caixa['status'] !== 'Aberto') {
                    throw new RuntimeException('O caixa selecionado deve estar aberto.');
                }
            }
            if ($idParcela !== null) {
                $parcela = $this->db->table('parcelas_do_pagamento_os p')
                    ->join('pagamentos_os plano', 'plano.id_pagamento = p.id_pagamento')
                    ->where('p.id_parcela', $idParcela)->where('p.removido_at', null)
                    ->where('plano.id_ordem', $idOrdem)->get()->getRowArray();
                if ($parcela === null) {
                    throw new InvalidArgumentException('A parcela nao pertence a este atendimento.');
                }
            }

            $total = $this->total($ordem);
            $pago = $this->pago($this->recebimentos($idOrdem));
            if ($valor > $total - $pago) {
                throw new RuntimeException('O recebimento excede o saldo restante do atendimento.');
            }
            $this->sincronizarConta($idOrdem, self::decimal($total), null, $idCliente, true);
            $conta = $this->conta($idOrdem);
            $agora = date('Y-m-d H:i:s');
            $idRecebimento = $this->inserir('pagamentos_do_cliente', [
                'id_ordem' => $idOrdem,
                'id_cliente' => $idCliente,
                'id_conta' => (int) $conta['id_conta'],
                'id_parcela' => $idParcela,
                'id_caixa' => $idCaixa,
                'forma_de_pagamento' => $forma,
                'descricao' => 'Recebimento ' . $this->descricao($ordem),
                'valor' => self::decimal($valor),
                'data' => substr($agora, 0, 10),
                'hora' => substr($agora, 11),
                'observacoes' => $observacoes,
                'created_by' => $idLogin,
                'chave_operacao' => $chave,
                'created_at' => $agora,
                'updated_at' => $agora,
                'deleted_at' => null,
            ]);
            if ($idCaixa !== null) {
                $this->inserir('lancamentos', [
                    'id_recebimento' => $idRecebimento,
                    'natureza' => 'recebimento_os',
                    'tipo_negocio' => TipoNegocio::SERVICOS,
                    'descricao' => 'Recebimento ' . $this->descricao($ordem),
                    'valor' => self::decimal($valor),
                    'data' => substr($agora, 0, 10),
                    'hora' => substr($agora, 11),
                    'observacoes' => 'Baixa do atendimento; nao representa uma nova receita de servico.',
                    'id_caixa' => $idCaixa,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                    'deleted_at' => null,
                ]);
            }
            $this->sincronizarConta($idOrdem, self::decimal($total), null, $idCliente, true);
            $this->evento($ordem, 'recebido', $idLogin, '', 'Recebimento #' . $idRecebimento . ': R$ ' . self::decimal($valor) . ' por ' . $forma . '.', $agora);
            $this->concluirTransacao($savepoint);
            return $idRecebimento;
        } catch (Throwable $exception) {
            $this->desfazerTransacao($savepoint);
            throw $exception;
        }
    }

    /**
     * Preserva o recebimento e seu lancamento. Consultas de caixa devem excluir estornados por join.
     */
    public function estornar(int $idRecebimento, int $idLogin, string $motivo): void
    {
        $motivo = trim($motivo);
        if ($motivo === '' || mb_strlen($motivo) > 2000) {
            throw new InvalidArgumentException('Informe o motivo do estorno, com ate 2000 caracteres.');
        }

        $savepoint = $this->iniciarTransacao();
        try {
            $this->validarAutor($idLogin);
            $recebimento = $this->db->table('pagamentos_do_cliente')->where('id_pagamento', $idRecebimento)->get()->getRowArray();
            if ($recebimento === null || (int) ($recebimento['id_ordem'] ?? 0) <= 0) {
                throw new RuntimeException('Recebimento vinculado ao atendimento nao encontrado.');
            }
            // Mesma ordem de locks usada por registrar: atendimento, depois recebimento.
            $idOrdem = (int) $recebimento['id_ordem'];
            $ordem = $this->ordem($idOrdem, true);
            $recebimento = $this->linhaBloqueada('pagamentos_do_cliente', 'id_pagamento', $idRecebimento);
            if ($recebimento === null) {
                throw new RuntimeException('Recebimento nao encontrado.');
            }
            if (empty($recebimento['estornado_at'])) {
                $agora = date('Y-m-d H:i:s');
                $this->atualizar('pagamentos_do_cliente', 'id_pagamento', $idRecebimento, [
                    'estornado_at' => $agora,
                    'estornado_by' => $idLogin,
                    'estorno_motivo' => $motivo,
                    'updated_at' => $agora,
                ]);
                $this->sincronizarConta($idOrdem, self::decimal($this->total($ordem)), null, (int) $ordem['id_cliente'], $this->ordemAtiva($ordem));
                $this->evento($ordem, 'estornado', $idLogin, $motivo, 'Estorno do recebimento #' . $idRecebimento . ': R$ ' . $recebimento['valor'] . '.', $agora);
            }
            $this->concluirTransacao($savepoint);
        } catch (Throwable $exception) {
            $this->desfazerTransacao($savepoint);
            throw $exception;
        }
    }

    private function total(array $ordem): int
    {
        $itens = $this->db->table('servicos_mao_de_obra_da_os')->where('id_ordem', $ordem['id_ordem'])
            ->where('removido_at', null)->get()->getResultArray();
        $resumo = AtendimentoGrafica::totais($ordem, $itens);
        return self::centavos($resumo['total']);
    }

    private function evento(array $ordem, string $evento, int $idLogin, string $motivo, string $observacoes, string $agora): void
    {
        $status = AtendimentoGrafica::status($ordem);
        $this->inserir('ordens_de_servicos_historico', [
            'id_ordem' => (int) $ordem['id_ordem'], 'evento' => $evento,
            'status_anterior' => $status, 'status_novo' => $status, 'id_login' => $idLogin,
            'motivo' => $motivo, 'observacoes' => $observacoes, 'created_at' => $agora,
        ]);
    }

    private function ordem(int $idOrdem, bool $bloquear = false): array
    {
        $ordem = $bloquear ? $this->linhaBloqueada('ordens_de_servicos', 'id_ordem', $idOrdem)
            : $this->db->table('ordens_de_servicos')->where('id_ordem', $idOrdem)->get()->getRowArray();
        if ($idOrdem <= 0 || $ordem === null) {
            throw new RuntimeException('Atendimento nao encontrado.');
        }
        return $ordem;
    }

    private function ordemAtiva(array $ordem): bool
    {
        return (empty($ordem['deleted_at']) || $ordem['deleted_at'] === '0000-00-00 00:00:00')
            && ($ordem['situacao'] ?? '') !== 'Cancelada'
            && ($ordem['status_operacional'] ?? '') !== 'cancelado';
    }

    private function recebimentos(int $idOrdem): array
    {
        return $this->db->table('pagamentos_do_cliente')->where('id_ordem', $idOrdem)
            ->where('deleted_at', null)->orderBy('id_pagamento', 'DESC')->get()->getResultArray();
    }

    private function pago(array $recebimentos): int
    {
        $pago = 0;
        foreach ($recebimentos as $recebimento) {
            if (empty($recebimento['estornado_at'])) {
                $pago += self::centavos($recebimento['valor']);
            }
        }
        return $pago;
    }

    private function conta(int $idOrdem): ?array
    {
        return $this->db->table('contas_a_receber')->where('id_ordem', $idOrdem)->get()->getRowArray();
    }

    private function descricao(array $ordem): string
    {
        return 'Atendimento ' . ($ordem['numero'] ?? $ordem['id_ordem']);
    }

    private function validarAutor(int $idLogin): void
    {
        if ($idLogin <= 0 || $this->db->table('login')->where('id_login', $idLogin)->get()->getRowArray() === null) {
            throw new RuntimeException('Usuario responsavel pelo recebimento nao encontrado.');
        }
    }

    private function linhaBloqueada(string $tabela, string $chave, int $id): ?array
    {
        $sql = $this->db->table($tabela)->where($chave, $id)->getCompiledSelect();
        // SQLite nao oferece FOR UPDATE; MariaDB/MySQL utiliza bloqueio da linha da OS.
        if ($this->db->getPlatform() !== 'SQLite3') {
            $sql .= ' FOR UPDATE';
        }
        return $this->db->query($sql)->getRowArray();
    }

    private function idOpcional($valor): ?int
    {
        if ($valor === null || $valor === '' || $valor === '0' || $valor === 0) {
            return null;
        }
        if (filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]) === false) {
            throw new InvalidArgumentException('Identificador de caixa ou parcela invalido.');
        }
        return (int) $valor;
    }

    private function inserir(string $tabela, array $dados): int
    {
        if (! $this->db->table($tabela)->insert($dados)) {
            throw new RuntimeException('Nao foi possivel registrar a movimentacao financeira.');
        }
        return (int) $this->db->insertID();
    }

    private function atualizar(string $tabela, string $chave, int $id, array $dados): void
    {
        if (! $this->db->table($tabela)->where($chave, $id)->update($dados)) {
            throw new RuntimeException('Nao foi possivel atualizar a movimentacao financeira.');
        }
    }

    private function iniciarTransacao(): ?string
    {
        if (! $this->db->transStatus()) {
            throw new RuntimeException('A transacao anterior falhou e deve ser desfeita antes de continuar.');
        }
        $savepoint = $this->db->transDepth > 0 ? 'recebimento_' . bin2hex(random_bytes(8)) : null;
        if ($savepoint !== null ? ! $this->db->query('SAVEPOINT ' . $savepoint) : ! $this->db->transBegin()) {
            throw new RuntimeException('Nao foi possivel iniciar a transacao financeira.');
        }
        return $savepoint;
    }

    private function concluirTransacao(?string $savepoint): void
    {
        if (! $this->db->transStatus()
            || ($savepoint !== null ? ! $this->db->query('RELEASE SAVEPOINT ' . $savepoint) : ! $this->db->transCommit())) {
            throw new RuntimeException('Nao foi possivel concluir a transacao financeira.');
        }
    }

    private function desfazerTransacao(?string $savepoint): void
    {
        if ($savepoint !== null) {
            $this->db->query('ROLLBACK TO SAVEPOINT ' . $savepoint);
            $this->db->query('RELEASE SAVEPOINT ' . $savepoint);
        } else {
            $this->db->transRollback();
            $this->db->resetTransStatus();
        }
    }

    private static function centavos($valor): int
    {
        if (! is_string($valor) && ! is_int($valor)) {
            throw new InvalidArgumentException('Informe o valor como decimal, sem calculos em ponto flutuante.');
        }
        $valor = trim((string) $valor);
        if (strpos($valor, ',') !== false) {
            if (! preg_match('/\A(?:[0-9]+|[0-9]{1,3}(?:\.[0-9]{3})+),[0-9]{1,2}\z/', $valor)) {
                throw new InvalidArgumentException('Valor monetario invalido.');
            }
            $valor = str_replace(['.', ','], ['', '.'], $valor);
        }
        if (! preg_match('/\A([0-9]{1,13})(?:\.([0-9]{1,2}))?\z/', $valor, $partes)) {
            throw new InvalidArgumentException('Informe um valor positivo com no maximo duas casas decimais.');
        }
        return ((int) $partes[1] * 100) + (int) str_pad($partes[2] ?? '', 2, '0');
    }

    private static function decimal(int $centavos): string
    {
        return (string) intdiv($centavos, 100) . '.' . str_pad((string) ($centavos % 100), 2, '0', STR_PAD_LEFT);
    }
}
