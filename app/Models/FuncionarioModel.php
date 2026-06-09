<?php

namespace App\Models;

class FuncionarioModel extends PadraoModel
{
    public const TIPO_OUTROS = 'Outros';
    public const TIPO_VENDEDOR = 'Vendedor';
    public const TIPO_TECNICO = 'Tecnico';
    public const TIPO_VENDEDOR_TECNICO = 'Vendedor e Tecnico';

    protected $table = 'funcionarios';
    protected $primaryKey = 'id_funcionario';
    protected $allowedFields = [
        'id_funcionario',
        'status',
        'tipo_funcionario',
        'nome',
        'data_de_nascimento',
        'rg',
        'cpf',
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
        'cargo',
        'data_de_contratacao',
        'data_inicio_das_atividades',
        'salario',
        'detalhes_da_atividade',
        'foto',
        'anotacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public static function normalizarTipo(string $tipo): string
    {
        $vendedor = stripos($tipo, 'Vendedor') !== false;
        $tecnico = stripos($tipo, 'Tecnico') !== false || stripos($tipo, 'Técnico') !== false;

        if ($vendedor && $tecnico) {
            return self::TIPO_VENDEDOR_TECNICO;
        }

        if ($vendedor) {
            return self::TIPO_VENDEDOR;
        }

        return $tecnico ? self::TIPO_TECNICO : self::TIPO_OUTROS;
    }

    public static function atuaComo(array $funcionario, string $atuacao): bool
    {
        return stripos(self::normalizarTipo((string) ($funcionario['tipo_funcionario'] ?? '')), $atuacao) !== false;
    }

    public static function removerAtuacao(array $funcionario, string $atuacao): string
    {
        $vendedor = self::atuaComo($funcionario, 'Vendedor') && $atuacao !== 'Vendedor';
        $tecnico = self::atuaComo($funcionario, 'Tecnico') && $atuacao !== 'Tecnico';

        if ($vendedor && $tecnico) {
            return self::TIPO_VENDEDOR_TECNICO;
        }

        if ($vendedor) {
            return self::TIPO_VENDEDOR;
        }

        return $tecnico ? self::TIPO_TECNICO : self::TIPO_OUTROS;
    }

    public static function descricaoTipo(string $tipo): string
    {
        return str_replace('Tecnico', 'Técnico', self::normalizarTipo($tipo));
    }
}
