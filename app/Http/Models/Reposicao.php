<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reposicao extends Model
{
    use HasFactory;
    public $table = 'loja_vendas_produtos';


    public function variacoes()
    {
        return $this->hasMany(ProdutoVariation::class, 'subcodigo', 'codigo_produto');
    }
}

