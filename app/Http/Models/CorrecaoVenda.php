<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrecaoVenda extends Model
{
    use HasFactory;

    public $table = 'loja_correcao_vendas';
    protected $fillable = ['json_corrigido','valor','data'];
}
