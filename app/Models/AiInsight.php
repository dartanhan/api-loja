<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiInsight extends Model
{
    protected $table = 'ai_insights';

    protected $fillable = [
        'tipo',
        'severidade',
        'titulo',
        'descricao',
        'dados',
        'lido'
    ];

    protected $casts = [
        'dados' => 'array',
        'lido' => 'boolean'
    ];
}
