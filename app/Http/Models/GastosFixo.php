<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class GastosFixo extends Model
{
    public $table = 'loja_gastos_fixos';
    protected $fillable = ['descricao','valor','tipo_gasto'];

    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y H:i:s', strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d/m/Y H:i:s', strtotime($value));
    }

}
