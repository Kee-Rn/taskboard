<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function teams()
    {
        return $this->belongsToMany(Team::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Helper
    public function isAdminOfTeam(Team $team): bool
    {
        return $this->teams()
                    ->wherePivot('team_id', $team->id)
                    ->wherePivot('role', 'admin')
                    ->exists();
    }
}