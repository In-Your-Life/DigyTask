<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalTaskShare extends Model
{
    protected $fillable = [
        'task_id', 'user_id', 'role_id', 'created_by', 'expires_at', 'is_active'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
