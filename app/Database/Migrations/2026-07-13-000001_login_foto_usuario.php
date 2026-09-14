<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LoginFotoUsuario extends Migration
{
    /**
     * Adiciona foto de identificacao ao usuario do sistema.
     */
    public function up()
    {
        if ($this->db->tableExists('login') && ! $this->db->fieldExists('foto', 'login')) {
            $this->forge->addColumn('login', [
                'foto' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                    'after'      => 'primeiro_nome',
                ],
            ]);
        }
    }

    /**
     * Remove a foto do usuario do sistema.
     */
    public function down()
    {
        if ($this->db->tableExists('login') && $this->db->fieldExists('foto', 'login')) {
            $this->forge->dropColumn('login', 'foto');
        }
    }
}
