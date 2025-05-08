<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusChanged extends Notification
{
    use Queueable;

    protected $task;
    protected $oldStatus;
    protected $newStatus;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $oldStatus, $newStatus, $user)
    {
        $this->task = $task;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->user = $user;
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
            'title' => 'Stato task aggiornato',
            'body' => $this->user->name . ' ha cambiato lo stato da ' . $this->oldStatus . ' a ' . $this->newStatus . ' per il task: ' . $this->task->title,
            'url' => route('tasks.show', $this->task->id),
            'icon' => 'fa-exchange-alt',
        ];
    }
}
