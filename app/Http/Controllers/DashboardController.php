<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Task;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $teams = $user->teams()->with('projects')->get();

        // Check if user is admin of any team
        $isAdmin = $teams->contains(fn($team) =>
            $user->isAdminOfTeam($team) || $team->owner_id === $user->id
        );

        Cache::forget("dashboard_stats_{$user->id}");

        if ($isAdmin) {
            // Admin sees overall stats across all their teams' projects
            $stats = Cache::remember("dashboard_stats_{$user->id}", 300, function () use ($user, $teams) {
                $teamIds    = $teams->pluck('id');
                $projectIds = Project::whereIn('team_id', $teamIds)->pluck('id');

                $tasks = Task::whereIn('project_id', $projectIds);

                return [
                    'total'        => (clone $tasks)->count(),
                    'todo'         => (clone $tasks)->where('status', 'todo')->count(),
                    'in_progress'  => (clone $tasks)->where('status', 'in_progress')->count(),
                    'review'       => (clone $tasks)->where('status', 'review')->count(),
                    'done'         => (clone $tasks)->where('status', 'done')->count(),
                    'overdue'      => (clone $tasks)
                                        ->where('due_date', '<', now())
                                        ->where('status', '!=', 'done')
                                        ->count(),
                    'projects'     => Project::whereIn('team_id', $teamIds)->count(),
                ];
            });

            $recentTasks = Task::whereIn('project_id',
                Project::whereIn('team_id', $teams->pluck('id'))->pluck('id')
            )
            ->with('project.team', 'assignees')
            ->latest()
            ->take(10)
            ->get();

        } else {
            // Regular member sees only their assigned tasks
            $stats = Cache::remember("dashboard_stats_{$user->id}", 300, function () use ($user) {
                $assigned = $user->assignedTasks();
                return [
                    'total'       => (clone $assigned)->count(),
                    'todo'        => (clone $assigned)->where('status', 'todo')->count(),
                    'in_progress' => (clone $assigned)->where('status', 'in_progress')->count(),
                    'review'      => (clone $assigned)->where('status', 'review')->count(),
                    'done'        => (clone $assigned)->where('status', 'done')->count(),
                    'overdue'     => (clone $assigned)
                                        ->where('due_date', '<', now())
                                        ->where('status', '!=', 'done')
                                        ->count(),
                    'projects'    => 0,
                ];
            });

            $recentTasks = $user->assignedTasks()
                                ->with('project.team', 'assignees')
                                ->latest()
                                ->take(10)
                                ->get();
        }

        return view('dashboard', compact('teams', 'stats', 'recentTasks', 'isAdmin'));
    }
}