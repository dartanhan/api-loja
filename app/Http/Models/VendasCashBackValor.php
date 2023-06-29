<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class VendasCashBackValor extends Model
{
    public $table = 'loja_vendas_cashback_valor';
    protected $fillable = ['cliente_id','valor_total','created_at','update_at'];

}
