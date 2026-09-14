<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class AtendimentoGrafica extends Migration
{
    private const QUANTIDADES = [
        'produtos' => ['quantidade', 'quantidade_minima'],
        'reposicoes' => ['quantidade'],
        'saida_de_mercadorias' => ['quantidade'],
        'produtos_do_inventario_do_estoque' => ['quantidade'],
        'produtos_pecas_os' => ['quantidade'],
        'produtos_pecas_os_provisorio' => ['quantidade'],
        'servicos_mao_de_obra_da_os' => ['quantidade'],
        'servicos_mao_de_obra_provisorio' => ['quantidade'],
        'provisorio_add_produto_por_xml' => ['quantidade', 'quantidade_minima'],
        'provisorio_reposicao_produtos_por_xml' => ['quantidade_da_reposicao'],
    ];

    public function up()
    {
        $this->adicionaColunas('servicos_mao_de_obra', [
            'tipo_preco' => ['type' => 'VARCHAR', 'constraint' => 24, 'default' => 'unidade'],
            'unidade' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'un'],
            'largura_padrao' => ['type' => 'DECIMAL', 'constraint' => '12,4', 'null' => true],
            'altura_padrao' => ['type' => 'DECIMAL', 'constraint' => '12,4', 'null' => true],
            'unidade_dimensao' => ['type' => 'VARCHAR', 'constraint' => 4, 'default' => 'm'],
            'tipo_execucao' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'interna'],
            'arte_padrao' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'nao_necessita'],
            'necessita_instalacao' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'imagem' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);

        $this->adicionaColunas('produtos', [
            'ativo' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
        ]);

        $this->alteraQuantidades();

        foreach (['ordens_de_servicos', 'ordens_de_servicos_provisorio'] as $tabela) {
            $this->adicionaColunas($tabela, $this->camposAtendimento());
            if (! $this->forge->modifyColumn($tabela, [
                'id_tecnico' => ['type' => 'INT', 'null' => true, 'default' => null],
                'deleted_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
                'data_de_saida' => ['type' => 'DATE', 'null' => true, 'default' => null],
                'hora_de_saida' => ['type' => 'TIME', 'null' => true, 'default' => null],
            ])) {
                throw new RuntimeException('Nao foi possivel preparar os campos opcionais de ' . $tabela . '.');
            }
            if ($this->db->query(
                'UPDATE ' . $this->identificador($this->db->prefixTable($tabela))
                . " SET `deleted_at` = NULL WHERE CAST(`deleted_at` AS CHAR) = '0000-00-00 00:00:00'"
            ) === false) {
                throw new RuntimeException('Nao foi possivel normalizar a exclusao logica de ' . $tabela . '.');
            }
        }

        $this->adicionaColunas('ordens_de_servicos', [
            'chave_criacao' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'hash_criacao' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'versao' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->criaIndiceUnico('numero', 'uk_atendimento_numero');
        $this->criaIndiceUnico('chave_criacao', 'uk_atendimento_chave_criacao');
        $this->protegeRelacionamentosDasOrdens();

        foreach (['servicos_mao_de_obra_da_os', 'servicos_mao_de_obra_provisorio'] as $tabela) {
            $this->adicionaColunas($tabela, [
                'id_servico_catalogo' => ['type' => 'INT', 'null' => true],
                'tipo_preco' => ['type' => 'VARCHAR', 'constraint' => 24, 'default' => 'unidade'],
                'unidade' => ['type' => 'VARCHAR', 'constraint' => 16, 'default' => 'un'],
                'largura' => ['type' => 'DECIMAL', 'constraint' => '15,4', 'null' => true],
                'altura' => ['type' => 'DECIMAL', 'constraint' => '15,4', 'null' => true],
                'area' => ['type' => 'DECIMAL', 'constraint' => '15,4', 'null' => true],
                'unidade_dimensao' => ['type' => 'VARCHAR', 'constraint' => 4, 'default' => 'm'],
                'cortesia' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'valor_catalogo' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
                'tipo_execucao' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'interna'],
                'arte' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'nao_necessita'],
                'necessita_instalacao' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            ]);
        }

        $this->adicionaColunas('servicos_mao_de_obra_da_os', [
            'removido_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        foreach (['servicos_mao_de_obra_da_os', 'pagamentos_os', 'parcelas_do_pagamento_os', 'equipamentos_os'] as $tabela) {
            if (! $this->forge->modifyColumn($tabela, [
                'deleted_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
            ])) {
                throw new RuntimeException('Nao foi possivel preparar a exclusao logica de ' . $tabela . '.');
            }
            if ($this->db->query(
                'UPDATE ' . $this->identificador($this->db->prefixTable($tabela))
                . " SET `deleted_at` = NULL WHERE CAST(`deleted_at` AS CHAR) = '0000-00-00 00:00:00'"
            ) === false) {
                throw new RuntimeException('Nao foi possivel normalizar a exclusao logica de ' . $tabela . '.');
            }
        }

        $this->criaHistorico();
        $this->criaAnexos();
        $this->adicionaColunas('config_empresa', [
            'condicoes_orcamento' => ['type' => 'TEXT', 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
        ]);
    }

    public function down()
    {
        throw new RuntimeException(
            'Esta migration preserva dados de atendimento e nao possui reversao automatica. '
            . 'Prepare uma migration compensatoria revisada a partir de um backup verificado.'
        );
    }

    private function camposAtendimento(): array
    {
        return [
            'numero' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'created_by' => ['type' => 'INT', 'null' => true],
            'id_atendente' => ['type' => 'INT', 'null' => true],
            'status_operacional' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'desconto_tipo' => ['type' => 'VARCHAR', 'constraint' => 12, 'default' => 'valor'],
            'desconto_informado' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'desconto_motivo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'validade_orcamento' => ['type' => 'DATE', 'null' => true],
            'previsao_conclusao' => ['type' => 'DATE', 'null' => true],
            'condicao_pagamento' => ['type' => 'TEXT', 'null' => true],
            'entrada_necessaria' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'valor_entrada' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'execucao_endereco' => ['type' => 'TEXT', 'null' => true],
            'execucao_referencia' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'execucao_prevista' => ['type' => 'DATETIME', 'null' => true],
            'execucao_responsavel' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => true],
            'execucao_telefone' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'execucao_observacoes' => ['type' => 'TEXT', 'null' => true],
            'deleted_by' => ['type' => 'INT', 'null' => true],
            'purge_at' => ['type' => 'DATETIME', 'null' => true],
        ];
    }

    private function adicionaColunas(string $tabela, array $campos): void
    {
        if (! $this->db->tableExists($tabela)) {
            throw new RuntimeException('Tabela existente necessaria para o atendimento: ' . $tabela);
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

    private function alteraQuantidades(): void
    {
        foreach (self::QUANTIDADES as $tabela => $colunas) {
            if (! $this->db->tableExists($tabela)) {
                continue;
            }

            foreach ($colunas as $coluna) {
                if (! $this->db->fieldExists($coluna, $tabela)) {
                    continue;
                }

                $campo = $this->db->query(
                    'SELECT COLUMN_TYPE, IS_NULLABLE FROM information_schema.COLUMNS '
                    . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$this->db->prefixTable($tabela), $coluna]
                )->getRowArray();

                if ($campo === null || strtolower($campo['COLUMN_TYPE']) === 'decimal(15,4)') {
                    continue;
                }

                // As quantidades existentes sao inteiras; a ampliacao nao arredonda seus valores.
                if (! $this->forge->modifyColumn($tabela, [
                    $coluna => [
                        'type' => 'DECIMAL',
                        'constraint' => '15,4',
                        'null' => $campo['IS_NULLABLE'] === 'YES',
                    ],
                ])) {
                    throw new RuntimeException('Nao foi possivel ampliar a precisao de ' . $tabela . '.' . $coluna . '.');
                }
            }
        }
    }

    private function criaIndiceUnico(string $coluna, string $nome): void
    {
        $tabela = $this->db->prefixTable('ordens_de_servicos');
        $indice = $this->db->query(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND NON_UNIQUE = 0 '
            . 'GROUP BY INDEX_NAME HAVING COUNT(*) = 1 AND MIN(COLUMN_NAME) = ?',
            [$tabela, $coluna]
        )->getRowArray();

        if ($indice === null) {
            if ($this->db->query(
                'ALTER TABLE ' . $this->identificador($tabela)
                . ' ADD UNIQUE KEY ' . $this->identificador($nome)
                . ' (' . $this->identificador($coluna) . ')'
            ) === false) {
                throw new RuntimeException('Nao foi possivel criar o indice unico ' . $nome . '.');
            }
        }
    }

    private function protegeRelacionamentosDasOrdens(): void
    {
        foreach (['ordens_de_servicos', 'ordens_de_servicos_provisorio'] as $nomeTabela) {
            $tabela = $this->db->prefixTable($nomeTabela);
            $chaves = $this->db->query(
                'SELECT k.CONSTRAINT_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME, '
                . 'k.REFERENCED_COLUMN_NAME, r.UPDATE_RULE, r.DELETE_RULE '
                . 'FROM information_schema.KEY_COLUMN_USAGE k '
                . 'JOIN information_schema.REFERENTIAL_CONSTRAINTS r '
                . 'ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND r.TABLE_NAME = k.TABLE_NAME '
                . 'AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME '
                . 'WHERE k.TABLE_SCHEMA = DATABASE() AND k.TABLE_NAME = ? '
                . 'ORDER BY k.CONSTRAINT_NAME, k.ORDINAL_POSITION',
                [$tabela]
            )->getResultArray();

            $relacionamentos = [];
            foreach ($chaves as $chave) {
                $relacionamentos[$chave['CONSTRAINT_NAME']][] = $chave;
            }

            foreach ($relacionamentos as $nome => $colunas) {
                $chave = $colunas[0];
                $pais = array_map([$this->db, 'prefixTable'], ['clientes', 'vendedores', 'tecnicos']);
                if ($chave['DELETE_RULE'] !== 'CASCADE'
                    || ! in_array($chave['REFERENCED_TABLE_NAME'], $pais, true)) {
                    continue;
                }

                $regraUpdate = $chave['UPDATE_RULE'];
                if (! in_array($regraUpdate, ['CASCADE', 'RESTRICT', 'NO ACTION', 'SET NULL'], true)) {
                    throw new RuntimeException('Regra de atualizacao inesperada na chave ' . $nome);
                }

                $locais = [];
                $referenciadas = [];
                foreach ($colunas as $coluna) {
                    $locais[] = $this->identificador($coluna['COLUMN_NAME']);
                    $referenciadas[] = $this->identificador($coluna['REFERENCED_COLUMN_NAME']);
                }

                // Uma unica alteracao mantem a relacao e impede que excluir o cadastro apague a OS.
                $novoNome = 'fk_atendimento_' . substr(hash('sha256', $tabela . ':' . $nome), 0, 24) . '_restrict';
                if ($this->db->query(
                    'ALTER TABLE ' . $this->identificador($tabela)
                    . ' DROP FOREIGN KEY ' . $this->identificador($nome)
                    . ', ADD CONSTRAINT ' . $this->identificador($novoNome)
                    . ' FOREIGN KEY (' . implode(', ', $locais) . ')'
                    . ' REFERENCES ' . $this->identificador($chave['REFERENCED_TABLE_NAME'])
                    . ' (' . implode(', ', $referenciadas) . ')'
                    . ' ON UPDATE ' . $regraUpdate . ' ON DELETE RESTRICT'
                ) === false) {
                    throw new RuntimeException('Nao foi possivel proteger o relacionamento ' . $nome . '.');
                }
            }
        }
    }

    private function criaHistorico(): void
    {
        if ($this->db->tableExists('ordens_de_servicos_historico')) {
            return;
        }

        $this->forge->addField([
            'id_historico' => ['type' => 'INT', 'auto_increment' => true],
            'id_ordem' => ['type' => 'INT'],
            'evento' => ['type' => 'VARCHAR', 'constraint' => 32],
            'status_anterior' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'status_novo' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'id_login' => ['type' => 'INT', 'null' => true],
            'motivo' => ['type' => 'TEXT', 'null' => true],
            'observacoes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id_historico', true);
        $this->forge->addKey(['id_ordem', 'created_at'], false, false, 'idx_historico_ordem_data');
        // Sem exclusao em cascata: eventos nao desaparecem com uma eventual purga do atendimento.
        if (! $this->forge->createTable('ordens_de_servicos_historico', true, ['ENGINE' => 'InnoDB'])) {
            throw new RuntimeException('Nao foi possivel criar o historico dos atendimentos.');
        }
    }

    private function criaAnexos(): void
    {
        if ($this->db->tableExists('anexos_os')) {
            return;
        }

        $this->forge->addField([
            'id_anexo' => ['type' => 'INT', 'auto_increment' => true],
            'id_ordem' => ['type' => 'INT'],
            'id_servico_os' => ['type' => 'INT', 'null' => true],
            'categoria' => ['type' => 'VARCHAR', 'constraint' => 32],
            'nome' => ['type' => 'VARCHAR', 'constraint' => 255],
            'arquivo' => ['type' => 'VARCHAR', 'constraint' => 255],
            'mime' => ['type' => 'VARCHAR', 'constraint' => 128],
            'tamanho' => ['type' => 'BIGINT', 'unsigned' => true],
            'created_by' => ['type' => 'INT', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id_anexo', true);
        $this->forge->addKey('id_ordem', false, false, 'idx_anexos_os_ordem');
        // arquivo identifica um arquivo privado sob writable; o banco nao armazena seu conteudo.
        if (! $this->forge->createTable('anexos_os', true, ['ENGINE' => 'InnoDB'])) {
            throw new RuntimeException('Nao foi possivel criar os anexos dos atendimentos.');
        }
    }

    private function identificador(string $nome): string
    {
        return '`' . str_replace('`', '``', $nome) . '`';
    }
}
