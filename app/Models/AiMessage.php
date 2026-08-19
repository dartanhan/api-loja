<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiMessage extends Model
{
    protected $table = 'ai_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tool_calls'
    ];

    protected $casts = [
        'tool_calls' => 'array'
    ];

    public function conversation()
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }
}
