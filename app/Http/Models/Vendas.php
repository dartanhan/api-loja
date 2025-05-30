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
    protected $fillable = ['codigo_venda','loja_id','valor_total','troca','cliente_id','tipo_venda_id','forma_entrega_id','usuario_id','created_at'];

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

    public function pagamentos()
    {
       // return $this->belongsToMany(Payments::class, 'loja_vendas_produtos_tipo_pagamentos', 'venda_id', 'forma_pagamento_id');
        return $this->hasMany(VendasProdutosTipoPagamento::class, 'venda_id', 'id')->with('formaPagamento');
    }

    function formaPgto(){
        return $this->hasMany(VendasProdutosTipoPagamento::class, 'venda_id');
     }

    function cashback() {
        return $this->hasMany(VendasCashBack::class, 'venda_id', 'id');
    }

    function entregas() {
        return $this->hasMany(VendasProdutosEntrega::class, 'venda_id', 'id');
    }

    public function loja()
    {
        return $this->belongsTo(Lojas::class, 'loja_id');
    }

    public function tipoVenda(){
        return $this->belongsTo(TipoVenda::class, 'tipo_venda_id');
    }

    public function cliente(){
        return $this->belongsTo(ClienteModel::class, 'cliente_id');
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function entrega(){
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function frete(){
        return $this->hasMany(VendasProdutosEntrega::class, 'venda_id');
    }

    public function troca()
    {
        return $this->hasOne(VendasTroca::class, 'nova_venda_id');
    }
}
