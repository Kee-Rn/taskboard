<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'priority'    => $this->priority,
            'due_date'    => $this->due_date?->toDateString(),
            'is_overdue'  => $this->isOverdue(),
            'project'     => [
                'id'   => $this->project->id,
                'name' => $this->project->name,
            ],
            'assignees' => $this->whenLoaded('assignees', fn() =>
                $this->assignees->map(fn($u) => ['id' => $u->id, 'name' => $u->name])
            ),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}