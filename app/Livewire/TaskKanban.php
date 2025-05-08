<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Notifications\TaskStatusChanged;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;

class TaskKanban extends Component
{
    public $statuses = [
        'draft' => 'Bozza',
        'pending' => 'In Attesa',
        'in_progress' => 'In Lavorazione',
        'review' => 'In Revisione',
        'completed' => 'Completato',
    ];

    public function moveTask($taskId, $newStatus)
    {
        $task = Task::findOrFail($taskId);
        $oldStatus = $task->status;
        $task->status = $newStatus;
        $task->save();
        // Notifica agli assegnatari
        $user = Auth::user();
        foreach ($task->assignedUsers as $assignee) {
            if ($assignee->id !== $user->id) {
                $assignee->notify(new TaskStatusChanged($task, $oldStatus, $newStatus, $user));
            }
        }
        // Logging attività
        Activity::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'description' => "Stato cambiato da $oldStatus a $newStatus",
        ]);
        $this->dispatch('taskMoved');
    }

    public function render()
    {
        $tasksByStatus = [];
        foreach (array_keys($this->statuses) as $status) {
            $tasksByStatus[$status] = Task::where('status', $status)->with(['roles', 'assignedUsers'])->orderBy('priority', 'desc')->get();
        }
        return view('livewire.task-kanban', [
            'tasksByStatus' => $tasksByStatus,
            'statuses' => $this->statuses,
        ]);
    }
}
