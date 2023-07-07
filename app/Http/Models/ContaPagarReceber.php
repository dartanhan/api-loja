<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ContaPagarReceber extends Model
{
    public $table = 'loja_gastos';

    protected $fillable = ['id','nome','status'];
}
