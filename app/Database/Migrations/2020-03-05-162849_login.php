<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Login extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_login' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'usuario' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'senha' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'primeiro_nome' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'ultimo_acesso' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'tema' => [
				'type'       => 'INT',
				'constraint' => 2
			],

			'controle_de_acesso' => [
				'type'       => 'VARCHAR',
				'constraint' => 1000
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

		$this->forge->addKey('id_login', TRUE);
		$this->forge->createTable('login');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('login');
	}
}
