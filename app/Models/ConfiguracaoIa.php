<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracaoIa extends Model
{
    protected $table = 'configuracoes_ia';

    protected $fillable = [
        'provedor',
        'api_key',
        'modelo',
        'ativo'
    ];
}
