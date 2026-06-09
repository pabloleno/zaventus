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

    public function down()
    {
        foreach (self::TABELAS as $tabela) {
            if ($this->db->fieldExists('tipo_negocio', $tabela)) {
                $this->forge->dropColumn($tabela, 'tipo_negocio');
            }
        }
    }

    private function campoAnterior(string $tabela): string
    {
        return in_array($tabela, ['contas_a_pagar', 'contas_a_receber'], true) ? 'status' : 'id_' . rtrim($tabela, 's');
    }
}
