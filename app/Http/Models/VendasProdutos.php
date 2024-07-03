<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class VendasProdutos extends Model implements Auditable
{
    use AuditableTrait;

    public $table = 'loja_vendas_produtos';
    public $timestamps = false;
    protected $fillable = ['venda_id','codigo_produto','descricao','valor_produto','quantidade','categoria_id','fornecedor_id','created_at'];

    public function vendas() {
        return $this->belongsTo('App\Http\Models\Vendas');
    }

    function productsSales() {
        return  $this->hasMany(ProdutoVariation::class,'subcodigo', 'codigo_produto');
    }

    function produtoVariation() {
        return  $this->hasMany(ProdutoVariation::class,'subcodigo', 'codigo_produto');
    }

}
