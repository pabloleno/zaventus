<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VinculaProdutosPecasOsEstoque extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        $this->adicionarCampo('produtos_pecas_os_provisorio');
        $this->adicionarCampo('produtos_pecas_os');
        $this->vincularProdutosPorNome('produtos_pecas_os_provisorio');
        $this->vincularProdutosPorNome('produtos_pecas_os');
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        $this->removerCampo('produtos_pecas_os_provisorio');
        $this->removerCampo('produtos_pecas_os');
    }

    /**
     * Adiciona campo.
     */
    private function adicionarCampo(string $tabela): void
    {
        if (! $this->db->fieldExists('id_produto_estoque', $tabela)) {
            $this->forge->addColumn($tabela, [
                'id_produto_estoque' => [
                    'type'       => 'INT',
                    'constraint' => 9,
                    'null'       => true,
                    'after'      => 'id_produto',
                ],
            ]);
        }
    }

    /**
     * Remove campo.
     */
    private function removerCampo(string $tabela): void
    {
        if ($this->db->fieldExists('id_produto_estoque', $tabela)) {
            $this->forge->dropColumn($tabela, 'id_produto_estoque');
        }
    }

    /**
     * Vincula produtos por nome.
     */
    private function vincularProdutosPorNome(string $tabela): void
    {
        $pecas = $this->db->table($tabela)
            ->where('id_produto_estoque IS NULL', null, false)
            ->get()
            ->getResultArray();

        foreach ($pecas as $peca) {
            $produto = $this->db->table('produtos')
                ->where('nome', $peca['nome'])
                ->orderBy('id_produto', 'ASC')
                ->get()
                ->getRowArray();

            if (empty($produto)) {
                continue;
            }

            $this->db->table($tabela)
                ->where('id_produto', $peca['id_produto'])
                ->update(['id_produto_estoque' => $produto['id_produto']]);
        }
    }
}
