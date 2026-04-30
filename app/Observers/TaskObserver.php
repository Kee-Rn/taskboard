<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskObserver
{
    public function created(Task $task): void
    {
        // Clear dashboard cache when a task is created
        Cache::forget("dashboard_stats_{$task->project->team_id}");
    }

    public function updated(Task $task): void
    {
        Cache::forget("dashboard_stats_{$task->project->team_id}");
    }

    public function deleted(Task $task): void
    {
        Cache::forget("dashboard_stats_{$task->project->team_id}");
    }
}