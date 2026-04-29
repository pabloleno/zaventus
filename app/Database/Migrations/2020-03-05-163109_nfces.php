<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Nfces extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_nfce' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'data' => [
				'type' => 'DATE'
			],

			'hora' => [
				'type' => 'TIME'
			],

			'chave' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'xml' => [
				'type' => 'TEXT'
			],

			'arquivo_xml' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'status' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'erro' => [
				'type' => 'TEXT'
			],

			'id_venda' => [
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

		$this->forge->addKey('id_nfce', TRUE);
		$this->forge->addForeignKey('id_venda', 'vendas', 'id_venda', 'CASCADE', 'CASCADE');
		$this->forge->createTable('nfces');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('nfces');
	}
}
