<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoQuantidade extends Model
{
    public $table = 'loja_produtos_quantidade';
    protected $fillable = ['status','quantidade','quantidade_minima','produto_id','loja_id','cor_id'];
    public $timestamps = false;

    protected $with = ['produtos','lojas','fornecedores','cores'];

    public function produtos() {
        return $this->belongsTo('App\Http\Models\Product');
    }

    public function lojas() {
        return $this->belongsTo('App\Http\Models\Lojas');
    }

    public function fornecedores() {
        return $this->belongsTo('App\Http\Models\Fornecedor');
    }

    public function cores() {
        return $this->belongsTo('App\Http\Models\Cor');
    }
}
