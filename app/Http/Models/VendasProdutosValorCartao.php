<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VendasProdutosValorCartao extends Model
{
    public $table = 'loja_vendas_produtos_valor_cartoes';
    public $timestamps = false;
    protected $fillable = ['venda_id','valor_desconto'];

}
