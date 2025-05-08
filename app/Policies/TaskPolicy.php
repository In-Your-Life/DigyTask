<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->roles()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // PM, Capo Sviluppo, assegnatari, reparti coinvolti, creatore
        return $user->roles()->whereIn('name', ['Project Manager', 'Capo Sviluppo'])->exists()
            || $task->assignedUsers->contains($user)
            || $task->roles->intersect($user->roles)->isNotEmpty()
            || $task->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // PM, Capo Sviluppo, Programmatore, Front-End
        return $user->roles()->whereIn('name', ['Project Manager', 'Capo Sviluppo', 'Programmatore', 'Front-End'])->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // PM, Capo Sviluppo, assegnatari, creatore
        return $user->roles()->whereIn('name', ['Project Manager', 'Capo Sviluppo'])->exists()
            || $task->assignedUsers->contains($user)
            || $task->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Solo PM e Capo Sviluppo
        return $user->roles()->whereIn('name', ['Project Manager', 'Capo Sviluppo'])->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }

    public function changeStatus(User $user, Task $task): bool
    {
        // PM, Capo Sviluppo, assegnatari
        return $user->roles()->whereIn('name', ['Project Manager', 'Capo Sviluppo'])->exists()
            || $task->assignedUsers->contains($user);
    }

    /**
     * Vincoli avanzati di transizione stato task.
     */
    public function canTransitionTo(User $user, Task $task, string $newStatus): bool
    {
        $current = $task->status;
        $isPM = $user->roles()->whereIn('name', ['Project Manager', 'Capo Sviluppo'])->exists();
        $isAssignee = $task->assignedUsers->contains($user);
        // Da Bozza a In Attesa: solo PM/Capo Sviluppo
        if ($current === 'draft' && $newStatus === 'pending') {
            return $isPM;
        }
        // Da In Attesa a In Lavorazione: solo assegnatario
        if ($current === 'pending' && $newStatus === 'in_progress') {
            return $isAssignee;
        }
        // Da In Lavorazione a In Revisione: solo assegnatario
        if ($current === 'in_progress' && $newStatus === 'review') {
            return $isAssignee;
        }
        // Da In Revisione a Completato: solo PM/Capo Sviluppo
        if ($current === 'review' && $newStatus === 'completed') {
            return $isPM;
        }
        // Da qualsiasi stato a Bozza: solo PM/Capo Sviluppo
        if ($newStatus === 'draft') {
            return $isPM;
        }
        // Da Completato a In Lavorazione (riapertura): solo PM/Capo Sviluppo
        if ($current === 'completed' && $newStatus === 'in_progress') {
            return $isPM;
        }
        // Default: solo PM/Capo Sviluppo
        return $isPM;
    }
}
