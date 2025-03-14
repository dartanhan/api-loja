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
    protected $fillable = ['venda_id','codigo_produto','descricao','valor_produto','troca','quantidade','categoria_id','loja_venda_id_troca','fornecedor_id','created_at'];
    protected $casts = [
        'troca' => 'boolean',
    ];
     // Define o relacionamento belongsTo
     public function vendas()
     {
         return $this->belongsTo(Vendas::class, 'venda_id');
     }

    function productsSales() {
        return  $this->hasMany(ProdutoVariation::class,'subcodigo', 'codigo_produto');
    }

    function produtoVariation() {
        return  $this->hasMany(ProdutoVariation::class,'subcodigo', 'codigo_produto');
    }
}
