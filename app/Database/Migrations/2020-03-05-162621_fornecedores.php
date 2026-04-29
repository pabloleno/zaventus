<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Fornecedores extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_fornecedor' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'nome_do_representante' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'nome_da_empresa' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'cnpj' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'ie' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'cep' => [
				'type'       => 'VARCHAR',
				'constraint' => 9
			],

			'logradouro' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'numero' => [
				'type'       => 'VARCHAR',
				'constraint' => 5
			],

			'complemento' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'bairro' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'municipio' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'codigo_do_municipio' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'celular' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'comercial' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'email' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'anotacoes' => [
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

		$this->forge->addKey('id_fornecedor', TRUE);
		$this->forge->createTable('fornecedores');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('fornecedores');
	}
}
