<?php
namespace App\Http\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VendasCashBack
 *
 * @property int $id
 * @property int $cliente_id
 * @property float $valor
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VendasCashBack extends Model
{
    public $table = 'loja_vendas_cashback';
    protected $fillable = ['cliente_id','venda_id','valor','created_at','update_at'];

     // Define o relacionamento belongsTo


    public static function cashback(string $string, int $id)
    {
        return VendasCashBack::query()->where($string, $id)->where( 'status', 0)->sum('valor');
    }

    public function vendas()
     {
         return $this->belongsTo(Vendas::class, 'venda_id');
     }

     public function cliente(){
         return $this->belongsTo(ClienteModel::class, 'cliente_id');
     }
}
