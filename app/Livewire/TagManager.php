<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tag;
use App\Models\Task;

class TagManager extends Component
{
    public $taskId;
    public $tagInput = '';
    public $suggestions = [];
    public $task;

    public function mount($taskId)
    {
        $this->taskId = $taskId;
        $this->task = Task::with('tags')->findOrFail($taskId);
    }

    public function updatedTagInput($value)
    {
        $this->suggestions = Tag::where('name', 'like', '%' . $value . '%')
            ->limit(5)
            ->pluck('name')
            ->toArray();
    }

    public function addTag($name = null)
    {
        $name = $name ?? trim($this->tagInput);
        if (!$name) return;
        $tag = Tag::firstOrCreate(['name' => $name]);
        $this->task->tags()->syncWithoutDetaching([$tag->id]);
        $this->task->refresh();
        $this->tagInput = '';
        $this->suggestions = [];
    }

    public function removeTag($tagId)
    {
        $this->task->tags()->detach($tagId);
        $this->task->refresh();
    }

    public function render()
    {
        return view('livewire.tag-manager', [
            'tags' => $this->task->tags,
        ]);
    }
}
