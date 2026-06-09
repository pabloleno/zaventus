<?php

namespace App\Models;

class ClienteModel extends PadraoModel
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $allowedFields = [
        'tipo',
        'nome',
        'data_de_nascimento',
        'rg',
        'cpf',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'ie',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'UF',
        'codigo_do_municipio',
        'celular',
        'whatsapp',
        'telefone_fixo',
        'comercial',
        'residencial',
        'email',
        'foto',
        'anotacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function consumidorFinal(): array
    {
        $cliente = $this->db->table($this->table)
            ->where('id_cliente', 1)
            ->get()
            ->getRowArray();

        if (empty($cliente)) {
            $cliente = $this->db->table($this->table)
                ->where('nome', 'Consumidor Final')
                ->get()
                ->getRowArray();
        }

        return $cliente ?? [];
    }

    public function idConsumidorFinal(): int
    {
        $cliente = $this->consumidorFinal();

        return (int) ($cliente['id_cliente'] ?? 0);
    }
}
