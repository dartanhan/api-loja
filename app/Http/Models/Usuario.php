<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    public $table = 'loja_usuarios';
    protected $fillable = ['nome','login','senha','status','admin','loja_id','sexo'];

}
