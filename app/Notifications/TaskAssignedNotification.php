<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Task Assigned: {$this->task->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have been assigned to the task: **{$this->task->title}**")
            ->line("Priority: {$this->task->priority}")
            ->action('View Task', url("/tasks/{$this->task->id}"))
            ->line('Thank you for using Taskboard!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id'    => $this->task->id,
            'task_title' => $this->task->title,
            'type'       => 'task_assigned',
        ];
    }
}