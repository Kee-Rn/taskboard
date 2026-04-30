<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;

class TeamService
{
    public function createTeam(array $data, User $owner): Team
    {
        $team = Team::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'owner_id'    => $owner->id,
        ]);

        // Owner automatically gets admin role
        $team->members()->attach($owner->id, ['role' => 'admin']);

        return $team;
    }

    public function addMember(Team $team, User $user, string $role = 'member'): void
    {
        if (!$team->hasMember($user)) {
            $team->members()->attach($user->id, ['role' => $role]);
        }
    }

    public function removeMember(Team $team, User $user): void
    {
        $team->members()->detach($user->id);
    }

    public function updateMemberRole(Team $team, User $user, string $role): void
    {
        $team->members()->updateExistingPivot($user->id, ['role' => $role]);
    }
}