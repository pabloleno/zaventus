<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PagamentosOsProvisorio extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_pagamento' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'tipo' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
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

		$this->forge->addKey('id_pagamento', TRUE);
		$this->forge->addForeignKey('id_ordem', 'ordens_de_servicos_provisorio', 'id_ordem', 'CASCADE', 'CASCADE');
		$this->forge->createTable('pagamentos_os_provisorio');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('pagamentos_os_provisorio');
	}
}
