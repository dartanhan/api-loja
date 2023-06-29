<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoControle extends Model
{
    public $table = 'loja_produtos_controle';
    protected $fillable = ['products_variation_id','valor_custo','quantidade'];
}
