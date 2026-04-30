<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'in:todo,in_progress,review,done'],
            'priority'    => ['required', 'in:low,medium,high'],
            'due_date'    => ['nullable', 'date', 'after_or_equal:today'],
            'project_id'  => ['required', 'exists:projects,id'],
            'assignees'   => ['nullable', 'array'],
            'assignees.*' => ['exists:users,id'],
        ];
    }
}