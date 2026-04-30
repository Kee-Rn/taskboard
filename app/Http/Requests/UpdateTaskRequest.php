<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['sometimes', 'in:todo,in_progress,review,done'],
            'priority'    => ['sometimes', 'in:low,medium,high'],
            'due_date'    => ['nullable', 'date'],
            'assignees'   => ['nullable', 'array'],
            'assignees.*' => ['exists:users,id'],
        ];
    }
}