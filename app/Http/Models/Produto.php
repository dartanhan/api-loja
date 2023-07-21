<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @method static create($data)
 */
class Produto extends Model
{
    public $table = 'loja_produtos_new';
    protected $fillable = ['codigo_produto','descricao','status','valor_produto','valor_dinheiro','valor_cartao','percentual','fornecedor_id','categoria_id','cor_id','ncm','cest','origem'];

    function produtos() {
        return  $this->hasMany('App\Http\Models\ProdutoQuantidade');
    }

    function products() {
        return  $this->hasMany(ProdutoVariation::class,'products_id', 'id')
            ->leftJoin('loja_produtos_imagens', 'loja_produtos_variacao.id', '=', 'loja_produtos_imagens.produto_variacao_id')
            ->select("loja_produtos_variacao.*","loja_produtos_imagens.path","loja_produtos_imagens.id as id_image","loja_produtos_imagens.produto_variacao_id");
    }
}