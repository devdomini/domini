<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportThread extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'unread_admin',
        'unread_user',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'unread_admin' => 'integer',
        'unread_user' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at');
    }

    public function latestMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SupportMessage::class)->latestOfMany();
    }
}
