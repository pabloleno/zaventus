<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DiagnosticosIntegracoesPagamento extends Migration
{
    /**
     * Guarda somente o resultado seguro do ultimo teste de autenticacao.
     */
    public function up()
    {
        if (! $this->db->tableExists('integracoes_pagamento')) {
            return;
        }

        $campos = [];

        if (! $this->db->fieldExists('ultimo_teste_em', 'integracoes_pagamento')) {
            $campos['ultimo_teste_em'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'observacoes',
            ];
        }

        if (! $this->db->fieldExists('ultimo_teste_status', 'integracoes_pagamento')) {
            $campos['ultimo_teste_status'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'ultimo_teste_em',
            ];
        }

        if (! $this->db->fieldExists('ultimo_teste_mensagem', 'integracoes_pagamento')) {
            $campos['ultimo_teste_mensagem'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'ultimo_teste_status',
            ];
        }

        if ($campos !== []) {
            $this->forge->addColumn('integracoes_pagamento', $campos);
        }

        $this->db->table('integracoes_pagamento')
            ->groupStart()
            ->where('ultimo_teste_status', null)
            ->orWhere('ultimo_teste_status !=', 'sucesso')
            ->groupEnd()
            ->update(['ativo' => 0]);
    }

    /**
     * Remove o historico de diagnosticos.
     */
    public function down()
    {
        if (! $this->db->tableExists('integracoes_pagamento')) {
            return;
        }

        foreach (['ultimo_teste_mensagem', 'ultimo_teste_status', 'ultimo_teste_em'] as $campo) {
            if ($this->db->fieldExists($campo, 'integracoes_pagamento')) {
                $this->forge->dropColumn('integracoes_pagamento', $campo);
            }
        }
    }
}
