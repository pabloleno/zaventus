<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

/**
 * Vincula os registros financeiros existentes ao atendimento, sem duplicar modulos.
 */
class RecebimentosAtendimento extends Migration
{
    public function up()
    {
        $inteiroOpcional = ['type' => 'INT', 'null' => true, 'default' => null];
        $dataOpcional = ['type' => 'DATETIME', 'null' => true, 'default' => null];
        $textoOpcional = ['type' => 'TEXT', 'null' => true];
        $chave = ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true, 'default' => null];

        $this->adicionarCampos('pagamentos_do_cliente', [
            'id_ordem' => $inteiroOpcional,
            'id_conta' => $inteiroOpcional,
            'id_parcela' => $inteiroOpcional,
            'forma_de_pagamento' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true, 'default' => null],
            'id_caixa' => $inteiroOpcional,
            'created_by' => $inteiroOpcional,
            'estornado_at' => $dataOpcional,
            'estornado_by' => $inteiroOpcional,
            'estorno_motivo' => $textoOpcional,
            'chave_operacao' => $chave,
        ]);
        $this->adicionarCampos('contas_a_receber', [
            'id_ordem' => $inteiroOpcional,
            'id_cliente' => $inteiroOpcional,
            'valor_pago' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
        ]);
        $this->adicionarCampos('lancamentos', [
            'id_recebimento' => $inteiroOpcional,
            'natureza' => ['type' => 'VARCHAR', 'constraint' => 24, 'default' => 'receita'],
        ]);
        $this->adicionarCampos('parcelas_do_pagamento_os', ['removido_at' => $dataOpcional]);
        $this->adicionarCampos('saida_de_mercadorias', [
            'id_ordem' => $inteiroOpcional,
            'id_servico_os' => $inteiroOpcional,
            'created_by' => $inteiroOpcional,
            'estornado_at' => $dataOpcional,
            'estornado_by' => $inteiroOpcional,
            'estorno_motivo' => $textoOpcional,
            'chave_operacao' => $chave,
        ]);

        $this->indice('pagamentos_do_cliente', 'uq_recebimento_chave_operacao', ['chave_operacao'], true);
        $this->indice('pagamentos_do_cliente', 'idx_recebimento_ordem', ['id_ordem']);
        $this->indice('contas_a_receber', 'uq_conta_receber_ordem', ['id_ordem'], true);
        $this->indice('lancamentos', 'uq_lancamento_recebimento', ['id_recebimento'], true);
        $this->indice('saida_de_mercadorias', 'uq_saida_chave_operacao', ['chave_operacao'], true);
        $this->indice('saida_de_mercadorias', 'idx_saida_ordem_servico', ['id_ordem', 'id_servico_os']);

        foreach (['pagamentos_do_cliente', 'contas_a_receber', 'lancamentos', 'saida_de_mercadorias'] as $tabela) {
            if (! $this->forge->modifyColumn($tabela, ['deleted_at' => $dataOpcional])
                || ! $this->db->table($tabela)->where("CAST(deleted_at AS CHAR) = '0000-00-00 00:00:00'", null, false)->update(['deleted_at' => null])) {
                throw new RuntimeException('Nao foi possivel normalizar a exclusao logica de ' . $tabela . '.');
            }
        }
    }

    public function down()
    {
        throw new RuntimeException('Esta migration preserva recebimentos e estornos. Reversao exige restauracao de backup validado.');
    }

    private function adicionarCampos(string $tabela, array $campos): void
    {
        if (! $this->db->tableExists($tabela)) {
            throw new RuntimeException('Tabela financeira existente nao encontrada: ' . $tabela);
        }

        $novos = [];
        foreach ($campos as $nome => $definicao) {
            if (! $this->db->fieldExists($nome, $tabela)) {
                $novos[$nome] = $definicao;
            }
        }

        if ($novos !== [] && ! $this->forge->addColumn($tabela, $novos)) {
            throw new RuntimeException('Nao foi possivel ampliar a tabela ' . $tabela . '.');
        }
    }

    private function indice(string $tabela, string $nome, array $campos, bool $unico = false): void
    {
        if (array_key_exists($nome, $this->db->getIndexData($tabela))) {
            return;
        }

        $colunas = array_map(fn (string $campo): string => $this->db->escapeIdentifiers($campo), $campos);
        $sql = 'CREATE ' . ($unico ? 'UNIQUE ' : '') . 'INDEX ' . $this->db->escapeIdentifiers($nome)
            . ' ON ' . $this->db->escapeIdentifiers($this->db->prefixTable($tabela))
            . ' (' . implode(', ', $colunas) . ')';
        if ($this->db->query($sql) === false) {
            throw new RuntimeException('Nao foi possivel criar o indice ' . $nome . '.');
        }
    }
}
