<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    public $table = 'loja_categorias';
    protected $primaryKey = 'id';
    protected $fillable = ['nome','quantidade','status'];

    function produtos() {
        return  $this->hasMany('App\Http\Models\Categoria');
    }

}
