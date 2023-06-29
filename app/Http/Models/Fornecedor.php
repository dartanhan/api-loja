<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @method orderBy(string $string, string $string1)
 * @method static create(array $all)
 * @method static find($input)
 */
class Fornecedor extends Model
{
    public $table = 'loja_fornecedores';

    protected $fillable = ['id','nome','status','updated_at'];
    //protected $dates = ['created_at','updated_at'];
    protected $appends = ['datacriacao','dataatualizacao'];


   function produtos() {
       return  $this->hasMany('App\Http\Models\Fornecedor');
   }

  /*  public function getDatacriacaoAttribute()
    {
        return date('d/m/Y H:i:s', strtotime($this->attributes['created_at']));
    }
    public function getDataatualizacaoAttribute()
    {
        return date('d/m/Y H:i:s', strtotime($this->attributes['updated_at']));
    }*/
}
