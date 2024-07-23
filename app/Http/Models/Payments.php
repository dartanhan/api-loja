<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 * @method static create(array $all)
 * @method static find($input)
 */
class Payments extends Model
{
    public $table = 'loja_forma_pagamentos';
    protected $fillable = ['id','nome','status','slug','update_at'];

    public function PaymentsTaxes()
    {
        return $this->hasMany( TaxaCartao::class,'forma_id','id');
    }
}
