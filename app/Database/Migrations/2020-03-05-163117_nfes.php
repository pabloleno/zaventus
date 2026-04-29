<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Nfes extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_nfe' => [
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

			'protocolo' => [
				'type' => 'TEXT'
			],

			'status' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'erro' => [
				'type' => 'TEXT'
			],

			'xml_protocolado_cancelamento' => [
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

		$this->forge->addKey('id_nfe', TRUE);
		$this->forge->addForeignKey('id_venda', 'vendas', 'id_venda', 'CASCADE', 'CASCADE');
		$this->forge->createTable('nfes');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('nfes');
	}
}
