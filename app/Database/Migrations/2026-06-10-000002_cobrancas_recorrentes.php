<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CobrancasRecorrentes extends Migration
{
    /**
     * Cria as tabelas independentes de cobrancas e seus lembretes.
     */
    public function up()
    {
        $this->criarCobrancas();
        $this->criarOcorrencias();
        $this->adicionarPermissao();
    }

    /**
     * Remove as tabelas de cobrancas recorrentes.
     */
    public function down()
    {
        $this->forge->dropTable('cobranca_ocorrencias', true);
        $this->forge->dropTable('cobrancas', true);
        $this->removerPermissao();
    }

    /**
     * Cria o cadastro principal sem qualquer vinculo financeiro.
     */
    private function criarCobrancas(): void
    {
        $this->forge->addField([
            'id_cobranca' => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true, 'auto_increment' => true],
            'id_cliente' => ['type' => 'INT', 'constraint' => 9, 'null' => true],
            'titulo' => ['type' => 'VARCHAR', 'constraint' => 120],
            'descricao' => ['type' => 'TEXT', 'null' => true],
            'valor_total' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'quantidade_parcelas' => ['type' => 'INT', 'constraint' => 5, 'default' => 1],
            'recorrencia' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Unica'],
            'intervalo_personalizado_dias' => ['type' => 'INT', 'constraint' => 5, 'null' => true],
            'data_inicio' => ['type' => 'DATE'],
            'hora_cobranca' => ['type' => 'TIME'],
            'juros_atraso' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'juros_percentual' => ['type' => 'DECIMAL', 'constraint' => '8,4', 'default' => 0],
            'lembrete_1_hora' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'lembrete_1_dia' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'lembrete_1_semana' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Ativa'],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_cobranca', true);
        $this->forge->addKey('id_cliente');
        $this->forge->addKey(['status', 'data_inicio']);
        $this->forge->addForeignKey('id_cliente', 'clientes', 'id_cliente', 'SET NULL', 'CASCADE');
        $this->forge->createTable('cobrancas');
    }

    /**
     * Cria a agenda de parcelas e lembretes gerada por cada cobranca.
     */
    private function criarOcorrencias(): void
    {
        $this->forge->addField([
            'id_ocorrencia' => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true, 'auto_increment' => true],
            'id_cobranca' => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true],
            'numero_parcela' => ['type' => 'INT', 'constraint' => 5],
            'vencimento' => ['type' => 'DATETIME'],
            'valor' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pendente'],
            'concluida_em' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_ocorrencia', true);
        $this->forge->addUniqueKey(['id_cobranca', 'numero_parcela']);
        $this->forge->addKey(['status', 'vencimento']);
        $this->forge->addForeignKey('id_cobranca', 'cobrancas', 'id_cobranca', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cobranca_ocorrencias');
    }

    /**
     * Habilita cobrancas para usuarios que ja possuem Controle Geral.
     */
    private function adicionarPermissao(): void
    {
        $usuarios = $this->db->table('login')->select('id_login, controle_de_acesso')->get()->getResultArray();

        foreach ($usuarios as $usuario) {
            $permissoes = json_decode((string) $usuario['controle_de_acesso'], true);

            if (! is_array($permissoes)) {
                continue;
            }

            $permissoes['controle_geral']['cobrancas'] = (int) ($permissoes['controle_geral']['modulo'] ?? 0);
            $this->db->table('login')
                ->where('id_login', $usuario['id_login'])
                ->update(['controle_de_acesso' => json_encode($permissoes)]);
        }
    }

    /**
     * Remove a permissao adicionada quando a migration for revertida.
     */
    private function removerPermissao(): void
    {
        $usuarios = $this->db->table('login')->select('id_login, controle_de_acesso')->get()->getResultArray();

        foreach ($usuarios as $usuario) {
            $permissoes = json_decode((string) $usuario['controle_de_acesso'], true);

            if (! is_array($permissoes)) {
                continue;
            }

            unset($permissoes['controle_geral']['cobrancas']);
            $this->db->table('login')
                ->where('id_login', $usuario['id_login'])
                ->update(['controle_de_acesso' => json_encode($permissoes)]);
        }
    }
}
