<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareAccessLog extends Model
{
    protected $fillable = [
        'task_share_id', 'ip_address', 'user_agent', 'accessed_at'
    ];
}
