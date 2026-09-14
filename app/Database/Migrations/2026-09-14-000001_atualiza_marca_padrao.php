<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;
use Throwable;

class AtualizaMarcaPadrao extends Migration
{
    private const MARCAS = [
        'favicon' => [
            'legado' => 'favicon.ico',
            'novo' => 'assets/img/favicon-cmy-7f5ab7a5892e.png',
        ],
        'logo_login' => [
            'legado' => 'assets/img/zaventus-login-marca.png',
            'novo' => 'assets/img/zaventus-logo-completa-353079a01cd5.png',
        ],
    ];

    public function up()
    {
        // Uma instalacao nova executa migrations anteriores na mesma conexao.
        $this->db->resetDataCache();

        foreach (self::MARCAS as $campo => $marca) {
            if (! $this->db->fieldExists($campo, 'config_empresa')) {
                throw new RuntimeException('A personalizacao da empresa deve estar migrada antes da marca padrao.');
            }
        }

        // Altera apenas o default: preserva tipo, nulabilidade e valores personalizados.
        $tabela = $this->db->protectIdentifiers($this->db->prefixTable('config_empresa'));
        foreach (self::MARCAS as $campo => $marca) {
            $sql = 'ALTER TABLE ' . $tabela . ' ALTER COLUMN ' . $this->db->protectIdentifiers($campo)
                . ' SET DEFAULT ' . $this->db->escape($marca['novo']);
            if ($this->db->query($sql) === false) {
                throw new RuntimeException('Nao foi possivel atualizar o default da marca da empresa.');
            }
        }

        if (! $this->db->transBegin()) {
            throw new RuntimeException('Nao foi possivel iniciar a atualizacao da marca da empresa.');
        }

        try {
            $consulta = $this->db->table('config_empresa')->select('id_config, favicon, logo_login')->get();
            if ($consulta === false) {
                throw new RuntimeException('Nao foi possivel consultar a marca da empresa.');
            }

            foreach ($consulta->getResultArray() as $empresa) {
                foreach (self::MARCAS as $campo => $marca) {
                    // Comparacao exata em PHP evita a collation do banco reconhecer outro arquivo como legado.
                    $atual = trim((string) ($empresa[$campo] ?? ''));
                    if ($atual !== '' && $atual !== $marca['legado']) {
                        continue;
                    }

                    if (! $this->db->table('config_empresa')
                        ->where('id_config', $empresa['id_config'])
                        ->where($campo, $empresa[$campo])
                        ->update([$campo => $marca['novo']])) {
                        throw new RuntimeException('Nao foi possivel atualizar a marca padrao da empresa.');
                    }
                }
            }

            if (! $this->db->transStatus() || ! $this->db->transCommit()) {
                throw new RuntimeException('Nao foi possivel confirmar a marca padrao da empresa.');
            }
        } catch (Throwable $exception) {
            $this->db->transRollback();
            throw $exception;
        }
    }

    public function down()
    {
        // A marca atual pode ter sido escolhida depois pelo usuario; nao desfazer essa escolha.
        throw new RuntimeException('A marca e as personalizacoes devem ser preservadas. Use uma migration compensatoria revisada.');
    }
}
