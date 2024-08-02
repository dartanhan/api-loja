<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Lojas extends Model
{
    public $table = 'loja_lojas';
    protected $fillable = ['nome','status','cnpj','endereco','local'];

    public function vendas()
    {
        return $this->hasMany(Vendas::class, 'loja_id');
    }

    function produtos() {
        return  $this->hasMany('App\\Http\Models\ProdutoQuantidade');
    }
}
