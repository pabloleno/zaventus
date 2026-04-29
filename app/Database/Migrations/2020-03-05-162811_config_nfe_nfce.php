<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigNfeNfce extends Migration
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

			'cUF' => [
				'type' => 'INT'
			],

			'natOp' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'serie' => [
				'type' => 'INT'
			],

			'nNF' => [
				'type' => 'INT'
			],

			'cMunFG' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'tpAmb' => [
				'type' => 'INT'
			],

			'verProc' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'CNPJ' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'xNome' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'xFant' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'IE' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'CRT' => [
				'type' => 'INT'
			],

			'CEP' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'xLgr' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'nro' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'xCpl' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'xBairro' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'cMun' => [
				'type'       => 'VARCHAR',
				'constraint' => 64
			],

			'xMun' => [
				'type'       => 'VARCHAR',
				'constraint' => 64
			],

			'UF' => [
				'type'       => 'VARCHAR',
				'constraint' => 5
			],

			'cPais' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'xPais' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'fone' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'CNPJ_responsavel_tecnico' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'xContato' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'email_responsavel_tecnico' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'fone_responsavel_tecnico' => [
				'type'       => 'VARCHAR',
				'constraint' => 32
			],

			'certificado' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'senha' => [
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
		$this->forge->createTable('config_nfe_nfce');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('config_nfe_nfce');
	}
}
