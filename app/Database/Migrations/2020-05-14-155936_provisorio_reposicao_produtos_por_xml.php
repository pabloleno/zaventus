<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProvisorioReposicaoProdutosPorXml extends Migration
{
	/**
	 * Aplica as alteracoes de banco definidas por esta migration.
	 */
	public function up()
	{
		if ($this->db->tableExists('provisorio_reposicao_produtos_por_xml')) {
			return;
		}

		$this->forge->addField([
			'id_produto_provisorio' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'nome' => [
				'type'       => 'VARCHAR',
				'constraint' => 512
			],

			'quantidade_da_reposicao' => [
				'type' => 'INT'
			],

			'id_produto' => [
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

		$this->forge->addKey('id_produto_provisorio', TRUE);
		$this->forge->createTable('provisorio_reposicao_produtos_por_xml');
	}

	//--------------------------------------------------------------------

	/**
	 * Reverte as alteracoes de banco aplicadas por esta migration.
	 */
	public function down()
	{
		$this->forge->dropTable('provisorio_reposicao_produtos_por_xml', true);
	}
}
