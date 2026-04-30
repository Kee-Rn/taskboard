<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $project->name }}</h2>
            <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                + New Task
            </a>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $status => $label)
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-medium text-gray-700 mb-3">
                    {{ $label }}
                    <span class="ml-1 text-xs bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded-full">
                        {{ ($tasksByStatus[$status] ?? collect())->count() }}
                    </span>
                </h3>
                <div class="space-y-2">
                    @foreach($tasksByStatus[$status] ?? [] as $task)
                    <a href="{{ route('tasks.show', $task) }}"
                       class="block bg-white rounded border p-3 hover:border-indigo-400 transition">
                        <div class="text-sm font-medium text-gray-800">{{ $task->title }}</div>
                        <div class="mt-1 flex items-center gap-1">
                            <span class="text-xs px-1.5 py-0.5 rounded
                                {{ match($task->priority) {
                                    'high'   => 'bg-red-100 text-red-600',
                                    'medium' => 'bg-yellow-100 text-yellow-600',
                                    'low'    => 'bg-green-100 text-green-600',
                                } }}">
                                {{ $task->priority }}
                            </span>
                            @if($task->due_date)
                            <span class="text-xs {{ $task->isOverdue() ? 'text-red-500' : 'text-gray-400' }}">
                                {{ $task->due_date->format('M d') }}
                            </span>
                            @endif
                        </div>
                        @if($task->assignees->count())
                        <div class="mt-2 text-xs text-gray-400">
                            {{ $task->assignees->pluck('name')->join(', ') }}
                        </div>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>