<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class contaPagarReceber extends Model
{
    public $table = 'loja_gastos';

    protected $fillable = ['id','nome','status'];
}
