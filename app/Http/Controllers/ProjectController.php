<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $projects = Project::whereHas('team.members', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->with('team', 'tasks')->get();

        return view('projects.index', compact('projects'));
    }

    public function create(Request $request)
    {
        $teams = $request->user()->teams;
        return view('projects.create', compact('teams'));
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('projects.show', $project)->with('success', 'Project created!');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load('tasks.assignees', 'team.members');

        $tasksByStatus = $project->tasks->groupBy('status');

        return view('projects.show', compact('project', 'tasksByStatus'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $teams = auth()->user()->teams;
        return view('projects.edit', compact('project', 'teams'));
    }

    public function update(StoreProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        $project->update($request->validated());
        return redirect()->route('projects.show', $project)->with('success', 'Project updated!');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}