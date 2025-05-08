<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Role;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class TaskList extends Component
{
    public $status = '';
    public $priority = '';
    public $role = '';
    public $assignee = '';
    public $tags = [];
    public $search = '';
    public $perPage = 10;
    public $showTemplates = false;
    public $tagSearch = '';

    public function render()
    {
        if ($this->showTemplates) {
            $templates = Task::where('is_template', true)->with(['roles', 'tags'])->get();
            return view('livewire.task-templates', compact('templates'));
        }

        $query = Task::query();

        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->priority) {
            $query->where('priority', $this->priority);
        }
        if ($this->role) {
            $query->whereHas('roles', fn($q) => $q->where('roles.id', $this->role));
        }
        if ($this->assignee) {
            $query->whereHas('assignedUsers', fn($q) => $q->where('users.id', $this->assignee));
        }
        if (!empty($this->tags)) {
            foreach ($this->tags as $tagId) {
                $query->whereHas('tags', fn($q) => $q->where('tags.id', $tagId));
            }
        }
        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        $tasks = $query->with(['roles', 'assignedUsers', 'tags'])->orderByDesc('created_at')->paginate($this->perPage);
        $roles = Role::all();
        $users = User::all();
        $allTags = Tag::all();

        return view('livewire.task-list', [
            'tasks' => $tasks,
            'roles' => $roles,
            'users' => $users,
            'allTags' => $allTags,
            'tags' => $this->tags,
            'tagSearch' => $this->tagSearch,
        ]);
    }

    public function showTemplates()
    {
        $this->showTemplates = true;
    }

    public function showTasks()
    {
        $this->showTemplates = false;
    }

    public function duplicateFromTemplate($templateId)
    {
        $template = Task::findOrFail($templateId);
        $newTask = Task::duplicateFromTemplate($template, Auth::id());
        return redirect()->route('tasks.show', $newTask->id);
    }

    public function addTagFilter($tagId)
    {
        if (!in_array($tagId, $this->tags)) {
            $this->tags[] = $tagId;
        }
    }

    public function removeTagFilter($tagId)
    {
        $this->tags = array_values(array_diff($this->tags, [$tagId]));
    }
}
