<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GestaoFiscalDocumentos extends Migration
{
    /**
     * Aplica os campos de acompanhamento fiscal e cria o historico de eventos.
     */
    public function up()
    {
        $this->prepararTabelaDocumento('nfes');
        $this->prepararTabelaDocumento('nfces', true);
        $this->criarEventos();
    }

    /**
     * Reverte apenas os campos criados por esta migration.
     */
    public function down()
    {
        $this->forge->dropTable('documentos_fiscais_eventos', true);

        foreach (['nfes', 'nfces'] as $tabela) {
            foreach ($this->camposDocumento($tabela === 'nfces') as $campo => $_definicao) {
                if ($this->db->fieldExists($campo, $tabela)) {
                    $this->forge->dropColumn($tabela, $campo);
                }
            }
        }
    }

    /**
     * Adiciona metadados fiscais nas tabelas de NFe/NFCe existentes.
     */
    private function prepararTabelaDocumento(string $tabela, bool $incluirProtocolo = false): void
    {
        if (! $this->db->tableExists($tabela)) {
            return;
        }

        foreach ($this->camposDocumento($incluirProtocolo) as $campo => $definicao) {
            if (! $this->db->fieldExists($campo, $tabela)) {
                $this->forge->addColumn($tabela, [$campo => $definicao]);
            }
        }
    }

    /**
     * Campos comuns usados para reconciliar o documento local com a SEFAZ.
     */
    private function camposDocumento(bool $incluirProtocolo = false): array
    {
        $campos = [
            'ambiente' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true, 'after' => 'status'],
            'serie' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true, 'after' => 'ambiente'],
            'numero' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'serie'],
            'recibo' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true, 'after' => 'numero'],
            'nprot' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true, 'after' => 'recibo'],
            'cstat' => ['type' => 'VARCHAR', 'constraint' => 8, 'null' => true, 'after' => 'nprot'],
            'xmotivo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'cstat'],
            'data_autorizacao' => ['type' => 'DATETIME', 'null' => true, 'after' => 'xmotivo'],
            'data_cancelamento' => ['type' => 'DATETIME', 'null' => true, 'after' => 'data_autorizacao'],
            'xml_cancelamento' => ['type' => 'MEDIUMTEXT', 'null' => true, 'after' => 'data_cancelamento'],
            'ultimo_retorno_sefaz' => ['type' => 'MEDIUMTEXT', 'null' => true, 'after' => 'xml_cancelamento'],
            'ultima_consulta_em' => ['type' => 'DATETIME', 'null' => true, 'after' => 'ultimo_retorno_sefaz'],
        ];

        if ($incluirProtocolo) {
            $campos = ['protocolo' => ['type' => 'MEDIUMTEXT', 'null' => true, 'after' => 'xml']] + $campos;
            $campos['xml_protocolado_cancelamento'] = ['type' => 'MEDIUMTEXT', 'null' => true, 'after' => 'xml_cancelamento'];
        }

        return $campos;
    }

    /**
     * Cria o historico enxuto de consultas, cancelamentos e demais eventos fiscais.
     */
    private function criarEventos(): void
    {
        if ($this->db->tableExists('documentos_fiscais_eventos')) {
            return;
        }

        $this->forge->addField([
            'id_evento' => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true, 'auto_increment' => true],
            'modelo' => ['type' => 'VARCHAR', 'constraint' => 4],
            'id_documento' => ['type' => 'INT', 'constraint' => 9],
            'tipo_evento' => ['type' => 'VARCHAR', 'constraint' => 32],
            'chave' => ['type' => 'VARCHAR', 'constraint' => 44, 'null' => true],
            'protocolo' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'cstat' => ['type' => 'VARCHAR', 'constraint' => 8, 'null' => true],
            'xmotivo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'justificativa' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'xml_envio' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'xml_retorno' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Registrado'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_evento', true);
        $this->forge->addKey(['modelo', 'id_documento']);
        $this->forge->addKey(['tipo_evento', 'created_at']);
        $this->forge->createTable('documentos_fiscais_eventos');
    }
}
