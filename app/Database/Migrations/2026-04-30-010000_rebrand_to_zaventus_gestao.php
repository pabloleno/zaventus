<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RebrandToZaventusGestao extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        foreach (['config_nfe_nfce', 'config_nfce'] as $tabela) {
            if (!$this->db->tableExists($tabela)) {
                continue;
            }

            $this->db->table($tabela)
                ->where('verProc', 'NXGestao-2026.04')
                ->update(['verProc' => 'ZaventusGestao-2026.04']);

            $this->db->table($tabela)
                ->where('xFant', 'NX GESTAO HOMOLOGACAO')
                ->update(['xFant' => 'ZAVENTUS GESTAO HOMOLOGACAO']);
        }
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        foreach (['config_nfe_nfce', 'config_nfce'] as $tabela) {
            if (!$this->db->tableExists($tabela)) {
                continue;
            }

            $this->db->table($tabela)
                ->where('verProc', 'ZaventusGestao-2026.04')
                ->update(['verProc' => 'NXGestao-2026.04']);

            $this->db->table($tabela)
                ->where('xFant', 'ZAVENTUS GESTAO HOMOLOGACAO')
                ->update(['xFant' => 'NX GESTAO HOMOLOGACAO']);
        }
    }
}
