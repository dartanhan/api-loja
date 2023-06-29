<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoCodigo extends Model
{
    public $timestamps = false;
    public $table = 'loja_produtos_codigos';
    protected $fillable = ['codigo'];

}
