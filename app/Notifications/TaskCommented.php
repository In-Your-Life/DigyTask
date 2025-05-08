<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TaskCommented extends Notification
{
    use Queueable;

    protected $task;
    protected $comment;
    protected $author;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $comment, $author)
    {
        $this->task = $task;
        $this->comment = $comment;
        $this->author = $author;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nuovo commento su un task',
            'body' => $this->author->name . ' ha commentato: "' . Str::limit($this->comment->body, 50) . '"',
            'url' => route('tasks.show', $this->task->id) . '#comments',
            'icon' => 'fa-comments',
        ];
    }
}
