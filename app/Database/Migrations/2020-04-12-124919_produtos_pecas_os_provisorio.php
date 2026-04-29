<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProdutosPecasOsProvisorio extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_produto' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'nome' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'quantidade' => [
				'type' => 'INT'
			],

			'valor_unitario' => [
				'type' => 'DOUBLE'
			],

			'desconto' => [
				'type' => 'DOUBLE'
			],

			'id_ordem' => [
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

		$this->forge->addKey('id_produto', TRUE);
		$this->forge->addForeignKey('id_ordem', 'ordens_de_servicos_provisorio', 'id_ordem', 'CASCADE', 'CASCADE');
		$this->forge->createTable('produtos_pecas_os_provisorio');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('produtos_pecas_os_provisorio');
	}
}
