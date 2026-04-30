<?php

namespace App\Services;

use App\Events\TaskAssigned;
use App\Events\TaskStatusUpdated;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function createTask(array $data, User $creator): Task
    {
        return DB::transaction(function () use ($data, $creator) {
            $task = Task::create([
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'status'      => $data['status'] ?? 'todo',
                'priority'    => $data['priority'] ?? 'medium',
                'due_date'    => $data['due_date'] ?? null,
                'project_id'  => $data['project_id'],
                'created_by'  => $creator->id,
            ]);

            if (!empty($data['assignees'])) {
                $task->assignees()->sync($data['assignees']);
                event(new TaskAssigned($task));
            }

            return $task;
        });
    }

    public function updateTask(Task $task, array $data): Task
    {
        $oldStatus = $task->status;

        $task->update(array_filter([
            'title'       => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? null,
            'priority'    => $data['priority'] ?? null,
            'due_date'    => $data['due_date'] ?? null,
        ], fn($v) => $v !== null));

        if (isset($data['assignees'])) {
            $task->assignees()->sync($data['assignees']);
        }

        if (isset($data['status']) && $oldStatus !== $data['status']) {
            event(new TaskStatusUpdated($task, $oldStatus));
        }

        return $task->fresh();
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }
}