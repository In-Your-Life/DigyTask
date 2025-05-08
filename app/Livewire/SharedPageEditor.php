<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SharedPage;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class SharedPageEditor extends Component
{
    public $sharedPageId;
    public $taskId;
    public $htmlContent;
    public $sharedPage;

    public function mount($taskId, $sharedPageId)
    {
        $this->taskId = $taskId;
        $this->sharedPageId = $sharedPageId;
        $this->sharedPage = SharedPage::findOrFail($sharedPageId);
        $this->htmlContent = $this->sharedPage->editable_html_content ?? $this->sharedPage->html_content;
    }

    public function save()
    {
        $user = Auth::user();
        // Crea nuova versione
        $newVersion = SharedPage::create([
            'task_id' => $this->sharedPage->task_id,
            'html_content' => $this->sharedPage->html_content, // mantiene la copia originale
            'editable_html_content' => $this->htmlContent,
            'edited_by' => $user->id,
            'generated_at' => now(),
            'version' => 1 + SharedPage::where('task_id', $this->sharedPage->task_id)->max('version'),
        ]);
        // Logging attività
        \App\Models\Activity::create([
            'task_id' => $this->sharedPage->task_id,
            'user_id' => $user->id,
            'description' => 'Ha modificato la copia HTML condivisa (versione ' . $newVersion->version . ')',
        ]);
        session()->flash('success', 'Modifica salvata!');
        // Redirect alla nuova versione
        return redirect()->route('shared-pages.edit', ['task' => $this->taskId, 'sharedPage' => $newVersion->id]);
    }

    public function render()
    {
        return view('livewire.shared-page-editor');
    }
}
