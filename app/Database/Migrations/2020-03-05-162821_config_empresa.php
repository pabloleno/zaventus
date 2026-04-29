<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigEmpresa extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_config' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'razao_social' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'nome_fantasia' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'cnpj' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'inscricao_estadual' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'telefone' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'endereco' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
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

		$this->forge->addKey('id_config', TRUE);
		$this->forge->createTable('config_empresa');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('config_empresa');
	}
}
