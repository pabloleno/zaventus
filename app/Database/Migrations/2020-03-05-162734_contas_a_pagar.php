<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ContasAPagar extends Migration
{
	/**
	 * Aplica as alteracoes de banco definidas por esta migration.
	 */
	public function up()
	{
		$this->forge->addField([
			'id_conta' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'status' => [
				'type' => 'VARCHAR',
				'constraint' => 32
			],

			'nome' => [
				'type' => 'VARCHAR',
				'constraint' => 128
			],

			'data_de_vencimento' => [
				'type' => 'DATE'
			],

			'valor' => [
				'type' => 'DOUBLE'
			],

			'observacoes' => [
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

		$this->forge->addKey('id_conta', TRUE);
		$this->forge->createTable('contas_a_pagar');
	}

	//--------------------------------------------------------------------

	/**
	 * Reverte as alteracoes de banco aplicadas por esta migration.
	 */
	public function down()
	{
		$this->forge->dropTable('contas_a_pagar');
	}
}
