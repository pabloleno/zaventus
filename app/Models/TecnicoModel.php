<?php

namespace App\Models;

class TecnicoModel extends PadraoModel
{
    private const STATUS_REMOVIDO = 'Removido';

    protected $table = 'tecnicos';
    protected $primaryKey = 'id_tecnico';
    protected $allowedFields = [
        'id_tecnico',
        'id_funcionario',
        'status',
        'nome',
        'cpf',
        'rg',
        'data_de_nascimento',
        'sexo',
        'email',
        'comissao',
        'observacoes',
        'foto',
        'celular',
        'whatsapp',
        'telefone_fixo',
        'fixo',
        'celular_1',
        'celular_2',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'codigo_do_municipio'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function visiveis(): array
    {
        return $this->db->table($this->table)
            ->where('status !=', self::STATUS_REMOVIDO)
            ->orderBy('nome', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function paraOrdem(): array
    {
        return $this->db->table($this->table)
            ->where('status', 'Ativo')
            ->orderBy('nome', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function sincronizarFuncionario(array $funcionario, int $idFuncionario): void
    {
        $tecnico = $this->db->table($this->table)
            ->where('id_funcionario', $idFuncionario)
            ->get()
            ->getRowArray();

        if (! FuncionarioModel::atuaComo($funcionario, 'Tecnico')) {
            if (! empty($tecnico)) {
                $this->update($tecnico['id_tecnico'], ['status' => self::STATUS_REMOVIDO]);
            }

            return;
        }

        $dados = [
            'id_funcionario' => $idFuncionario,
            'status' => ($funcionario['status'] ?? 'Ativo') === 'Ativo' ? 'Ativo' : 'Desligado',
            'nome' => $funcionario['nome'] ?? '',
            'cpf' => $funcionario['cpf'] ?? '',
            'rg' => $funcionario['rg'] ?? '',
            'data_de_nascimento' => $funcionario['data_de_nascimento'] ?? '',
            'sexo' => $tecnico['sexo'] ?? 'N/I',
            'email' => $funcionario['email'] ?? '',
            'comissao' => $tecnico['comissao'] ?? 0,
            'observacoes' => $funcionario['anotacoes'] ?? '',
            'foto' => $funcionario['foto'] ?? '',
            'celular' => $funcionario['celular'] ?? '',
            'whatsapp' => $funcionario['whatsapp'] ?? '',
            'telefone_fixo' => $funcionario['telefone_fixo'] ?? '',
            'fixo' => $funcionario['telefone_fixo'] ?? '',
            'celular_1' => $funcionario['celular'] ?? '',
            'celular_2' => $funcionario['whatsapp'] ?? '',
            'cep' => $funcionario['cep'] ?? '',
            'logradouro' => $funcionario['logradouro'] ?? '',
            'numero' => $funcionario['numero'] ?? '',
            'complemento' => $funcionario['complemento'] ?? '',
            'bairro' => $funcionario['bairro'] ?? '',
            'cidade' => $funcionario['municipio'] ?? '',
            'uf' => $funcionario['UF'] ?? '',
            'codigo_do_municipio' => $funcionario['codigo_do_municipio'] ?? '',
        ];

        if (! empty($tecnico)) {
            $dados['id_tecnico'] = $tecnico['id_tecnico'];
        }

        $this->save($dados);
    }

    public function ocultarPorFuncionario(int $idFuncionario): void
    {
        $tecnico = $this->db->table($this->table)
            ->where('id_funcionario', $idFuncionario)
            ->get()
            ->getRowArray();

        if (! empty($tecnico)) {
            $this->update($tecnico['id_tecnico'], ['status' => self::STATUS_REMOVIDO]);
        }
    }

    public function ocultar(int $idTecnico): void
    {
        $this->update($idTecnico, ['status' => self::STATUS_REMOVIDO]);
    }

    public function ehGeral(array $tecnico): bool
    {
        return strtoupper(trim((string) ($tecnico['nome'] ?? ''))) === 'GERAL';
    }
}
