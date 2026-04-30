<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_task(): void
    {
        [$user, $task] = $this->makeUserAndTask();

        $response = $this->actingAs($user)->get(route('tasks.show', $task));

        $response->assertOk();
        $response->assertSee($task->title);
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $task = Task::factory()->create();
        $this->get(route('tasks.show', $task))->assertRedirect(route('login'));
    }

    public function test_user_can_create_task(): void
    {
        [$user, , $project] = $this->makeUserAndTask(createTask: false);

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title'      => 'New Feature',
            'status'     => 'todo',
            'priority'   => 'high',
            'project_id' => $project->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'New Feature']);
    }

    private function makeUserAndTask(bool $createTask = true): array
    {
        $user    = User::factory()->create();
        $team    = Team::factory()->create(['owner_id' => $user->id]);
        $team->members()->attach($user->id, ['role' => 'admin']);
        $project = Project::factory()->create(['team_id' => $team->id, 'created_by' => $user->id]);

        if (!$createTask) {
            return [$user, null, $project];
        }

        $task = Task::factory()->create(['project_id' => $project->id, 'created_by' => $user->id]);
        return [$user, $task, $project];
    }
}