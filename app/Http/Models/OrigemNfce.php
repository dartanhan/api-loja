<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrigemNfce extends Model
{
    use HasFactory;

    public $table = 'loja_produto_origem_nfces';
    protected $fillable = ['id','codigo','descricao','created_at','update_at'];

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
