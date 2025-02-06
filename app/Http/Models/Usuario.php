<?php

namespace App\Http\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    public $table = 'loja_usuarios';
    protected $fillable = ['nome','login','senha','status','admin','loja_id','sexo'];
    // Ocultando colunas específicas
    protected $hidden = ['senha', 'password','sexo','created_at','updated_at','admin'];

    public function vendas()
    {
        return $this->belongsTo(Vendas::class, 'usuario_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
