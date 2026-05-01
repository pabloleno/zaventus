<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigEmpresaEnderecoContatos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('config_empresa', [
            'celular' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'after'      => 'inscricao_estadual',
            ],
            'whatsapp' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'after'      => 'celular',
            ],
            'telefone_fixo' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'after'      => 'whatsapp',
            ],
            'cep' => [
                'type'       => 'VARCHAR',
                'constraint' => 9,
                'null'       => true,
                'after'      => 'telefone',
            ],
            'logradouro' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'after'      => 'cep',
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'after'      => 'logradouro',
            ],
            'complemento' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'after'      => 'numero',
            ],
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'after'      => 'complemento',
            ],
            'municipio' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'after'      => 'bairro',
            ],
            'UF' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'municipio',
            ],
            'codigo_do_municipio' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null'       => true,
                'after'      => 'UF',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('config_empresa', [
            'celular',
            'whatsapp',
            'telefone_fixo',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'municipio',
            'UF',
            'codigo_do_municipio',
        ]);
    }
}
