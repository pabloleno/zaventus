<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ParcelasDoPagamentoOsProvisorio extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_parcela' => [
				'type'           => 'INT',
				'constraint'     => 9,
				'usigned'        => TRUE,
				'auto_increment' => TRUE
			],

			'data_de_vencimento' => [
				'type' => 'DATE'
			],

			'valor_da_parcela' => [
				'type' => 'DOUBLE'
			],

			'forma_de_pagamento' => [
				'type'       => 'VARCHAR',
				'constraint' => 128
			],

			'observacoes' => [
				'type'       => 'VARCHAR',
				'constraint' => 2048
			],

			'id_pagamento' => [
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

		$this->forge->addKey('id_parcela', TRUE);
		$this->forge->addForeignKey('id_pagamento', 'pagamentos_os_provisorio', 'id_pagamento', 'CASCADE', 'CASCADE');
		$this->forge->createTable('parcelas_do_pagamento_os_provisorio');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('parcelas_do_pagamento_os_provisorio');
	}
}
