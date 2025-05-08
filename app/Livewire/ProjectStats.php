<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use App\Models\Role;

class ProjectStats extends Component
{
    public function render()
    {
        $stats = [
            'total' => Task::count(),
            'by_status' => Task::select('status')->selectRaw('count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray(),
            'by_priority' => Task::select('priority')->selectRaw('count(*) as count')->groupBy('priority')->pluck('count', 'priority')->toArray(),
            'urgent' => Task::where('priority', 'urgent')->count(),
            'due_soon' => Task::whereNotNull('deadline')->where('deadline', '<=', now()->addDays(3))->count(),
        ];
        $roles = Role::all();
        $by_role = [];
        foreach ($roles as $role) {
            $by_role[$role->name] = $role->tasks()->count();
        }
        return view('livewire.project-stats', compact('stats', 'by_role'));
    }
}
