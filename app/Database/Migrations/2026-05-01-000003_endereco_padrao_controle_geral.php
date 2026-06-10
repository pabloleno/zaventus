<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnderecoPadraoControleGeral extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        $this->adicionarCampo('fornecedores', 'UF', [
            'type'       => 'VARCHAR',
            'constraint' => 2,
            'null'       => true,
            'after'      => 'municipio',
        ]);

        $this->adicionarCampo('funcionarios', 'UF', [
            'type'       => 'VARCHAR',
            'constraint' => 2,
            'null'       => true,
            'after'      => 'municipio',
        ]);

        $this->adicionarCampo('funcionarios', 'codigo_do_municipio', [
            'type'       => 'VARCHAR',
            'constraint' => 7,
            'null'       => true,
            'after'      => 'UF',
        ]);

        $this->adicionarCampo('tecnicos', 'codigo_do_municipio', [
            'type'       => 'VARCHAR',
            'constraint' => 7,
            'null'       => true,
            'after'      => 'uf',
        ]);
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        foreach ([
            'fornecedores' => ['UF'],
            'funcionarios' => ['UF', 'codigo_do_municipio'],
            'tecnicos' => ['codigo_do_municipio'],
        ] as $tabela => $campos) {
            foreach ($campos as $campo) {
                if ($this->db->fieldExists($campo, $tabela)) {
                    $this->forge->dropColumn($tabela, $campo);
                }
            }
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
}
