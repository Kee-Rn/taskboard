<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTaskAssignedNotification implements ShouldQueue
{
    public function handle(TaskAssigned $event): void
    {
        $task = $event->task->load('assignees');

        foreach ($task->assignees as $user) {
            $user->notify(new TaskAssignedNotification($task));
        }
    }
}