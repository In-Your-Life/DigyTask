<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskShare;
use App\Models\SharedPage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\InternalTaskShare;
use App\Models\Role;
use Illuminate\Support\Facades\Http;

class TaskDetail extends Component
{
    public $taskId;
    public $task;
    public $selectedUserId = '';
    public $internalShareUserId = '';
    public $internalShareRoleId = '';
    public $figma_url;
    public $notion_url;
    public $github_url;
    public $slack_channel;
    public $webhook_url;

    public function mount($taskId)
    {
        $this->taskId = $taskId;
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator'
        ])->findOrFail($taskId);
        $this->figma_url = $this->task->figma_url;
        $this->notion_url = $this->task->notion_url;
        $this->github_url = $this->task->github_url;
        $this->slack_channel = $this->task->slack_channel;
        $this->webhook_url = $this->task->webhook_url;
    }

    public function assignUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->task->assignedUsers()->syncWithoutDetaching([$userId]);
        $assigner = Auth::user();
        $user->notify(new TaskAssigned($this->task, $assigner));
        // Logging attività
        \App\Models\Activity::create([
            'task_id' => $this->task->id,
            'user_id' => $assigner->id,
            'description' => 'Ha assegnato il task a ' . $user->name,
        ]);
        // Aggiorna il task per la vista
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator'
        ])->findOrFail($this->taskId);
    }

    public function generatePublicShare()
    {
        // Genera token unico
        $token = Str::uuid()->toString();
        // Renderizza la vista Blade in HTML statico
        $html = view('public.task-share', ['task' => $this->task])->render();
        // Salva shared_page
        $sharedPage = SharedPage::create([
            'task_id' => $this->task->id,
            'html_content' => $html,
            'generated_at' => now(),
            'version' => 1,
        ]);
        // Salva task_share
        $share = TaskShare::create([
            'task_id' => $this->task->id,
            'token' => $token,
            'created_by' => Auth::id(),
            'is_active' => true,
        ]);
        // Aggiorna il task per la vista
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator'
        ])->findOrFail($this->taskId);
        session()->flash('public_share_link', URL::to('/share/' . $token));
        // Logging attività
        \App\Models\Activity::create([
            'task_id' => $this->task->id,
            'user_id' => Auth::id(),
            'description' => 'Ha generato un link pubblico di condivisione',
        ]);
    }

    public function deactivateShare($shareId)
    {
        $share = \App\Models\TaskShare::findOrFail($shareId);
        $share->is_active = false;
        $share->save();
        // Logging attività
        \App\Models\Activity::create([
            'task_id' => $this->task->id,
            'user_id' => Auth::id(),
            'description' => 'Ha disattivato un link pubblico di condivisione',
        ]);
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator'
        ])->findOrFail($this->taskId);
    }

    public function regenerateSharedHtml($shareId)
    {
        $share = \App\Models\TaskShare::findOrFail($shareId);
        $html = view('public.task-share', ['task' => $this->task])->render();
        \App\Models\SharedPage::create([
            'task_id' => $this->task->id,
            'html_content' => $html,
            'generated_at' => now(),
            'version' => 1 + \App\Models\SharedPage::where('task_id', $this->task->id)->max('version'),
        ]);
        // Logging attività
        \App\Models\Activity::create([
            'task_id' => $this->task->id,
            'user_id' => Auth::id(),
            'description' => 'Ha rigenerato la copia HTML condivisa',
        ]);
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator'
        ])->findOrFail($this->taskId);
    }

    public function changeStatus($newStatus)
    {
        $user = Auth::user();
        if (!app(\Illuminate\Contracts\Auth\Access\Gate::class)->forUser($user)->allows('canTransitionTo', [$this->task, $newStatus])) {
            session()->flash('error', 'Non hai i permessi per cambiare lo stato in ' . $newStatus);
            return;
        }
        $oldStatus = $this->task->status;
        $this->task->status = $newStatus;
        $this->task->save();
        // Logging attività
        \App\Models\Activity::create([
            'task_id' => $this->task->id,
            'user_id' => $user->id,
            'description' => "Stato cambiato da $oldStatus a $newStatus",
        ]);
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator'
        ])->findOrFail($this->taskId);
        // Notifica Slack/webhook
        if ($this->task->webhook_url) {
            try {
                Http::post($this->task->webhook_url, [
                    'text' => "[Task] {$this->task->title} - Stato cambiato da $oldStatus a $newStatus da {$user->name}" . ($this->task->slack_channel ? " (canale: {$this->task->slack_channel})" : ""),
                ]);
            } catch (\Exception $e) {
                // Log errori webhook
            }
        }
    }

    public function toggleTemplate()
    {
        $this->task->is_template = !$this->task->is_template;
        $this->task->save();
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator'
        ])->findOrFail($this->taskId);
    }

    public function shareInternal()
    {
        if (!$this->internalShareUserId && !$this->internalShareRoleId) return;
        InternalTaskShare::create([
            'task_id' => $this->task->id,
            'user_id' => $this->internalShareUserId ?: null,
            'role_id' => $this->internalShareRoleId ?: null,
            'created_by' => Auth::id(),
            'is_active' => true,
        ]);
        $this->internalShareUserId = '';
        $this->internalShareRoleId = '';
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator', 'internalShares.user', 'internalShares.role'
        ])->findOrFail($this->taskId);
    }

    public function revokeInternalShare($shareId)
    {
        $share = InternalTaskShare::findOrFail($shareId);
        $share->is_active = false;
        $share->save();
        $this->task = Task::with([
            'roles', 'assignedUsers', 'attachments', 'comments.user', 'tags', 'activities', 'sharedPages', 'shares', 'creator', 'internalShares.user', 'internalShares.role'
        ])->findOrFail($this->taskId);
    }

    public function saveExternalLinks()
    {
        $this->task->figma_url = $this->figma_url;
        $this->task->notion_url = $this->notion_url;
        $this->task->github_url = $this->github_url;
        $this->task->slack_channel = $this->slack_channel;
        $this->task->webhook_url = $this->webhook_url;
        $this->task->save();
        $this->task->refresh();
        session()->flash('success_external', 'Integrazioni aggiornate!');
    }

    public function render()
    {
        return view('livewire.task-detail', [
            'task' => $this->task,
        ]);
    }
}
