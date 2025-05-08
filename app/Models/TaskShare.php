<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskShare extends Model
{
    protected $fillable = [
        'task_id', 'token', 'expires_at', 'created_by', 'is_active'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
