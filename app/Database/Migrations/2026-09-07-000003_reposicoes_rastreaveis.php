<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class ReposicoesRastreaveis extends Migration
{
    public function up()
    {
        $campos = [
            'created_by' => ['type' => 'INT', 'null' => true],
            'estornado_at' => ['type' => 'DATETIME', 'null' => true],
            'estornado_by' => ['type' => 'INT', 'null' => true],
            'estorno_motivo' => ['type' => 'TEXT', 'null' => true],
            'chave_operacao' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
        ];
        foreach ($campos as $nome => $definicao) {
            if (! $this->db->fieldExists($nome, 'reposicoes') && ! $this->forge->addColumn('reposicoes', [$nome => $definicao])) {
                throw new RuntimeException('Não foi possível adicionar rastreabilidade às reposições.');
            }
        }
        $tabela = $this->db->prefixTable('reposicoes');
        $indice = $this->db->query(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$tabela, 'uq_reposicao_chave_operacao']
        )->getRowArray();
        if ($indice === null) {
            if ($this->db->query('ALTER TABLE ' . $this->db->protectIdentifiers($tabela) . ' ADD UNIQUE KEY uq_reposicao_chave_operacao (chave_operacao)') === false) {
                throw new RuntimeException('Não foi possível criar o índice único das reposições.');
            }
        }
        if (! $this->forge->modifyColumn('reposicoes', ['deleted_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null]])) {
            throw new RuntimeException('Não foi possível normalizar a exclusão das reposições.');
        }
        if (! $this->db->table('reposicoes')->where("CAST(deleted_at AS CHAR) = '0000-00-00 00:00:00'", null, false)->update(['deleted_at' => null])) {
            throw new RuntimeException('Não foi possível normalizar a exclusão lógica das reposições.');
        }
    }

    public function down()
    {
        throw new RuntimeException('Os registros de estorno devem ser preservados. Reversão exige migration compensatória revisada.');
    }
}
