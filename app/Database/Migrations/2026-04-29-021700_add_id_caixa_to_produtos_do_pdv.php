<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdCaixaToProdutosDoPdv extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        if (!$this->db->fieldExists('id_caixa', 'produtos_do_pdv')) {
            $this->forge->addColumn('produtos_do_pdv', [
                'id_caixa' => [
                    'type' => 'INT',
                    'null' => true,
                    'after' => 'id_produto'
                ]
            ]);
        }
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        if ($this->db->fieldExists('id_caixa', 'produtos_do_pdv')) {
            $this->forge->dropColumn('produtos_do_pdv', 'id_caixa');
        }
    }
}
