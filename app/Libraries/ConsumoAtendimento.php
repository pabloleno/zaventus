<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class ConsumoAtendimento
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function registrar(int $idOrdem, array $dados, int $idLogin): int
    {
        return $this->movimentar($dados, $idLogin, false, $idOrdem);
    }

    public function estornar(int $idOrdem, int $idSaida, int $idLogin, string $motivo): void
    {
        $this->reverter($idSaida, $idLogin, $motivo, false, $idOrdem);
    }

    public function registrarManual(array $dados, int $idLogin, bool $reposicao = false): int
    {
        return $this->movimentar($dados, $idLogin, $reposicao, null);
    }

    public function estornarManual(int $idMovimento, int $idLogin, string $motivo, bool $reposicao = false): void
    {
        $this->reverter($idMovimento, $idLogin, $motivo, $reposicao, null);
    }

    private function movimentar(array $dados, int $idLogin, bool $reposicao, ?int $idOrdem): int
    {
        $idProduto = $this->id($dados['id_produto'] ?? null);
        $idServico = $this->id($dados['id_servico_os'] ?? null, true);
        $quantidade = OrcamentoCalculo::decimal($dados['quantidade'] ?? '');
        if (bccomp($quantidade, '0', 4) <= 0) {
            throw new InvalidArgumentException('A quantidade deve ser maior que zero.');
        }
        $observacoes = $this->texto($dados['observacoes'] ?? '', 512);
        $chave = $this->texto($dados['chave_operacao'] ?? '', 64);
        if (preg_match('/\A[A-Za-z0-9._:-]{16,64}\z/', $chave) !== 1) {
            throw new InvalidArgumentException('Identificação da operação inválida. Reabra o formulário.');
        }
        if ($idOrdem === null && $idServico !== null) {
            throw new InvalidArgumentException('O serviço deve ser informado na ficha do atendimento.');
        }

        $savepoint = $this->iniciar();
        try {
            $this->autor($idLogin);
            $ordem = $idOrdem !== null ? $this->ordem($idOrdem) : null;
            $tabela = $reposicao ? 'reposicoes' : 'saida_de_mercadorias';
            $campoId = $reposicao ? 'id_reposicao' : 'id_saida';
            $anterior = $this->db->table($tabela)->where('chave_operacao', $chave)->get()->getRowArray();
            if ($anterior !== null) {
                if ((int) $anterior['id_produto'] !== $idProduto
                    || (! isset($anterior['id_ordem']) ? null : (int) $anterior['id_ordem']) !== $idOrdem
                    || (! isset($anterior['id_servico_os']) ? null : (int) $anterior['id_servico_os']) !== $idServico
                    || bccomp($anterior['quantidade'], $quantidade, 4) !== 0
                    || (string) $anterior['observacoes'] !== $observacoes
                    || (int) $anterior['created_by'] !== $idLogin) {
                    throw new InvalidArgumentException('Esta operação já foi usada com outros dados.');
                }
                $this->concluir($savepoint);
                return (int) $anterior[$campoId];
            }
            if ($ordem !== null && (! $this->semData($ordem['deleted_at'] ?? null) || AtendimentoGrafica::status($ordem) === 'cancelado')) {
                throw new InvalidArgumentException('Reabra ou restaure o atendimento antes de registrar consumo.');
            }
            if ($idServico !== null) {
                $servico = $this->bloquear('servicos_mao_de_obra_da_os', 'id_servico', $idServico);
                if ($servico === null || (int) $servico['id_ordem'] !== $idOrdem
                    || ! $this->semData($servico['removido_at'] ?? null) || ! $this->semData($servico['deleted_at'] ?? null)) {
                    throw new InvalidArgumentException('Selecione um serviço ativo deste atendimento.');
                }
            }
            $produto = $this->bloquear('produtos', 'id_produto', $idProduto);
            if ($produto === null || (int) ($produto['ativo'] ?? 1) !== 1 || ! $this->semData($produto['deleted_at'] ?? null)) {
                throw new InvalidArgumentException('Selecione um material ativo.');
            }
            $saldo = OrcamentoCalculo::decimal($produto['quantidade']);
            if (! $reposicao && bccomp($saldo, $quantidade, 4) < 0) {
                throw new InvalidArgumentException('Estoque insuficiente para esta saída de material.');
            }
            $novoSaldo = OrcamentoCalculo::decimal($reposicao ? bcadd($saldo, $quantidade, 4) : bcsub($saldo, $quantidade, 4));
            $agora = date('Y-m-d H:i:s');
            $movimento = [
                'id_produto' => $idProduto, 'quantidade' => $quantidade, 'observacoes' => $observacoes,
                'data' => substr($agora, 0, 10), 'hora' => substr($agora, 11), 'created_at' => $agora, 'updated_at' => $agora,
                'deleted_at' => null, 'created_by' => $idLogin, 'chave_operacao' => $chave,
            ];
            if (! $reposicao) {
                $movimento += ['id_ordem' => $idOrdem, 'id_servico_os' => $idServico];
            }
            $id = $this->inserir($reposicao ? 'reposicoes' : 'saida_de_mercadorias', $movimento);
            $this->atualizar('produtos', 'id_produto', $idProduto, ['quantidade' => $novoSaldo, 'updated_at' => $agora]);
            if ($ordem !== null) {
                $this->evento($ordem, 'consumo', $idLogin, '', 'Saída #' . $id . ': ' . $quantidade . ' ' . $produto['unidade'] . ' de ' . $produto['nome'] . '.', $agora);
            }
            $this->concluir($savepoint);
            return $id;
        } catch (Throwable $exception) {
            $this->desfazer($savepoint);
            throw $exception;
        }
    }

    private function reverter(int $idMovimento, int $idLogin, string $motivo, bool $reposicao, ?int $idOrdem): void
    {
        $motivo = $this->texto($motivo, 512);
        if ($motivo === '') {
            throw new InvalidArgumentException('Informe o motivo do estorno.');
        }
        $savepoint = $this->iniciar();
        try {
            $this->autor($idLogin);
            $ordem = $idOrdem !== null ? $this->ordem($idOrdem) : null;
            $tabela = $reposicao ? 'reposicoes' : 'saida_de_mercadorias';
            $campoId = $reposicao ? 'id_reposicao' : 'id_saida';
            $movimento = $this->bloquear($tabela, $campoId, $idMovimento);
            if ($movimento === null) {
                throw new InvalidArgumentException('Movimentação não encontrada.');
            }
            $vinculo = empty($movimento['id_ordem']) ? null : (int) $movimento['id_ordem'];
            if ($vinculo !== $idOrdem) {
                throw new InvalidArgumentException('Estorne o consumo na ficha do atendimento correspondente.');
            }
            if (! $this->semData($movimento['estornado_at']) || ! $this->semData($movimento['deleted_at'])) {
                $this->concluir($savepoint);
                return;
            }
            $produto = $this->bloquear('produtos', 'id_produto', (int) $movimento['id_produto']);
            if ($produto === null) {
                throw new RuntimeException('O material da movimentação não foi encontrado.');
            }
            $saldo = OrcamentoCalculo::decimal($produto['quantidade']);
            $quantidade = OrcamentoCalculo::decimal($movimento['quantidade']);
            if ($reposicao && bccomp($saldo, $quantidade, 4) < 0) {
                throw new InvalidArgumentException('A reposição não pode ser estornada: parte do saldo já foi consumida.');
            }
            $novoSaldo = OrcamentoCalculo::decimal($reposicao ? bcsub($saldo, $quantidade, 4) : bcadd($saldo, $quantidade, 4));
            $agora = date('Y-m-d H:i:s');
            $alteracoes = ['estornado_at' => $agora, 'estornado_by' => $idLogin, 'estorno_motivo' => $motivo];
            $alteracoes['updated_at'] = $agora;
            $this->atualizar($tabela, $campoId, $idMovimento, $alteracoes);
            $this->atualizar('produtos', 'id_produto', (int) $movimento['id_produto'], ['quantidade' => $novoSaldo, 'updated_at' => $agora]);
            if ($ordem !== null) {
                $this->evento($ordem, 'estorno_consumo', $idLogin, $motivo, 'Estornada a saída #' . $idMovimento . ': ' . $quantidade . ' ' . $produto['unidade'] . ' de ' . $produto['nome'] . '.', $agora);
            }
            $this->concluir($savepoint);
        } catch (Throwable $exception) {
            $this->desfazer($savepoint);
            throw $exception;
        }
    }

    private function ordem(int $idOrdem): array
    {
        $ordem = $idOrdem > 0 ? $this->bloquear('ordens_de_servicos', 'id_ordem', $idOrdem) : null;
        if ($ordem === null) {
            throw new InvalidArgumentException('Atendimento não encontrado.');
        }
        return $ordem;
    }

    private function evento(array $ordem, string $evento, int $idLogin, string $motivo, string $observacoes, string $agora): void
    {
        $status = AtendimentoGrafica::status($ordem);
        $this->inserir('ordens_de_servicos_historico', [
            'id_ordem' => (int) $ordem['id_ordem'], 'evento' => $evento, 'status_anterior' => $status,
            'status_novo' => $status, 'id_login' => $idLogin, 'motivo' => $motivo, 'observacoes' => $observacoes, 'created_at' => $agora,
        ]);
    }

    private function bloquear(string $tabela, string $campo, int $id): ?array
    {
        $sql = $this->db->table($tabela)->where($campo, $id)->getCompiledSelect();
        return $this->db->query($sql . ' FOR UPDATE')->getRowArray();
    }

    private function autor(int $idLogin): void
    {
        if ($idLogin <= 0 || $this->db->table('login')->where('id_login', $idLogin)->get()->getRowArray() === null) {
            throw new InvalidArgumentException('Usuário responsável não encontrado.');
        }
    }

    private function texto($valor, int $limite): string
    {
        if (! is_string($valor) || mb_strlen(trim($valor)) > $limite) {
            throw new InvalidArgumentException('Informe um texto de até ' . $limite . ' caracteres.');
        }
        return trim($valor);
    }

    private function id($valor, bool $opcional = false): ?int
    {
        if ($opcional && in_array($valor, [null, '', 0, '0'], true)) {
            return null;
        }
        if (filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]) === false) {
            throw new InvalidArgumentException('Identificador de material ou serviço inválido.');
        }
        return (int) $valor;
    }

    private function semData(?string $valor): bool
    {
        return $valor === null || $valor === '' || $valor === '0000-00-00 00:00:00';
    }

    private function inserir(string $tabela, array $dados): int
    {
        if (! $this->db->table($tabela)->insert($dados)) {
            throw new RuntimeException('Não foi possível registrar a movimentação de material.');
        }
        return (int) $this->db->insertID();
    }

    private function atualizar(string $tabela, string $campo, int $id, array $dados): void
    {
        if (! $this->db->table($tabela)->where($campo, $id)->update($dados)) {
            throw new RuntimeException('Não foi possível atualizar a movimentação de material.');
        }
    }

    private function iniciar(): ?string
    {
        if (! $this->db->transStatus()) {
            throw new RuntimeException('A transação anterior falhou e deve ser desfeita antes de continuar.');
        }
        $savepoint = $this->db->transDepth > 0 ? 'consumo_' . bin2hex(random_bytes(8)) : null;
        if ($savepoint !== null ? ! $this->db->query('SAVEPOINT ' . $savepoint) : ! $this->db->transBegin()) {
            throw new RuntimeException('Não foi possível iniciar a movimentação de material.');
        }
        return $savepoint;
    }

    private function concluir(?string $savepoint): void
    {
        if (! $this->db->transStatus()
            || ($savepoint !== null ? ! $this->db->query('RELEASE SAVEPOINT ' . $savepoint) : ! $this->db->transCommit())) {
            throw new RuntimeException('Não foi possível concluir a movimentação de material.');
        }
    }

    private function desfazer(?string $savepoint): void
    {
        if ($savepoint !== null) {
            $this->db->query('ROLLBACK TO SAVEPOINT ' . $savepoint);
            $this->db->query('RELEASE SAVEPOINT ' . $savepoint);
        } else {
            $this->db->transRollback();
            $this->db->resetTransStatus();
        }
    }
}
