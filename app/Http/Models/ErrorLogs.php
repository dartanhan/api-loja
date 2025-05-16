<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLogs extends Model
{
    use HasFactory;
    public $table = 'loja_error_logs';
    protected $fillable = ['codigo_venda','mensagem','dados','codigo_erro'];
}
