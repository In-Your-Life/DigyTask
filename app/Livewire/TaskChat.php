<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatMessage;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskChat extends Component
{
    public $taskId;
    public $message = '';
    public $messages = [];

    protected $rules = [
        'message' => 'required|string|max:1000',
    ];

    public function mount($taskId)
    {
        $this->taskId = $taskId;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = ChatMessage::where('task_id', $this->taskId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        $this->validate();
        $user = Auth::user();
        ChatMessage::create([
            'task_id' => $this->taskId,
            'user_id' => $user->id,
            'message' => $this->message,
        ]);
        $this->message = '';
        $this->loadMessages();
        $this->dispatch('chat-message-sent');
    }

    public function render()
    {
        return view('livewire.task-chat');
    }

    public function getListeners()
    {
        return [
            'refreshChat' => 'loadMessages',
            'echo:task-chat.' . $this->taskId . ',ChatMessageSent' => 'loadMessages',
        ];
    }
}
