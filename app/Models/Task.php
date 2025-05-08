<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'is_page', 'slug', 'status', 'priority', 'deadline', 'created_by', 'is_template',
        'figma_url', 'notion_url', 'github_url', 'slack_channel', 'webhook_url',
        // aggiungi altri campi se necessario
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'task_role');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_role', 'task_id', 'assigned_user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_task');
    }

    public function sharedPages()
    {
        return $this->hasMany(SharedPage::class);
    }

    public function shares()
    {
        return $this->hasMany(TaskShare::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function internalShares()
    {
        return $this->hasMany(InternalTaskShare::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public static function duplicateFromTemplate(Task $template, $userId)
    {
        $newTask = self::create([
            'title' => $template->title,
            'description' => $template->description,
            'is_page' => $template->is_page,
            'slug' => null,
            'status' => 'draft',
            'priority' => $template->priority,
            'deadline' => null,
            'created_by' => $userId,
            'is_template' => false,
        ]);
        // Duplica relazioni (tag, ruoli)
        $newTask->tags()->sync($template->tags->pluck('id'));
        $newTask->roles()->sync($template->roles->pluck('id'));
        // (opzionale: duplica meta SEO, checklist, ecc.)
        return $newTask;
    }
}
