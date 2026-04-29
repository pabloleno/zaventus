<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ControleDeAcessoFuncionalidades extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_ca_funcionalidade' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'funcionalidade' => [
				'type'       => 'VARCHAR',
				'constraint' => 512
			],

			'permissao' => [
				'type' => 'INT'
			],

			'id_ca_modulo' => [
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

		$this->forge->addKey('id_ca_funcionalidade', TRUE);
		$this->forge->addForeignKey('id_ca_modulo', 'controle_de_acesso_modulos', 'id_ca_modulo', 'CASCADE', 'CASCADE');
		$this->forge->createTable('controle_de_acesso_funcionalidades');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('controle_de_acesso_funcionalidades');
	}
}
