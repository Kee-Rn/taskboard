<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'priority', 'due_date', 'project_id', 'created_by'
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }

    public function scopeFilter($query, array $filters)
{
    $query->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v));
    $query->when($filters['priority'] ?? null, fn($q, $v) => $q->where('priority', $v));
    $query->when($filters['project_id'] ?? null, fn($q, $v) => $q->where('project_id', $v));
    return $query;
}
}