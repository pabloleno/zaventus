<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfiguraFinalizacaoPdv extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        if (! $this->db->fieldExists('finalizacao_pdv', 'config_empresa')) {
            $this->forge->addColumn('config_empresa', [
                'finalizacao_pdv' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 24,
                    'default'    => 'cupom_nao_fiscal',
                    'after'      => 'logo_login',
                ],
            ]);
        }
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        if ($this->db->fieldExists('finalizacao_pdv', 'config_empresa')) {
            $this->forge->dropColumn('config_empresa', 'finalizacao_pdv');
        }
    }
}
