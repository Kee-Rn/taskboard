<?php

namespace App\Listeners;

use App\Events\TaskStatusUpdated;
use App\Notifications\TaskStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTaskStatusNotification implements ShouldQueue
{
    public function handle(TaskStatusUpdated $event): void
    {
        $task = $event->task->load('assignees', 'creator');
        $task->creator->notify(new TaskStatusNotification($task, $event->oldStatus));
    }
}