<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ServicosMaoDeObraProvisorio extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_servico' => [
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
				'constraint' => 1024
			],

			'quantidade' => [
				'type' => 'INT'
			],

			'valor' => [
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

		$this->forge->addKey('id_servico', TRUE);
		$this->forge->addForeignKey('id_ordem', 'ordens_de_servicos_provisorio', 'id_ordem', 'CASCADE', 'CASCADE');
		$this->forge->createTable('servicos_mao_de_obra_provisorio');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('servicos_mao_de_obra_provisorio');
	}
}
