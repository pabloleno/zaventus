<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ContatosPadraoControleGeral extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        $this->adicionarCampo('clientes', 'whatsapp', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'celular',
        ]);

        $this->adicionarCampo('clientes', 'telefone_fixo', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'whatsapp',
        ]);

        $this->adicionarCampo('fornecedores', 'whatsapp', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'celular',
        ]);

        $this->adicionarCampo('fornecedores', 'telefone_fixo', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'whatsapp',
        ]);

        $this->adicionarCampo('funcionarios', 'whatsapp', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'celular',
        ]);

        $this->adicionarCampo('funcionarios', 'telefone_fixo', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'whatsapp',
        ]);

        $this->adicionarCampo('tecnicos', 'celular', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'foto',
        ]);

        $this->adicionarCampo('tecnicos', 'whatsapp', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'celular',
        ]);

        $this->adicionarCampo('tecnicos', 'telefone_fixo', [
            'type'       => 'VARCHAR',
            'constraint' => 16,
            'null'       => true,
            'after'      => 'whatsapp',
        ]);
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        foreach ([
            'clientes' => ['whatsapp', 'telefone_fixo'],
            'fornecedores' => ['whatsapp', 'telefone_fixo'],
            'funcionarios' => ['whatsapp', 'telefone_fixo'],
            'tecnicos' => ['celular', 'whatsapp', 'telefone_fixo'],
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
