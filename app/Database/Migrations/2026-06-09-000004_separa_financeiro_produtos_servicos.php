<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeparaFinanceiroProdutosServicos extends Migration
{
    private const TABELAS = [
        'contas_a_pagar',
        'contas_a_receber',
        'lancamentos',
        'despesas',
    ];

    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        foreach (self::TABELAS as $tabela) {
            if (! $this->db->fieldExists('tipo_negocio', $tabela)) {
                $this->forge->addColumn($tabela, [
                    'tipo_negocio' => [
                        'type' => 'VARCHAR',
                        'constraint' => 16,
                        'default' => 'Geral',
                        'null' => false,
                        'after' => $this->campoAnterior($tabela),
                    ],
                ]);
            }
        }
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        foreach (self::TABELAS as $tabela) {
            if ($this->db->fieldExists('tipo_negocio', $tabela)) {
                $this->forge->dropColumn($tabela, 'tipo_negocio');
            }
        }
    }

    /**
     * Consulta a definicao anterior de uma coluna antes de altera-la.
     */
    private function campoAnterior(string $tabela): string
    {
        return in_array($tabela, ['contas_a_pagar', 'contas_a_receber'], true) ? 'status' : 'id_' . rtrim($tabela, 's');
    }
}
