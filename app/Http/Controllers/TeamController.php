<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreTeamRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{   use AuthorizesRequests;
    public function __construct(private TeamService $teamService) {}

    public function index(Request $request)
    {
        $teams = $request->user()->teams()->with('owner', 'members')->get();
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(StoreTeamRequest $request)
    {
        $team = $this->teamService->createTeam($request->validated(), $request->user());
        return redirect()->route('teams.show', $team)->with('success', 'Team created!');
    }

    public function show(Team $team)
    {
        $this->authorize('view', $team);
        $team->load('members', 'projects.tasks');
        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $this->authorize('update', $team);
        return view('teams.edit', compact('team'));
    }

    public function update(StoreTeamRequest $request, Team $team)
    {
        $this->authorize('update', $team);
        $team->update($request->validated());
        return redirect()->route('teams.show', $team)->with('success', 'Team updated!');
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted.');
    }

    public function addMember(Request $request, Team $team)
    {
        $this->authorize('manageMembers', $team);
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role'  => ['required', 'in:admin,member'],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $this->teamService->addMember($team, $user, $request->role);

        return back()->with('success', "{$user->name} added to team.");
    }

    public function removeMember(Team $team, User $user)
    {
        $this->authorize('manageMembers', $team);
        $this->teamService->removeMember($team, $user);
        return back()->with('success', 'Member removed.');
    }
}