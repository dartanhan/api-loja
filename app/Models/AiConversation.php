<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    protected $table = 'ai_conversations';

    protected $fillable = [
        'user_id',
        'titulo'
    ];

    public function messages()
    {
        return $this->hasMany(AiMessage::class, 'conversation_id');
    }
}
