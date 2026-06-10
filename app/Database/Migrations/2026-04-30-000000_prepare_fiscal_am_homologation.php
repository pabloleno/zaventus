<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PrepareFiscalAmHomologation extends Migration
{
    /**
     * Aplica as alteracoes de banco definidas por esta migration.
     */
    public function up()
    {
        if ($this->db->tableExists('formas_de_pagamento') && !$this->db->fieldExists('codigo_nfce', 'formas_de_pagamento')) {
            $this->forge->addColumn('formas_de_pagamento', [
                'codigo_nfce' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 2,
                    'default'    => '99',
                    'after'      => 'nome'
                ]
            ]);

            $this->db->resetDataCache();
        }

        $this->atualizaFormasDePagamento();
        $this->preparaConfigFiscal('config_nfe_nfce');
        $this->preparaConfigFiscal('config_nfce', [
            'CSC'   => '0123456789',
            'CSCid' => '000001'
        ]);
    }

    /**
     * Reverte as alteracoes de banco aplicadas por esta migration.
     */
    public function down()
    {
        if ($this->db->tableExists('formas_de_pagamento') && $this->db->fieldExists('codigo_nfce', 'formas_de_pagamento')) {
            $this->forge->dropColumn('formas_de_pagamento', 'codigo_nfce');
        }
    }

    /**
     * Atualiza formas de pagamento.
     */
    private function atualizaFormasDePagamento()
    {
        if (!$this->db->tableExists('formas_de_pagamento') || !$this->db->fieldExists('codigo_nfce', 'formas_de_pagamento')) {
            return;
        }

        $codigos = [
            1  => '01', // Dinheiro
            2  => '03', // Cartao de credito
            3  => '04', // Cartao de debito
            4  => '02', // Cheque
            5  => '05', // Credito loja
            6  => '10', // Vale alimentacao
            7  => '11', // Vale refeicao
            8  => '12', // Vale presente
            9  => '13', // Vale combustivel
            10 => '16', // Deposito bancario
            11 => '15', // Boleto bancario
            12 => '18', // Transferencia bancaria/carteira digital
            13 => '16', // Deposito bancario
            14 => '99', // Outros
            15 => '99', // Outros
        ];

        foreach ($codigos as $id => $codigo) {
            $this->db->table('formas_de_pagamento')
                ->where('id_forma', $id)
                ->update(['codigo_nfce' => $codigo]);
        }
    }

    /**
     * Prepara config fiscal.
     */
    private function preparaConfigFiscal($tabela, array $extras = [])
    {
        if (!$this->db->tableExists($tabela)) {
            return;
        }

        $row = $this->db->table($tabela)->where('id_config', 1)->get()->getRowArray();
        $dados = array_merge($this->dadosHomologacaoAm($row ?? []), $extras);

        if (empty($row)) {
            $agora = date('Y-m-d H:i:s');
            $dados['id_config'] = 1;
            $dados['created_at'] = $agora;
            $dados['updated_at'] = $agora;
            $dados['deleted_at'] = '0000-00-00 00:00:00';

            $this->db->table($tabela)->insert($dados);
            return;
        }

        if (!$this->configParecePadraoAntigo($row)) {
            return;
        }

        $dados['updated_at'] = date('Y-m-d H:i:s');
        $this->db->table($tabela)->where('id_config', 1)->update($dados);
    }

    /**
     * Monta os dados de homologacao am.
     */
    private function dadosHomologacaoAm(array $row)
    {
        $cnpj = preg_replace('/\D/', '', (string) ($row['CNPJ'] ?? ''));
        $ie = preg_replace('/\D/', '', (string) ($row['IE'] ?? ''));
        $usaIdentidadePadrao = $cnpj === '' || $cnpj === '1234567890123' || $cnpj === '00000000000000';

        return [
            'cUF'                       => '13',
            'natOp'                     => 'VENDA DE MERCADORIAS',
            'serie'                     => !empty($row['serie']) ? $row['serie'] : '1',
            'nNF'                       => !empty($row['nNF']) ? $row['nNF'] : '1',
            'cMunFG'                    => '1302603',
            'tpAmb'                     => '2',
            'verProc'                   => 'ZaventusGestao-2026.04',
            'CNPJ'                      => $usaIdentidadePadrao ? '00000000000000' : $cnpj,
            'xNome'                     => $usaIdentidadePadrao ? 'EMITENTE EM HOMOLOGACAO - SUBSTITUIR CADASTRO' : ($row['xNome'] ?? ''),
            'xFant'                     => $usaIdentidadePadrao ? 'ZAVENTUS GESTAO HOMOLOGACAO' : ($row['xFant'] ?? ''),
            'IE'                        => $usaIdentidadePadrao || $ie === '' ? '000000000' : $ie,
            'CRT'                       => $row['CRT'] ?? '1',
            'CEP'                       => '69000000',
            'xLgr'                      => 'ENDERECO DO EMITENTE',
            'nro'                       => 'S/N',
            'xCpl'                      => '',
            'xBairro'                   => 'CENTRO',
            'cMun'                      => '1302603',
            'xMun'                      => 'Manaus',
            'UF'                        => 'AM',
            'cPais'                     => '1058',
            'xPais'                     => 'BRASIL',
            'fone'                      => '',
            'CNPJ_responsavel_tecnico'  => $usaIdentidadePadrao ? '00000000000000' : ($row['CNPJ_responsavel_tecnico'] ?? $cnpj),
            'xContato'                  => $usaIdentidadePadrao ? 'RESPONSAVEL TECNICO - SUBSTITUIR' : ($row['xContato'] ?? ''),
            'email_responsavel_tecnico' => $usaIdentidadePadrao ? 'suporte@example.com' : ($row['email_responsavel_tecnico'] ?? ''),
            'fone_responsavel_tecnico'  => $usaIdentidadePadrao ? '00000000000' : ($row['fone_responsavel_tecnico'] ?? ''),
            'certificado'               => !empty($row['certificado']) ? $row['certificado'] : '0',
            'senha'                     => $row['senha'] ?? ''
        ];
    }

    /**
     * Informa se a configuracao fiscal ainda possui os valores padrao legados.
     */
    private function configParecePadraoAntigo(array $row)
    {
        $cnpj = preg_replace('/\D/', '', (string) ($row['CNPJ'] ?? ''));
        $uf = strtoupper((string) ($row['UF'] ?? ''));

        return (string) ($row['cUF'] ?? '') === '17'
            || $uf === 'TO'
            || (string) ($row['cMunFG'] ?? '') === '1715101'
            || $cnpj === '1234567890123';
    }
}
