<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaEntrega extends Model
{
    use HasFactory;
    public $table = 'loja_forma_entregas';
    protected $fillable = ['descricao','status','slug'];
}
