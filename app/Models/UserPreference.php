<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id', 'per_page', 'theme', 'notifications_enabled', 'email_notifications', 'granular_notifications'
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'email_notifications' => 'boolean',
        'granular_notifications' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 