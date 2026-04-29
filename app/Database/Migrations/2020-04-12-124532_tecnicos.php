<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tecnicos extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_tecnico' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'nome' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'cpf' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'rg' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'data_de_nascimento' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'sexo' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'email' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'comissao' => [
				'type' => 'DOUBLE'
			],

			'observacoes' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'foto' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'fixo' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'celular_1' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'celular_2' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'cep' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'logradouro' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'numero' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'complemento' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'bairro' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'cidade' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'uf' => [
				'type'       => 'VARCHAR',
				'constraint' => 2
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

		$this->forge->addKey('id_tecnico', TRUE);
		$this->forge->createTable('tecnicos');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('tecnicos');
	}
}
