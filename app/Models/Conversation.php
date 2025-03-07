<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_id',
    ];

    public function userOne(): BelongsTo {

        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo(): BelongsTo {

        return $this->belongsTo(User::class, 'user_two_id');
    }
}
