<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedPage extends Model
{
    protected $fillable = [
        'task_id', 'html_content', 'generated_at', 'version', 'editable_html_content', 'edited_by'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
