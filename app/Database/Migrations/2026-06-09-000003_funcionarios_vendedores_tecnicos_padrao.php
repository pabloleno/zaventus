<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FuncionariosVendedoresTecnicosPadrao extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('tipo_funcionario', 'funcionarios')) {
            $this->forge->modifyColumn('funcionarios', [
                'tipo_funcionario' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'default'    => 'Outros',
                    'null'       => true,
                ],
            ]);
        }

        $this->adicionarCampo('tecnicos', 'id_funcionario', [
            'type'       => 'INT',
            'constraint' => 9,
            'null'       => true,
            'after'      => 'id_tecnico',
        ]);

        $this->adicionarCampo('tecnicos', 'status', [
            'type'       => 'VARCHAR',
            'constraint' => 32,
            'default'    => 'Ativo',
            'after'      => 'id_funcionario',
        ]);

        $this->garantirClientePadrao();
        $this->garantirFornecedorPadrao();
        $idFuncionario = $this->garantirFuncionarioPadrao();
        $this->garantirVendedorPadrao($idFuncionario);
        $this->garantirTecnicoPadrao($idFuncionario);
        $this->vincularTecnicosExistentes();
    }

    public function down()
    {
        if ($this->db->fieldExists('status', 'tecnicos')) {
            $this->forge->dropColumn('tecnicos', 'status');
        }

        if ($this->db->fieldExists('id_funcionario', 'tecnicos')) {
            $this->forge->dropColumn('tecnicos', 'id_funcionario');
        }

        if ($this->db->fieldExists('tipo_funcionario', 'funcionarios')) {
            $this->forge->modifyColumn('funcionarios', [
                'tipo_funcionario' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 16,
                    'default'    => 'Outros',
                    'null'       => true,
                ],
            ]);
        }
    }

    private function adicionarCampo(string $tabela, string $campo, array $definicao): void
    {
        if (! $this->db->fieldExists($campo, $tabela)) {
            $this->forge->addColumn($tabela, [$campo => $definicao]);
        }
    }

    private function garantirClientePadrao(): void
    {
        if ($this->db->table('clientes')->where('id_cliente', 1)->countAllResults() > 0) {
            return;
        }

        $this->db->table('clientes')->insert([
            'id_cliente' => 1,
            'tipo' => 1,
            'nome' => 'Consumidor Final',
            'data_de_nascimento' => '1900-01-01',
            'rg' => 'S/N',
            'cpf' => '',
            'razao_social' => '',
            'nome_fantasia' => '',
            'cnpj' => '',
            'ie' => '',
            'cep' => '',
            'logradouro' => '',
            'numero' => '',
            'complemento' => '',
            'bairro' => '',
            'municipio' => '',
            'codigo_do_municipio' => '',
            'UF' => '',
            'celular' => '',
            'whatsapp' => '',
            'telefone_fixo' => '',
            'comercial' => '',
            'residencial' => '',
            'email' => '',
            'anotacoes' => 'Cliente padrao do sistema.',
            'foto' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => '0000-00-00 00:00:00',
        ]);
    }

    private function garantirFornecedorPadrao(): void
    {
        if ($this->db->table('fornecedores')->where('id_fornecedor', 1)->countAllResults() > 0) {
            return;
        }

        $this->db->table('fornecedores')->insert([
            'id_fornecedor' => 1,
            'nome_do_representante' => 'GERAL',
            'nome_da_empresa' => 'GERAL',
            'cnpj' => '',
            'ie' => '',
            'cep' => '',
            'logradouro' => '',
            'numero' => '',
            'complemento' => '',
            'bairro' => '',
            'municipio' => '',
            'UF' => '',
            'codigo_do_municipio' => '',
            'celular' => '',
            'whatsapp' => '',
            'telefone_fixo' => '',
            'comercial' => '',
            'email' => '',
            'anotacoes' => 'Fornecedor padrao do sistema.',
            'foto' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => '0000-00-00 00:00:00',
        ]);
    }

    private function garantirFuncionarioPadrao(): int
    {
        $funcionario = $this->db->table('funcionarios')
            ->where('nome', 'GERAL')
            ->get()
            ->getRowArray();

        if (empty($funcionario)) {
            $this->db->table('funcionarios')->insert([
                'status' => 'Ativo',
                'tipo_funcionario' => 'Vendedor e Tecnico',
                'nome' => 'GERAL',
                'data_de_nascimento' => '1900-01-01',
                'rg' => 'S/N',
                'cpf' => '',
                'cep' => '',
                'logradouro' => '',
                'numero' => '',
                'complemento' => '',
                'bairro' => '',
                'municipio' => '',
                'UF' => '',
                'codigo_do_municipio' => '',
                'celular' => '',
                'whatsapp' => '',
                'telefone_fixo' => '',
                'comercial' => '',
                'residencial' => '',
                'email' => '',
                'cargo' => 'Geral',
                'data_de_contratacao' => date('Y-m-d'),
                'data_inicio_das_atividades' => date('Y-m-d'),
                'salario' => 0,
                'detalhes_da_atividade' => 'Funcionario padrao do sistema.',
                'anotacoes' => 'Funcionario padrao para vendas e ordens de servico.',
                'foto' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => '0000-00-00 00:00:00',
            ]);

            return (int) $this->db->insertID();
        }

        $this->db->table('funcionarios')
            ->where('id_funcionario', $funcionario['id_funcionario'])
            ->update([
                'status' => 'Ativo',
                'tipo_funcionario' => 'Vendedor e Tecnico',
            ]);

        return (int) $funcionario['id_funcionario'];
    }

    private function garantirVendedorPadrao(int $idFuncionario): void
    {
        $vendedor = $this->db->table('vendedores')->where('nome', 'GERAL')->get()->getRowArray();

        if (empty($vendedor)) {
            $this->db->table('vendedores')->insert([
                'id_funcionario' => $idFuncionario,
                'status' => 'Ativo',
                'nome' => 'GERAL',
                'data_inicio_das_atividades' => date('Y-m-d'),
                'anotacoes' => 'Vendedor padrao do sistema.',
                'foto' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => '0000-00-00 00:00:00',
            ]);

            return;
        }

        $this->db->table('vendedores')
            ->where('id_vendedor', $vendedor['id_vendedor'])
            ->update([
                'id_funcionario' => $idFuncionario,
                'status' => 'Ativo',
            ]);
    }

    private function garantirTecnicoPadrao(int $idFuncionario): void
    {
        $tecnico = $this->db->table('tecnicos')->where('nome', 'GERAL')->get()->getRowArray();

        if (empty($tecnico)) {
            $this->db->table('tecnicos')->insert([
                'id_funcionario' => $idFuncionario,
                'status' => 'Ativo',
                'nome' => 'GERAL',
                'cpf' => '',
                'rg' => 'S/N',
                'data_de_nascimento' => '1900-01-01',
                'sexo' => 'N/I',
                'email' => '',
                'comissao' => 0,
                'observacoes' => 'Tecnico padrao do sistema.',
                'foto' => '',
                'celular' => '',
                'whatsapp' => '',
                'telefone_fixo' => '',
                'fixo' => '',
                'celular_1' => '',
                'celular_2' => '',
                'cep' => '',
                'logradouro' => '',
                'numero' => '',
                'complemento' => '',
                'bairro' => '',
                'cidade' => '',
                'uf' => '',
                'codigo_do_municipio' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => '0000-00-00 00:00:00',
            ]);

            return;
        }

        $this->db->table('tecnicos')
            ->where('id_tecnico', $tecnico['id_tecnico'])
            ->update([
                'id_funcionario' => $idFuncionario,
                'status' => 'Ativo',
            ]);
    }

    private function vincularTecnicosExistentes(): void
    {
        $tecnicos = $this->db->table('tecnicos')
            ->where('id_funcionario IS NULL', null, false)
            ->where('nome !=', 'GERAL')
            ->get()
            ->getResultArray();

        foreach ($tecnicos as $tecnico) {
            $funcionario = $this->db->table('funcionarios')
                ->where('nome', $tecnico['nome'])
                ->get()
                ->getRowArray();

            if (empty($funcionario)) {
                continue;
            }

            $tipo = $this->adicionarAtuacao($funcionario['tipo_funcionario'] ?? 'Outros', 'Tecnico');

            $this->db->table('funcionarios')
                ->where('id_funcionario', $funcionario['id_funcionario'])
                ->update(['tipo_funcionario' => $tipo]);

            $this->db->table('tecnicos')
                ->where('id_tecnico', $tecnico['id_tecnico'])
                ->update(['id_funcionario' => $funcionario['id_funcionario']]);
        }
    }

    private function adicionarAtuacao(string $tipo, string $atuacao): string
    {
        $vendedor = stripos($tipo, 'Vendedor') !== false || $atuacao === 'Vendedor';
        $tecnico = stripos($tipo, 'Tecnico') !== false || stripos($tipo, 'Técnico') !== false || $atuacao === 'Tecnico';

        if ($vendedor && $tecnico) {
            return 'Vendedor e Tecnico';
        }

        if ($vendedor) {
            return 'Vendedor';
        }

        return $tecnico ? 'Tecnico' : 'Outros';
    }
}
