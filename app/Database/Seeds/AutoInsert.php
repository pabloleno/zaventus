<?php

namespace App\Database\Seeds;

class AutoInsert extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        // Dados da Empresa
        $this->db->table('config_empresa')->insert([
            'nome_fantasia' => 'Sua Empresa',
            'telefone'        => '6335710000',
            'endereco'        => 'Av. 01 Quadra 02 Lote 03'
        ]);

        // Dados do Usuário
        $this->db->table('login')->insert([
            'usuario'            => 'admin',
            'senha'              => '123',
            'primeiro_nome'      => 'Administrador',
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
            'cUF'                       => '17',
            'natOp'                     => 'VENDA DE MERCADORIAS',
            'serie'                     => '1',
            'nNF'                       => '1',
            'cMunFG'                    => '1715101',
            'tpAmb'                     => '1',
            'verProc'                   => 'V1.0.1',
            'CNPJ'                      => '1234567890123',
            'xNome'                     => 'NX SISTEMAS AG. DIGITAL - ME',
            'xFant'                     => 'NX SISTEMAS',
            'IE'                        => '1234567890',
            'CRT'                       => '1',
            'CEP'                       => '77610000',
            'xLgr'                      => 'DIOCLECI RIBEIRO DE SOUSA',
            'nro'                       => 'S/N',
            'xCpl'                      => 'AEROPORTO',
            'xBairro'                   => 'AEROPORTO',
            'cMun'                      => '1715101',
            'xMun'                      => 'Palmas TO',
            'UF'                        => 'TO',
            'cPais'                     => '1058',
            'xPais'                     => 'BRASIL',
            'fone'                      => '63992000000',
            'CNPJ_responsavel_tecnico'  => '1234567890123',
            'xContato'                  => 'NX SISTEMAS',
            'email_responsavel_tecnico' => 'nxsistemas@gmail.com',
            'fone_responsavel_tecnico'  => '63992127726'
        ]);

        // Configurações da NFCe
        $this->db->table('config_nfce')->insert([
            'cUF'                       => '17',
            'natOp'                     => 'VENDA DE MERCADORIAS',
            'serie'                     => '1',
            'nNF'                       => '1',
            'cMunFG'                    => '1715101',
            'tpAmb'                     => '1',
            'verProc'                   => 'V1.0.1',
            'CNPJ'                      => '1234567890123',
            'xNome'                     => 'NX SISTEMAS AG. DIGITAL - ME',
            'xFant'                     => 'NX SISTEMAS',
            'IE'                        => '1234567890',
            'CRT'                       => '1',
            'CEP'                       => '77610000',
            'xLgr'                      => 'DIOCLECI RIBEIRO DE SOUSA',
            'nro'                       => 'S/N',
            'xCpl'                      => 'AEROPORTO',
            'xBairro'                   => 'AEROPORTO',
            'cMun'                      => '1715101',
            'xMun'                      => 'Palmas TO',
            'UF'                        => 'TO',
            'cPais'                     => '1058',
            'xPais'                     => 'BRASIL',
            'fone'                      => '63992000000',
            'CNPJ_responsavel_tecnico'  => '1234567890123',
            'xContato'                  => 'NX SISTEMAS',
            'email_responsavel_tecnico' => 'nxsistemas@gmail.com',
            'fone_responsavel_tecnico'  => '63992127726'
        ]);

        // Formas de Pagamento
        $formas_de_pagamento = [
            ['nome' => 'Dinheiro'],
            ['nome' => 'Cartão de Crédito'],
            ['nome' => 'Cartão de Débito'],
            ['nome' => 'Cheque'],
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
            ['nome' => 'PayPal']
        ];
        $this->db->table('formas_de_pagamento')->insertBatch($formas_de_pagamento);

        // Vendedor
        $this->db->table('vendedores')->insert([
            'status'                     => "Ativo",
            'nome'                       => 'GERAL',
            'data_inicio_das_atividades' => date('Y-m-d'),
            'anotacoes'                  => 'Vendedor para vendas em geral.'
        ]);

        // Técnico
        $this->db->table('tecnicos')->insert([
            'nome'      => "GERAL",
            'cpf'       => "S/N",
            'celular_1' => "S/N",
        ]);
    }
}
