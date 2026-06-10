<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PadronizaValoresMonetarios extends Migration
{
    private const COLUNAS = [
        'caixas' => ['valor_inicial', 'valor_total', 'valor_de_fechamento'],
        'contas_a_pagar' => ['valor'],
        'contas_a_receber' => ['valor'],
        'despesas' => ['valor'],
        'funcionarios' => ['salario'],
        'lancamentos' => ['valor'],
        'orcamentos' => ['valor_a_pagar', 'desconto', 'valor_recebido', 'troco'],
        'ordens_de_servicos' => ['frete', 'outros', 'desconto'],
        'ordens_de_servicos_provisorio' => ['frete', 'outros', 'desconto'],
        'pagamentos_do_cliente' => ['valor'],
        'parcelas_do_pagamento_os' => ['valor_da_parcela'],
        'parcelas_do_pagamento_os_provisorio' => ['valor_da_parcela'],
        'pedidos' => ['valor_a_pagar', 'desconto', 'valor_recebido', 'troco'],
        'produtos' => ['valor_de_custo', 'margem_de_lucro', 'valor_de_venda', 'lucro'],
        'produtos_da_venda' => ['valor_unitario', 'subtotal', 'desconto', 'valor_final'],
        'produtos_da_venda_rapida' => ['valor_unitario', 'subtotal', 'desconto', 'valor_final'],
        'produtos_do_inventario_do_estoque' => ['valor_unitario'],
        'produtos_do_orcamento' => ['valor_unitario', 'subtotal', 'desconto', 'valor_final'],
        'produtos_do_pdv' => ['valor_unitario', 'subtotal', 'desconto', 'valor_final'],
        'produtos_do_pedido' => ['valor_unitario', 'subtotal', 'desconto', 'valor_final'],
        'produtos_pecas_os' => ['valor_unitario', 'desconto'],
        'produtos_pecas_os_provisorio' => ['valor_unitario', 'desconto'],
        'provisorio_add_produto_por_xml' => ['valor_de_custo', 'margem_de_lucro', 'valor_de_venda', 'lucro'],
        'retiradas' => ['valor'],
        'servicos_mao_de_obra' => ['valor'],
        'servicos_mao_de_obra_da_os' => ['valor', 'desconto'],
        'servicos_mao_de_obra_provisorio' => ['valor', 'desconto'],
        'tecnicos' => ['comissao'],
        'vendas' => ['valor_a_pagar', 'desconto', 'valor_recebido', 'troco'],
        'venda_rapida' => ['valor_a_pagar', 'desconto', 'valor_recebido', 'troco'],
    ];

    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        $this->alteraColunas('DECIMAL(15,2)');
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        $this->alteraColunas('DOUBLE');
    }

    /**
     * Atualiza colunas.
     */
    private function alteraColunas(string $tipo): void
    {
        foreach (self::COLUNAS as $tabela => $colunas) {
            if (! $this->db->tableExists($tabela)) {
                continue;
            }

            foreach ($colunas as $coluna) {
                if ($this->db->fieldExists($coluna, $tabela)) {
                    $this->db->query("ALTER TABLE `{$tabela}` MODIFY `{$coluna}` {$tipo} NOT NULL");
                }
            }
        }
    }
}
