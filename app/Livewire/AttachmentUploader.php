<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Attachment;
use App\Notifications\TaskCommented;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use Illuminate\Support\Facades\Http;

class AttachmentUploader extends Component
{
    use WithFileUploads;

    public $taskId;
    public $file;
    public $file_type;
    public $alt_text;

    protected $rules = [
        'file' => 'required|file|max:10240', // max 10MB
        'file_type' => 'required|string|max:100',
        'alt_text' => 'nullable|string|max:255',
    ];

    public function index()
    {
        // ad esempio, restituisci tutti gli allegati
        $attachments = Attachment::orderByDesc('created_at')->get();
        return view('attachments.index', compact('attachments'));
    }

    public function upload()
    {
        $this->validate();
        $path = $this->file->store('attachments', 'public');
        $attachment = Attachment::create([
            'task_id' => $this->taskId,
            'user_id' => Auth::id(),
            'filepath' => $path,
            'file_type' => $this->file_type,
            'alt_text' => $this->alt_text,
        ]);
        // Notifica agli assegnatari
        $task = Task::with('assignedUsers')->find($this->taskId);
        $user = Auth::user();
        foreach ($task->assignedUsers as $assignee) {
            if ($assignee->id !== $user->id) {
                $assignee->notify(new TaskCommented($task, (object)['body' => 'Nuovo allegato caricato: ' . $this->file->getClientOriginalName()], $user));
            }
        }
        // Notifica webhook/Slack
        if ($task->webhook_url) {
            try {
                Http::post($task->webhook_url, [
                    'text' => "[Task] {$task->title} - Nuovo allegato caricato da {$user->name}: {$this->file->getClientOriginalName()}" . ($task->slack_channel ? " (canale: {$task->slack_channel})" : ""),
                ]);
            } catch (\Exception $e) {
                // Log errori webhook
            }
        }
        $this->reset(['file', 'file_type', 'alt_text']);
        $this->dispatch('attachmentUploaded');
        // Logging attività
        Activity::create([
            'task_id' => $attachment->task_id,
            'user_id' => $user->id,
            'description' => 'Ha caricato un nuovo allegato',
        ]);
    }

    public function render()
    {
        $attachments = Attachment::where('task_id', $this->taskId)->orderByDesc('created_at')->get();
        return view('livewire.attachment-uploader', compact('attachments'));
    }
}
