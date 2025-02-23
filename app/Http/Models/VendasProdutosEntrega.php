<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendasProdutosEntrega extends Model
{
    use HasFactory;
    public $table = 'loja_vendas_produtos_entregas';
    protected $fillable = ['venda_id','forma_id','valor_entrega','created_at','updated_at'];

    function vendas(){
        return $this->belongsTo(Vendas::class, 'venda_id');
    }

    function formaEntrega(){
        return $this->belongsTo(FormaEntrega::class, 'forma_id');
    }

}
