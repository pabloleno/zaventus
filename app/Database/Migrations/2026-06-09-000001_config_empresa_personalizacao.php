<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigEmpresaPersonalizacao extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        $campos = [];

        if (! $this->db->fieldExists('favicon', 'config_empresa')) {
            $campos['favicon'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'favicon.ico',
            ];
        }

        if (! $this->db->fieldExists('logo_login', 'config_empresa')) {
            $campos['logo_login'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'assets/img/zaventus-login-marca.png',
            ];
        }

        if (! empty($campos)) {
            $this->forge->addColumn('config_empresa', $campos);
        }
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        $campos = [];

        if ($this->db->fieldExists('logo_login', 'config_empresa')) {
            $campos[] = 'logo_login';
        }

        if ($this->db->fieldExists('favicon', 'config_empresa')) {
            $campos[] = 'favicon';
        }

        if (! empty($campos)) {
            $this->forge->dropColumn('config_empresa', $campos);
        }
    }
}
