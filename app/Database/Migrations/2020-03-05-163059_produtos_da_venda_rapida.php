<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProdutosDaVendaRapida extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_produto_da_venda_rapida' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'nome' => [
				'type'       => 'VARCHAR',
				'constraint' => 512
			],

			'unidade' => [
				'type'       => 'VARCHAR',
				'constraint' => 16
			],

			'codigo_de_barras' => [
				'type'       => 'VARCHAR',
				'constraint' => 13
			],

			'quantidade' => [
				'type' => 'INT'
			],

			'valor_unitario' => [
				'type' => 'DOUBLE'
			],

			'subtotal' => [
				'type' => 'DOUBLE'
			],

			'desconto' => [
				'type' => 'DOUBLE'
			],

			'valor_final' => [
				'type' => 'DOUBLE'
			],

			'NCM' => [
				'type'       => 'VARCHAR',
				'constraint' => 8
			],

			'CSOSN' => [
				'type'       => 'VARCHAR',
				'constraint' => 3
			],

			'CFOP' => [
				'type'       => 'VARCHAR',
				'constraint' => 4
			],

			'id_produto' => [
				'type' => 'INT'
			],
		]);

		$this->forge->addKey('id_produto_da_venda_rapida', TRUE);
		$this->forge->createTable('produtos_da_venda_rapida');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('produtos_da_venda_rapida');
	}
}
