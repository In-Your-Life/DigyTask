<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TaskCommented;
use App\Notifications\UserMentioned;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Activity;
use Illuminate\Support\Facades\Http;

class Comments extends Component
{
    public $taskId;
    public $content = '';

    protected $rules = [
        'content' => 'required|string|min:1',
    ];

    public function index(Request $request)
    {
        // se ti serve filtrare per task:
        $taskId = $request->query('task_id');
        $comments = Comment::with('user')
            ->when($taskId, fn($q) => $q->where('task_id', $taskId))
            ->orderBy('created_at')
            ->get();

        return view('comments.index', compact('comments'));
    }


    public function addComment()
    {
        $this->validate();
        $comment = Comment::create([
            'task_id' => $this->taskId,
            'user_id' => Auth::id(),
            'content' => $this->content,
            'type' => 'comment',
        ]);
        // Notifica agli assegnatari (escluso autore)
        $task = $comment->task()->with('assignedUsers')->first();
        $author = Auth::user();
        foreach ($task->assignedUsers as $user) {
            if ($user->id !== $author->id) {
                $user->notify(new TaskCommented($task, $comment, $author));
            }
        }
        // Notifica agli utenti menzionati
        preg_match_all('/@([\w.]+)/', $this->content, $matches);
        $mentionedUsernames = $matches[1] ?? [];
        if (!empty($mentionedUsernames)) {
            $mentionedUsers = User::whereIn('name', $mentionedUsernames)->get();
            foreach ($mentionedUsers as $mentioned) {
                if ($mentioned->id !== $author->id) {
                    $mentioned->notify(new UserMentioned($task, $comment, $author));
                }
            }
        }
        $this->content = '';
        $this->dispatch('commentAdded');
        // Logging attività
        Activity::create([
            'task_id' => $comment->task_id,
            'user_id' => $author->id,
            'description' => 'Ha aggiunto un commento',
        ]);
    }

    public function render()
    {
        $comments = Comment::with('user')->where('task_id', $this->taskId)->orderBy('created_at')->get();
        return view('livewire.comments', compact('comments'));
    }
}
