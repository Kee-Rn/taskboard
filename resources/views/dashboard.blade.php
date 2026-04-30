<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats Cards --}}
            @if($isAdmin)
            {{-- Admin: overall project stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow text-center col-span-2 md:col-span-1">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['projects'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Projects</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow text-center">
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Tasks</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow text-center">
                    <div class="text-3xl font-bold text-blue-500">{{ $stats['in_progress'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">In Progress</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow text-center border-l-4 border-red-500">
                    <div class="text-3xl font-bold text-red-500">{{ $stats['overdue'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Overdue</div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4 shadow text-center">
                    <div class="text-3xl font-bold text-gray-500">{{ $stats['todo'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">To Do</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow text-center">
                    <div class="text-3xl font-bold text-yellow-500">{{ $stats['review'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">In Review</div>
                </div>
                <div class="bg-white rounded-lg p-4 shadow text-center">
                    <div class="text-3xl font-bold text-green-500">{{ $stats['done'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Done</div>
                </div>
            </div>

            @else
            {{-- Member: personal task stats --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach(['total' => 'All', 'todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done', 'overdue' => 'Overdue'] as $key => $label)
                <div class="bg-white rounded-lg p-4 shadow text-center {{ $key === 'overdue' ? 'border-l-4 border-red-500' : '' }}">
                    <div class="text-3xl font-bold {{ $key === 'overdue' ? 'text-red-500' : 'text-indigo-600' }}">
                        {{ $stats[$key] }}
                    </div>
                    <div class="text-sm text-gray-500">{{ $label }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Recent Tasks --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">
                    {{ $isAdmin ? 'All Recent Tasks' : 'My Recent Tasks' }}
                </h3>
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-2">Task</th>
                            <th class="pb-2">Project</th>
                            @if($isAdmin)
                            <th class="pb-2">Assigned To</th>
                            @endif
                            <th class="pb-2">Status</th>
                            <th class="pb-2">Priority</th>
                            <th class="pb-2">Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $task)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2">
                                <a href="{{ route('tasks.show', $task) }}" class="text-indigo-600 hover:underline">
                                    {{ $task->title }}
                                </a>
                            </td>
                            <td class="py-2 text-gray-600">{{ $task->project->name }}</td>
                            @if($isAdmin)
                            <td class="py-2 text-gray-500 text-xs">
                                {{ $task->assignees->pluck('name')->join(', ') ?: '—' }}
                            </td>
                            @endif
                            <td class="py-2">
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    {{ match($task->status) {
                                        'todo'        => 'bg-gray-100 text-gray-700',
                                        'in_progress' => 'bg-blue-100 text-blue-700',
                                        'review'      => 'bg-yellow-100 text-yellow-700',
                                        'done'        => 'bg-green-100 text-green-700',
                                    } }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>
                            <td class="py-2">
                                <span class="px-2 py-0.5 rounded text-xs
                                    {{ match($task->priority) {
                                        'high'   => 'bg-red-100 text-red-700',
                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                        'low'    => 'bg-green-100 text-green-700',
                                    } }}">
                                    {{ $task->priority }}
                                </span>
                            </td>
                            <td class="py-2 {{ $task->isOverdue() ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                {{ $task->due_date?->format('M d, Y') ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-4 text-center text-gray-400">No tasks yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Teams --}}
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-medium">My Teams</h3>
                    <a href="{{ route('teams.create') }}" class="text-sm text-indigo-600 hover:underline">+ New Team</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($teams as $team)
                    <a href="{{ route('teams.show', $team) }}"
                       class="block border rounded-lg p-4 hover:border-indigo-400 transition">
                        <div class="font-medium">{{ $team->name }}</div>
                        <div class="text-sm text-gray-500 mt-1">
                            {{ $team->members->count() }} members ·
                            {{ $team->projects->count() }} projects
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>