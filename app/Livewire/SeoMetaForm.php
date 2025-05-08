<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;

class SeoMetaForm extends Component
{
    public $taskId;
    public $seo_title;
    public $seo_description;
    public $seo_keywords;

    public function mount($taskId)
    {
        $task = Task::findOrFail($taskId);
        $this->taskId = $taskId;
        $this->seo_title = $task->seo_title;
        $this->seo_description = $task->seo_description;
        $this->seo_keywords = $task->seo_keywords;
    }

    public function save()
    {
        $this->validate([
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_keywords' => 'nullable|string',
        ]);
        $task = Task::findOrFail($this->taskId);
        $task->seo_title = $this->seo_title;
        $task->seo_description = $this->seo_description;
        $task->seo_keywords = $this->seo_keywords;
        $task->save();
        session()->flash('seo_saved', 'Meta SEO salvati!');
    }

    public function render()
    {
        return view('livewire.seo-meta-form');
    }
}
