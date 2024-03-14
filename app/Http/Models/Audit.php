<?php

namespace App\Http\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model implements \OwenIt\Auditing\Contracts\Audit
{
    use HasFactory;

    use \OwenIt\Auditing\Audit;

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values'   => 'json',
        'new_values'   => 'json',
        // Note: Please do not add 'auditable_id' in here, as it will break non-integer PK models
    ];

    public function getSerializedDate($date)
    {
        return $this->serializeDate($date);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productNew()
    {
        return $this->belongsTo(Produto::class);
    }
}
