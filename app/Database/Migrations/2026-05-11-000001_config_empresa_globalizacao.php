<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigEmpresaGlobalizacao extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        $campos = [];

        if (! $this->db->fieldExists('idioma', 'config_empresa')) {
            $campos['idioma'] = [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'pt-BR',
                'after'      => 'endereco',
            ];
        }

        if (! $this->db->fieldExists('fuso_horario', 'config_empresa')) {
            $campos['fuso_horario'] = [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'default'    => 'America/Manaus',
                'after'      => 'idioma',
            ];
        }

        if (! empty($campos)) {
            $this->forge->addColumn('config_empresa', $campos);
        }

        $this->db->table('config_empresa')
            ->where('id_config', 1)
            ->update([
                'idioma'       => 'pt-BR',
                'fuso_horario' => 'America/Manaus',
            ]);
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        $campos = [];

        if ($this->db->fieldExists('fuso_horario', 'config_empresa')) {
            $campos[] = 'fuso_horario';
        }

        if ($this->db->fieldExists('idioma', 'config_empresa')) {
            $campos[] = 'idioma';
        }

        if (! empty($campos)) {
            $this->forge->dropColumn('config_empresa', $campos);
        }
    }
}
