<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IntegracoesPagamento extends Migration
{
    /**
     * Cria o cadastro de provedores e moderniza as formas de pagamento.
     */
    public function up()
    {
        $this->criarIntegracoes();
        $this->adicionarCamposFormasPagamento();
        $this->cadastrarProvedores();
        $this->configurarFormasPagamento();
        $this->adicionarPermissao();
    }

    /**
     * Remove os recursos adicionados pela migration.
     */
    public function down()
    {
        $this->removerPermissao();

        if ($this->db->tableExists('formas_de_pagamento')) {
            $this->db->table('formas_de_pagamento')
                ->whereIn('nome', [
                    'PIX (QR Code Dinamico)',
                    'PIX (QR Code Estatico)',
                    'PIX (QR Code Dinâmico)',
                    'PIX (QR Code Estático)',
                ])
                ->delete();

            if ($this->db->fieldExists('disponivel_servicos', 'formas_de_pagamento')) {
                $this->db->table('formas_de_pagamento')
                    ->whereIn('id_forma', [6, 7, 8, 9])
                    ->update(['disponivel_servicos' => 1]);
            }

            if ($this->db->fieldExists('id_integracao', 'formas_de_pagamento')) {
                $this->db->table('formas_de_pagamento')
                    ->where('nome', 'PayPal')
                    ->update(['codigo_nfce' => '99', 'id_integracao' => null]);
            }

            foreach (['id_integracao', 'disponivel_produtos', 'disponivel_servicos'] as $campo) {
                if ($this->db->fieldExists($campo, 'formas_de_pagamento')) {
                    $this->forge->dropColumn('formas_de_pagamento', $campo);
                }
            }
        }

        $this->forge->dropTable('integracoes_pagamento', true);
    }

    /**
     * Cria a tabela que guarda configuracoes, nunca transacoes financeiras.
     */
    private function criarIntegracoes(): void
    {
        if ($this->db->tableExists('integracoes_pagamento')) {
            return;
        }

        $this->forge->addField([
            'id_integracao' => ['type' => 'INT', 'constraint' => 9, 'unsigned' => true, 'auto_increment' => true],
            'provedor' => ['type' => 'VARCHAR', 'constraint' => 40],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 80],
            'descricao' => ['type' => 'VARCHAR', 'constraint' => 255],
            'documentacao_url' => ['type' => 'VARCHAR', 'constraint' => 255],
            'tipo_autenticacao' => ['type' => 'VARCHAR', 'constraint' => 80],
            'ambiente' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'sandbox'],
            'credencial_publica' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'credencial_secreta' => ['type' => 'TEXT', 'null' => true],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'api_publica' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_integracao', true);
        $this->forge->addUniqueKey('provedor');
        $this->forge->addKey(['ativo', 'ambiente']);
        $this->forge->createTable('integracoes_pagamento');
    }

    /**
     * Adiciona aplicabilidade e vinculo opcional com o provedor.
     */
    private function adicionarCamposFormasPagamento(): void
    {
        if (! $this->db->tableExists('formas_de_pagamento')) {
            return;
        }

        $campos = [];

        if (! $this->db->fieldExists('id_integracao', 'formas_de_pagamento')) {
            $campos['id_integracao'] = [
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => true,
                'null' => true,
                'after' => 'codigo_nfce',
            ];
        }

        if (! $this->db->fieldExists('disponivel_produtos', 'formas_de_pagamento')) {
            $campos['disponivel_produtos'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'id_integracao',
            ];
        }

        if (! $this->db->fieldExists('disponivel_servicos', 'formas_de_pagamento')) {
            $campos['disponivel_servicos'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'disponivel_produtos',
            ];
        }

        if ($campos !== []) {
            $this->forge->addColumn('formas_de_pagamento', $campos);
        }
    }

    /**
     * Cadastra os provedores conhecidos sem ativar qualquer comunicacao externa.
     */
    private function cadastrarProvedores(): void
    {
        $provedores = [
            [
                'provedor' => 'paypal',
                'nome' => 'PayPal',
                'descricao' => 'Carteira digital e pagamentos internacionais.',
                'documentacao_url' => 'https://developer.paypal.com/api/rest/',
                'tipo_autenticacao' => 'OAuth 2.0 - Client ID e Secret',
                'api_publica' => 1,
            ],
            [
                'provedor' => 'pagbank',
                'nome' => 'PagBank',
                'descricao' => 'PIX, cartoes, boleto e cobrancas digitais.',
                'documentacao_url' => 'https://developer.pagbank.com.br/',
                'tipo_autenticacao' => 'Token / OAuth 2.0 conforme produto',
                'api_publica' => 1,
            ],
            [
                'provedor' => 'asaas',
                'nome' => 'Asaas',
                'descricao' => 'PIX, boleto, cartao e gestao de cobrancas.',
                'documentacao_url' => 'https://docs.asaas.com/',
                'tipo_autenticacao' => 'API Key',
                'api_publica' => 1,
            ],
            [
                'provedor' => 'banco_inter',
                'nome' => 'Banco Inter',
                'descricao' => 'PIX e cobrancas para contas empresariais.',
                'documentacao_url' => 'https://developers.bancointer.com.br/',
                'tipo_autenticacao' => 'OAuth 2.0 com certificado mTLS',
                'api_publica' => 1,
            ],
            [
                'provedor' => 'nubank',
                'nome' => 'Nubank / Nu Empresas',
                'descricao' => 'Recebimentos manuais pela conta Nu Empresas.',
                'documentacao_url' => 'https://nubank.com.br/empresas/solucoes-para-cobranca',
                'tipo_autenticacao' => 'Sem API publica de cobranca suportada',
                'api_publica' => 0,
            ],
        ];
        $agora = date('Y-m-d H:i:s');

        foreach ($provedores as $provedor) {
            $existente = $this->db->table('integracoes_pagamento')
                ->where('provedor', $provedor['provedor'])
                ->get()
                ->getRowArray();

            if ($existente) {
                continue;
            }

            $provedor['ambiente'] = 'sandbox';
            $provedor['ativo'] = 0;
            $provedor['created_at'] = $agora;
            $provedor['updated_at'] = $agora;
            $this->db->table('integracoes_pagamento')->insert($provedor);
        }
    }

    /**
     * Insere PIX e define quais formas fazem sentido para produtos e servicos.
     */
    private function configurarFormasPagamento(): void
    {
        if (! $this->db->tableExists('formas_de_pagamento')) {
            return;
        }

        $agora = date('Y-m-d H:i:s');
        $pix = [
            ['nome' => 'PIX (QR Code Dinâmico)', 'codigo_nfce' => '17'],
            ['nome' => 'PIX (QR Code Estático)', 'codigo_nfce' => '20'],
        ];

        foreach ($pix as $forma) {
            $existente = $this->db->table('formas_de_pagamento')
                ->where('nome', $forma['nome'])
                ->get()
                ->getRowArray();

            if ($existente) {
                $this->db->table('formas_de_pagamento')
                    ->where('id_forma', $existente['id_forma'])
                    ->update([
                        'codigo_nfce' => $forma['codigo_nfce'],
                        'disponivel_produtos' => 1,
                        'disponivel_servicos' => 1,
                    ]);
                continue;
            }

            $forma['disponivel_produtos'] = 1;
            $forma['disponivel_servicos'] = 1;
            $forma['created_at'] = $agora;
            $forma['updated_at'] = $agora;
            $forma['deleted_at'] = '0000-00-00 00:00:00';
            $this->db->table('formas_de_pagamento')->insert($forma);
        }

        $this->db->table('formas_de_pagamento')
            ->whereIn('id_forma', [6, 7, 8, 9])
            ->update(['disponivel_produtos' => 1, 'disponivel_servicos' => 0]);

        $paypal = $this->db->table('integracoes_pagamento')->where('provedor', 'paypal')->get()->getRowArray();

        if ($paypal) {
            $this->db->table('formas_de_pagamento')
                ->where('nome', 'PayPal')
                ->update(['codigo_nfce' => '18', 'id_integracao' => $paypal['id_integracao']]);
        }
    }

    /**
     * Habilita o painel para usuarios que ja administram configuracoes.
     */
    private function adicionarPermissao(): void
    {
        if (! $this->db->tableExists('login')) {
            return;
        }

        $usuarios = $this->db->table('login')->select('id_login, controle_de_acesso')->get()->getResultArray();

        foreach ($usuarios as $usuario) {
            $permissoes = json_decode((string) $usuario['controle_de_acesso'], true);

            if (! is_array($permissoes)) {
                continue;
            }

            if (! isset($permissoes['configs']) || ! is_array($permissoes['configs'])) {
                $permissoes['configs'] = [];
            }

            $permissoes['configs']['desenvolvedor'] = (int) ($permissoes['configs']['modulo'] ?? 0);
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
        if (! $this->db->tableExists('login')) {
            return;
        }

        $usuarios = $this->db->table('login')->select('id_login, controle_de_acesso')->get()->getResultArray();

        foreach ($usuarios as $usuario) {
            $permissoes = json_decode((string) $usuario['controle_de_acesso'], true);

            if (! is_array($permissoes)) {
                continue;
            }

            unset($permissoes['configs']['desenvolvedor']);
            $this->db->table('login')
                ->where('id_login', $usuario['id_login'])
                ->update(['controle_de_acesso' => json_encode($permissoes)]);
        }
    }
}
