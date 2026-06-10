<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Caixas extends Migration
{
	/**
	 * Aplica as alteracoes de banco definidas por esta migration.
	 */
	public function up()
	{
		$this->forge->addField([
			'id_caixa' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'data_de_abertura' => [
				'type' => 'DATE'
			],

			'data_de_fechamento' => [
				'type' => 'DATE'
			],

			'hora_de_abertura' => [
				'type' => 'TIME'
			],

			'hora_de_fechamento' => [
				'type' => 'TIME'
			],

			'valor_inicial' => [
				'type' => 'DOUBLE'
			],

			'valor_total' => [
				'type' => 'DOUBLE'
			],

			'valor_de_fechamento' => [
				'type' => 'DOUBLE'
			],

			'observacoes' => [
				'type'       => 'VARCHAR',
				'constraint' => 512
			],

			'status' => [
				'type'       => 'VARCHAR',
				'constraint' => 18
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

		$this->forge->addKey('id_caixa', TRUE);
		$this->forge->createTable('caixas');
	}

	//--------------------------------------------------------------------

	/**
	 * Reverte as alteracoes de banco aplicadas por esta migration.
	 */
	public function down()
	{
		$this->forge->dropTable('caixas');
	}
}
