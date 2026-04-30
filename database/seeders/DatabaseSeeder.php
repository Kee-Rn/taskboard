<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $teamService = app(TeamService::class);

        // Create demo user
        $demo = User::factory()->create([
            'name'  => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        // Create extra users
        $users = User::factory(5)->create();
        $allUsers = $users->push($demo);

        // Create a team
        $team = $teamService->createTeam([
            'name'        => 'Engineering Team',
            'description' => 'Our main dev team',
        ], $demo);

        // Add others as members
        foreach ($users as $user) {
            $teamService->addMember($team, $user, 'member');
        }

        // Create projects
        $projects = Project::factory(3)->create([
            'team_id'    => $team->id,
            'created_by' => $demo->id,
        ]);

        // Create tasks for each project
        foreach ($projects as $project) {
            Task::factory(8)->create([
                'project_id' => $project->id,
                'created_by' => $demo->id,
            ])->each(function ($task) use ($allUsers) {
                $task->assignees()->attach(
                    $allUsers->random(rand(1, 3))->pluck('id')->toArray()
                );
            });
        }
    }
}