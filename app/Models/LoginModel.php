<?php

namespace App\Models;

class LoginModel extends PadraoModel
{
    protected $table = 'login';
    protected $primaryKey = 'id_login';
    protected $allowedFields = [
        'id_login',
        'usuario',
        'senha',
        'primeiro_nome',
        'ultimo_acesso',
        'tema',
        'controle_de_acesso'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
