<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VinculaVendedoresFuncionarios extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        $this->adicionarCampo('funcionarios', 'tipo_funcionario', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'default'    => 'Outros',
            'after'      => 'status',
        ]);

        $this->adicionarCampo('vendedores', 'id_funcionario', [
            'type'       => 'INT',
            'constraint' => 9,
            'null'       => true,
            'after'      => 'id_vendedor',
        ]);

        $this->garantirVendedorGeral();
        $this->marcarFuncionariosComCargoVendedor();
        $this->vincularVendedoresExistentes();
        $this->criarVendedoresDosFuncionarios();
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        if ($this->db->fieldExists('id_funcionario', 'vendedores')) {
            $this->forge->dropColumn('vendedores', 'id_funcionario');
        }

        if ($this->db->fieldExists('tipo_funcionario', 'funcionarios')) {
            $this->forge->dropColumn('funcionarios', 'tipo_funcionario');
        }
    }

    /**
     * Adiciona campo.
     */
    private function adicionarCampo(string $tabela, string $campo, array $definicao): void
    {
        if (! $this->db->fieldExists($campo, $tabela)) {
            $this->forge->addColumn($tabela, [$campo => $definicao]);
        }
    }

    /**
     * Garante a existencia de vendedor geral.
     */
    private function garantirVendedorGeral(): void
    {
        $tabela = $this->db->table('vendedores');
        $geral = $tabela->where('nome', 'GERAL')->get()->getRowArray();

        if (empty($geral)) {
            $this->db->table('vendedores')->insert([
                'status'                     => 'Ativo',
                'nome'                       => 'GERAL',
                'data_inicio_das_atividades' => date('Y-m-d'),
                'anotacoes'                  => 'Vendedor generico para PDV e venda rapida.',
            ]);

            return;
        }

        if (($geral['status'] ?? '') !== 'Ativo') {
            $this->db->table('vendedores')
                ->where('id_vendedor', $geral['id_vendedor'])
                ->update(['status' => 'Ativo']);
        }
    }

    /**
     * Marca funcionarios com cargo de vendedor antes de criar os vinculos.
     */
    private function marcarFuncionariosComCargoVendedor(): void
    {
        $funcionarios = $this->db->table('funcionarios')
            ->like('cargo', 'vendedor')
            ->get()
            ->getResultArray();

        foreach ($funcionarios as $funcionario) {
            $this->db->table('funcionarios')
                ->where('id_funcionario', $funcionario['id_funcionario'])
                ->update(['tipo_funcionario' => 'Vendedor']);
        }
    }

    /**
     * Vincula vendedores existentes.
     */
    private function vincularVendedoresExistentes(): void
    {
        $vendedores = $this->db->table('vendedores')
            ->where('nome !=', 'GERAL')
            ->get()
            ->getResultArray();

        foreach ($vendedores as $vendedor) {
            if (! empty($vendedor['id_funcionario'])) {
                continue;
            }

            $funcionario = $this->db->table('funcionarios')
                ->where('nome', $vendedor['nome'])
                ->get()
                ->getRowArray();

            if (empty($funcionario)) {
                continue;
            }

            $this->db->table('funcionarios')
                ->where('id_funcionario', $funcionario['id_funcionario'])
                ->update(['tipo_funcionario' => 'Vendedor']);

            $this->db->table('vendedores')
                ->where('id_vendedor', $vendedor['id_vendedor'])
                ->update(['id_funcionario' => $funcionario['id_funcionario']]);
        }
    }

    /**
     * Cria vendedores dos funcionarios.
     */
    private function criarVendedoresDosFuncionarios(): void
    {
        $funcionarios = $this->db->table('funcionarios')
            ->where('tipo_funcionario', 'Vendedor')
            ->get()
            ->getResultArray();

        foreach ($funcionarios as $funcionario) {
            $vendedor = $this->db->table('vendedores')
                ->where('id_funcionario', $funcionario['id_funcionario'])
                ->get()
                ->getRowArray();

            if (! empty($vendedor)) {
                continue;
            }

            $this->db->table('vendedores')->insert([
                'id_funcionario'             => $funcionario['id_funcionario'],
                'status'                     => $funcionario['status'] ?? 'Ativo',
                'nome'                       => $funcionario['nome'] ?? '',
                'data_inicio_das_atividades' => $this->dataInicio($funcionario),
                'anotacoes'                  => $funcionario['anotacoes'] ?? '',
            ]);
        }
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
