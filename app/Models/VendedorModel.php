<?php

namespace App\Models;

class VendedorModel extends PadraoModel
{
    private const STATUS_REMOVIDO = 'Removido';

    protected $table = 'vendedores';
    protected $primaryKey = 'id_vendedor';
    protected $allowedFields = [
        'id_vendedor',
        'id_funcionario',
        'status',
        'nome',
        'data_inicio_das_atividades',
        'foto',
        'anotacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Garante a existencia de geral.
     */
    public function garantirGeral(): void
    {
        $geral = $this->db->table($this->table)
            ->where('nome', 'GERAL')
            ->get()
            ->getRowArray();

        if (! empty($geral)) {
            if (($geral['status'] ?? '') !== 'Ativo') {
                $this->update($geral['id_vendedor'], ['status' => 'Ativo']);
            }

            return;
        }

        $this->insert([
            'status'                     => 'Ativo',
            'nome'                       => 'GERAL',
            'data_inicio_das_atividades' => date('Y-m-d'),
            'anotacoes'                  => 'Vendedor generico para PDV e venda rapida.',
        ]);
    }

    /**
     * Executa a consulta ou persistencia de visiveis.
     */
    public function visiveis(): array
    {
        $this->garantirGeral();

        return $this->db->table($this->table . ' AS vendedores')
            ->select('vendedores.*, funcionarios.telefone_fixo, funcionarios.celular, funcionarios.whatsapp, funcionarios.comercial, funcionarios.residencial, funcionarios.email')
            ->join('funcionarios', 'funcionarios.id_funcionario = vendedores.id_funcionario', 'left')
            ->where('vendedores.status !=', self::STATUS_REMOVIDO)
            ->orderBy('vendedores.nome', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Executa a consulta ou persistencia de geral.
     */
    public function geral(): array
    {
        $this->garantirGeral();

        return $this->db->table($this->table)
            ->where('nome', 'GERAL')
            ->get()
            ->getRowArray() ?? [];
    }

    /**
     * Executa a consulta ou persistencia de id geral.
     */
    public function idGeral(): int
    {
        $geral = $this->geral();

        return (int) ($geral['id_vendedor'] ?? 0);
    }

    /**
     * Executa a consulta ou persistencia de para venda.
     */
    public function paraVenda(): array
    {
        $this->garantirGeral();

        return $this->db->table($this->table)
            ->where('status', 'Ativo')
            ->orderBy('nome', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Sincroniza funcionario.
     */
    public function sincronizarFuncionario(array $funcionario, int $idFuncionario): void
    {
        $vendedor = $this->db->table($this->table)
            ->where('id_funcionario', $idFuncionario)
            ->get()
            ->getRowArray();

        if (! FuncionarioModel::atuaComo($funcionario, 'Vendedor')) {
            if (! empty($vendedor)) {
                $this->update($vendedor['id_vendedor'], ['status' => self::STATUS_REMOVIDO]);
            }

            return;
        }

        $dados = [
            'id_funcionario'             => $idFuncionario,
            'status'                     => $funcionario['status'] ?? 'Ativo',
            'nome'                       => $funcionario['nome'] ?? '',
            'data_inicio_das_atividades' => $this->dataInicio($funcionario),
            'foto'                       => $funcionario['foto'] ?? '',
            'anotacoes'                  => $funcionario['anotacoes'] ?? '',
        ];

        if (! empty($vendedor)) {
            $dados['id_vendedor'] = $vendedor['id_vendedor'];
        }

        $this->save($dados);
    }

    /**
     * Oculta por funcionario.
     */
    public function ocultarPorFuncionario(int $idFuncionario): void
    {
        $vendedor = $this->db->table($this->table)
            ->where('id_funcionario', $idFuncionario)
            ->get()
            ->getRowArray();

        if (! empty($vendedor)) {
            $this->update($vendedor['id_vendedor'], ['status' => self::STATUS_REMOVIDO]);
        }
    }

    /**
     * Oculta ocultar.
     */
    public function ocultar(int $idVendedor): void
    {
        $this->update($idVendedor, ['status' => self::STATUS_REMOVIDO]);
    }

    /**
     * Informa se atende a regra de geral.
     */
    public function ehGeral(array $vendedor): bool
    {
        return strtoupper(trim((string) ($vendedor['nome'] ?? ''))) === 'GERAL';
    }

    /**
     * Define a data inicial usada na vinculacao do registro.
     */
    private function dataInicio(array $funcionario): string
    {
        foreach (['data_inicio_das_atividades', 'data_de_contratacao'] as $campo) {
            $data = trim((string) ($funcionario[$campo] ?? ''));

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) === 1) {
                return $data;
            }
        }

        return date('Y-m-d');
    }
}
