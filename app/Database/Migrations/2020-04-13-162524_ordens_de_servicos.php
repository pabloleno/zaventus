<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrdensDeServicos extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_ordem' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'situacao' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'data_de_entrada' => [
				'type' => 'DATE'
			],

			'hora_de_entrada' => [
				'type' => 'TIME'
			],

			'data_de_saida' => [
				'type' => 'DATE'
			],

			'hora_de_saida' => [
				'type' => 'TIME'
			],

			'canal_de_venda' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'centro_de_custo' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'frete' => [
				'type' => 'DOUBLE'
			],

			'outros' => [
				'type' => 'DOUBLE'
			],

			'desconto' => [
				'type' => 'DOUBLE'
			],

			'observacoes' => [
				'type'       => 'VARCHAR',
				'constraint' => 2048
			],

			'observacoes_internas' => [
				'type'       => 'VARCHAR',
				'constraint' => 2048
			],

			'id_cliente' => [
				'type' => 'INT'
			],

			'id_vendedor' => [
				'type' => 'INT'
			],

			'id_tecnico' => [
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

		$this->forge->addKey('id_ordem', TRUE);
		$this->forge->addForeignKey('id_cliente', 'clientes', 'id_cliente', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('id_vendedor', 'vendedores', 'id_vendedor', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('id_tecnico', 'tecnicos', 'id_tecnico', 'CASCADE', 'CASCADE');
		$this->forge->createTable('ordens_de_servicos');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('ordens_de_servicos');
	}
}
