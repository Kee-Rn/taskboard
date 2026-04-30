<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private TaskService $taskService) {}

    public function create(Request $request)
    {
        $project = Project::findOrFail($request->query('project_id'));
        $this->authorize('view', $project);
        $members = $project->team->members;
        return view('tasks.create', compact('project', 'members'));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask($request->validated(), $request->user());
        return redirect()->route('projects.show', $task->project_id)->with('success', 'Task created!');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load('assignees', 'comments.user', 'attachments', 'project.team');
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $members = $task->project->team->members;
        return view('tasks.edit', compact('task', 'members'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $this->taskService->updateTask($task, $request->validated());
        return redirect()->route('tasks.show', $task)->with('success', 'Task updated!');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $projectId = $task->project_id;
        $this->taskService->deleteTask($task);
        return redirect()->route('projects.show', $projectId)->with('success', 'Task deleted.');
    }
}