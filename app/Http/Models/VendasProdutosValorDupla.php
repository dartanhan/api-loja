<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VendasProdutosValorDupla extends Model
{
    public $table = 'loja_vendas_produtos_valor_duplas';
    public $timestamps = false;
    protected $fillable = ['venda_id', 'valor_cartao', 'valor_dinheiro'];
}
