<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CadastrosControleGeralFotos extends Migration
{
    private const TABELAS = [
        'clientes',
        'fornecedores',
        'funcionarios',
        'vendedores',
    ];

    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        foreach (self::TABELAS as $tabela) {
            if ($this->db->tableExists($tabela) && ! $this->db->fieldExists('foto', $tabela)) {
                $this->forge->addColumn($tabela, [
                    'foto' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 255,
                        'default'    => '',
                    ],
                ]);
            }
        }

        if (
            $this->db->fieldExists('foto', 'funcionarios')
            && $this->db->fieldExists('foto', 'vendedores')
            && $this->db->fieldExists('id_funcionario', 'vendedores')
        ) {
            $this->db->query(
                'UPDATE vendedores v
                 INNER JOIN funcionarios f ON f.id_funcionario = v.id_funcionario
                 SET v.foto = f.foto'
            );
        }
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        foreach (array_reverse(self::TABELAS) as $tabela) {
            if ($this->db->tableExists($tabela) && $this->db->fieldExists('foto', $tabela)) {
                $this->forge->dropColumn($tabela, 'foto');
            }
        }
    }
}
