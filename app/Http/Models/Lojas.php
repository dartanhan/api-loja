<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Lojas extends Model
{
    public $table = 'loja_lojas';
    protected $fillable = ['nome','status','cnpj','endereco','local'];

    function produtos() {
        return  $this->hasMany('App\\Http\Models\ProdutoQuantidade');
    }
}
