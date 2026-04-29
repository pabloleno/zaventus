<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Funcionarios extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_funcionario' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'status' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'nome' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'data_de_nascimento' => [
				'type' => 'DATE'
			],

			'rg' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'cpf' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
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

			'celular' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'comercial' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'residencial' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'email' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'cargo' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'data_de_contratacao' => [
				'type' => 'DATE'
			],

			'data_inicio_das_atividades' => [
				'type' => 'DATE'
			],

			'salario' => [
				'type' => 'DOUBLE'
			],

			'detalhes_da_atividade' => [
				'type'       => 'VARCHAR',
				'constraint' => 521
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

		$this->forge->addKey('id_funcionario', TRUE);
		$this->forge->createTable('funcionarios');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('funcionarios');
	}
}
