<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    use HasFactory;
    public $table = 'loja_despesas';
    protected $fillable = ['descricao','valor','data'];

    // configurar o modelo para tratar data como date
    //Assim, o campo data será automaticamente convertido em Carbon sempre que for acessado.
    protected $casts = [
        'data' => 'date',
    ];

}
