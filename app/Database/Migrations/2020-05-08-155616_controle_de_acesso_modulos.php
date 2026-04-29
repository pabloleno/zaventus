<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ControleDeAcessoModulos extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_ca_modulo' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'modulo' => [
				'type'       => 'VARCHAR',
				'constraint' => 512
			],

			'permissao' => [
				'type' => 'INT'
			],

			'created_at' => [
				'type' => 'DATETIME'
			],

			'updated_at' => [
				'type' => 'DATETIME'
			],

			'deleted_at' => [
				'type' => 'DATETIME'
			]
		]);

		$this->forge->addKey('id_ca_modulo', TRUE);
		$this->forge->createTable('controle_de_acesso_modulos');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('controle_de_acesso_modulos');
	}
}
