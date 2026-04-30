<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $project->team->hasMember($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            || $user->isAdminOfTeam($project->team);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->created_by === $user->id
            || $project->team->owner_id === $user->id;
    }
}