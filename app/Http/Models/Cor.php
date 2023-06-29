<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method orderBy(string $string, string $string1)
 */
class Cor extends Model
{
    public $table = 'loja_cores';

    protected $fillable = ['id','nome','status'];

    function produtos() {
        return  $this->hasMany('App\Http\Models\Cor');
    }
}
