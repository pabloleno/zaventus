<?php

namespace App\Database\Seeds;

class AutoInsert extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        // Dados da Empresa
        $this->db->table('config_empresa')->insert([
            'nome_fantasia' => 'Sua Empresa',
            'telefone'      => '6335710000',
            'endereco'      => 'Av. 01 Quadra 02 Lote 03',
            'idioma'        => 'pt-BR',
            'fuso_horario'  => 'America/Manaus'
        ]);

        // Dados do Usuário
        $this->db->table('login')->insert([
            'usuario'            => 'admin',
            'senha'              => password_hash('123', PASSWORD_BCRYPT),
            'primeiro_nome'      => 'Administrador',
            'tema'               => 0,
            'controle_de_acesso' => '{"vendas":{"modulo":1,"venda_rapida":1,"pdv":1,"pesq_produto":1,"hist_de_vendas":1},"controle_geral":{"modulo":1,"clientes":1,"fornecedores":1,"funcionarios":1,"vendedores":1},"estoque":{"modulo":1,"produtos":1,"reposicoes":1,"saida_de_mercadorias":1,"categorias_do_produto":1},"financeiro":{"modulo":1,"caixas":1,"lancamentos":1,"retiradas_do_caixa":1,"despesas":1, "contas_a_pagar":1,"contas_a_receber":1,"orcamentos":1,"pedidos":1,"relatorio_dre":1,"inventario_do_estoque":1,"controle_fiscal":1},"relatorios":{"modulo":1,"vendas":1,"estoque":1,"financeiro":1,"geral":1},"configs":{"modulo":1,"nfe":1,"nfce":1,"empresa":1,"sistema":1,"usuarios":1,"backup_de_dados":1}}'
        ]);

        // Cliente Consumidor para NFCe
        $this->db->table('clientes')->insert([
            'tipo' => 1,
            'nome' => 'Consumidor Final'
        ]);

        // Categorias dos Produtos
        $this->db->table('categorias_dos_produtos')->insert([
            'nome'      => 'Nenhuma',
            'descricao' => 'Para produtos que não tem categoria.'
        ]);

        // Fornecedores
        $this->db->table('fornecedores')->insert([
            'nome_da_empresa'       => 'GERAL',
            'nome_do_representante' => 'GERAL',
            'cnpj'                  => 'S/N'
        ]);

        // Configurações da NFe
        $this->db->table('config_nfe_nfce')->insert([
            'cUF'                       => '13',
            'natOp'                     => 'VENDA DE MERCADORIAS',
            'serie'                     => '1',
            'nNF'                       => '1',
            'cMunFG'                    => '1302603',
            'tpAmb'                     => '2',
            'verProc'                   => 'ZaventusGestao-2026.04',
            'CNPJ'                      => '00000000000000',
            'xNome'                     => 'EMITENTE EM HOMOLOGACAO - SUBSTITUIR CADASTRO',
            'xFant'                     => 'ZAVENTUS GESTAO HOMOLOGACAO',
            'IE'                        => '000000000',
            'CRT'                       => '1',
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
            'CNPJ_responsavel_tecnico'  => '00000000000000',
            'xContato'                  => 'RESPONSAVEL TECNICO - SUBSTITUIR',
            'email_responsavel_tecnico' => 'suporte@example.com',
            'fone_responsavel_tecnico'  => '00000000000',
            'certificado'               => '0',
            'senha'                     => ''
        ]);

        // Configurações da NFCe
        $this->db->table('config_nfce')->insert([
            'cUF'                       => '13',
            'natOp'                     => 'VENDA DE MERCADORIAS',
            'serie'                     => '1',
            'nNF'                       => '1',
            'cMunFG'                    => '1302603',
            'tpAmb'                     => '2',
            'verProc'                   => 'ZaventusGestao-2026.04',
            'CNPJ'                      => '00000000000000',
            'xNome'                     => 'EMITENTE EM HOMOLOGACAO - SUBSTITUIR CADASTRO',
            'xFant'                     => 'ZAVENTUS GESTAO HOMOLOGACAO',
            'IE'                        => '000000000',
            'CRT'                       => '1',
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
            'CNPJ_responsavel_tecnico'  => '00000000000000',
            'xContato'                  => 'RESPONSAVEL TECNICO - SUBSTITUIR',
            'email_responsavel_tecnico' => 'suporte@example.com',
            'fone_responsavel_tecnico'  => '00000000000',
            'certificado'               => '0',
            'senha'                     => '',
            'CSC'                       => '0123456789',
            'CSCid'                     => '000001'
        ]);

        // Formas de Pagamento
        $formas_de_pagamento = [
            ['nome' => 'Dinheiro', 'codigo_nfce' => '01'],
            ['nome' => 'Cartão de Crédito'],
            ['nome' => 'Cartão de Débito'],
            ['nome' => 'Cheque', 'codigo_nfce' => '02'],
            ['nome' => 'Crédito Loja'],
            ['nome' => 'Vale Alimentação'],
            ['nome' => 'Vale Refeição'],
            ['nome' => 'Vale Presente'],
            ['nome' => 'Vale Combustível'],
            ['nome' => 'Débito em Conta'],
            ['nome' => 'Boleto Bancário'],
            ['nome' => 'Transferência'],
            ['nome' => 'Depósito'],
            ['nome' => 'Nota Promissória'],
            ['nome' => 'PayPal', 'codigo_nfce' => '99']
        ];
        $codigos_nfce = ['01', '03', '04', '02', '05', '10', '11', '12', '13', '16', '15', '18', '16', '99', '99'];
        foreach ($formas_de_pagamento as $indice => $forma) {
            $formas_de_pagamento[$indice]['codigo_nfce'] = $codigos_nfce[$indice] ?? '99';
        }

        $this->db->table('formas_de_pagamento')->insertBatch($formas_de_pagamento);

        // Vendedor
        $vendedorGeral = $this->db->table('vendedores')->where('nome', 'GERAL')->get()->getRowArray();

        if (empty($vendedorGeral)) {
            $this->db->table('vendedores')->insert([
                'status'                     => "Ativo",
                'nome'                       => 'GERAL',
                'data_inicio_das_atividades' => date('Y-m-d'),
                'anotacoes'                  => 'Vendedor para vendas em geral.'
            ]);
        }

        // Técnico
        $this->db->table('tecnicos')->insert([
            'nome'      => "GERAL",
            'cpf'       => "S/N",
            'celular_1' => "S/N",
        ]);
    }
}
