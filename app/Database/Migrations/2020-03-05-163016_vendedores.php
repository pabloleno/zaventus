<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Vendedores extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_vendedor' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'status' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'nome' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'data_inicio_das_atividades' => [
				'type' => 'DATE'
			],

			'anotacoes' => [
				'type'       => 'VARCHAR',
				'constraint' => 512
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

		$this->forge->addKey('id_vendedor', TRUE);
		$this->forge->createTable('vendedores');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('vendedores');
	}
}
