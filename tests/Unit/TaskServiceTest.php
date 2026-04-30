<?php

namespace Tests\Unit;

use App\Events\TaskAssigned;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskService();
    }

    public function test_can_create_a_task(): void
    {
        $user    = User::factory()->create();
        $project = $this->makeProject($user);

        $task = $this->service->createTask([
            'title'      => 'Test Task',
            'status'     => 'todo',
            'priority'   => 'medium',
            'project_id' => $project->id,
        ], $user);

        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
        $this->assertEquals($user->id, $task->created_by);
    }

    public function test_fires_task_assigned_event_when_assignees_provided(): void
    {
        Event::fake();

        $user    = User::factory()->create();
        $assignee = User::factory()->create();
        $project  = $this->makeProject($user, $assignee);

        $this->service->createTask([
            'title'      => 'Test Task',
            'status'     => 'todo',
            'priority'   => 'medium',
            'project_id' => $project->id,
            'assignees'  => [$assignee->id],
        ], $user);

        Event::assertDispatched(TaskAssigned::class);
    }

    public function test_can_update_task_status(): void
    {
        $user = User::factory()->create();
        $project = $this->makeProject($user);
        $task = Task::factory()->create(['project_id' => $project->id, 'status' => 'todo']);

        $updated = $this->service->updateTask($task, ['status' => 'in_progress']);

        $this->assertEquals('in_progress', $updated->status);
    }

    private function makeProject(User ...$members): Project
    {
        $owner = $members[0];
        $team  = Team::factory()->create(['owner_id' => $owner->id]);
        $team->members()->attach($owner->id, ['role' => 'admin']);
        foreach (array_slice($members, 1) as $m) {
            $team->members()->attach($m->id, ['role' => 'member']);
        }
        return Project::factory()->create(['team_id' => $team->id, 'created_by' => $owner->id]);
    }
}