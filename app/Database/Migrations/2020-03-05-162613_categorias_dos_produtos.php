<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CategoriasDosProdutos extends Migration
{
	/**
	 * Aplica as alteracoes de banco definidas por esta migration.
	 */
	public function up()
	{
		$this->forge->addField([
			'id_categoria' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'nome' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'descricao' => [
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

		$this->forge->addKey('id_categoria', TRUE);
		$this->forge->createTable('categorias_dos_produtos');
	}

	//--------------------------------------------------------------------

	/**
	 * Reverte as alteracoes de banco aplicadas por esta migration.
	 */
	public function down()
	{
		$this->forge->dropTable('categorias_dos_produtos');
	}
}
