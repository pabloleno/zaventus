<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfiguraFinalizacaoPdv extends Migration
{
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

    public function down()
    {
        if ($this->db->fieldExists('finalizacao_pdv', 'config_empresa')) {
            $this->forge->dropColumn('config_empresa', 'finalizacao_pdv');
        }
    }
}
