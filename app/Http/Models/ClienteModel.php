<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteModel extends Model
{
    public $table = 'loja_clientes';
    protected $fillable = ['cpf','telefone','nome','cep','logradouro','numero','complemento','bairro','localidade','uf','taxa','email','created_at','update_at'];
}
