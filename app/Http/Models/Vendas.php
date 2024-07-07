<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * @method static join(string $string, string $string1, string $string2, string $string3)
 */
class Vendas extends Model implements Auditable
{
    use AuditableTrait;

    public $table = 'loja_vendas';
    protected $fillable = ['codigo_venda','loja_id','valor_total','troca','cliente_id','tipo_venda_id','usuario_id','created_at','created_at'];

    // Define o relacionamento hasMany
    public function produtos()
    {
        return $this->hasMany(VendasProdutos::class, 'venda_id');
    }

    function vendas() {
        return  $this->hasMany('App\Http\Models\VendasProdutos', 'venda_id','id');
    }

    function quantityProduct(){
        return $this->hasMany(VendasProdutos::class, 'venda_id', 'id');
    }

    function descontos(){
        return $this->hasMany(VendasProdutosDesconto::class ,'venda_id');
    }

    function formaPgto(){
        return $this->hasMany(VendasProdutosTipoPagamento::class, 'venda_id');
     }

    function cashback() {
        return  $this->hasMany(VendasCashBack::class,'venda_id');
    }
}
