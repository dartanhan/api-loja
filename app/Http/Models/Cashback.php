<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Cashback extends Model
{
    public $table = 'loja_cashback';
    protected $fillable = ['taxa','valor','status','created_at','updated_at'];
   // protected $dates = ['created_at','updated_at'];

    //data formatada d/m/Y H:i:s
    protected $appends = ['created_at','updated_at'];


    public function getCreatedAtAttribute()
    {
        return date('d/m/Y H:i:s', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute()
    {
        return date('d/m/Y H:i:s', strtotime($this->attributes['updated_at']));
    }

}
