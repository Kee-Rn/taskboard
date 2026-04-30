<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Projects</h2>
            <a href="{{ route('projects.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                + New Project
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($projects as $project)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold">{{ $project->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $project->team->name }}</p>
                    <p class="text-sm text-gray-400 mt-2">{{ $project->tasks->count() }} tasks</p>
                    <a href="{{ route('projects.show', $project) }}"
                       class="mt-4 block text-center px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                        Open
                    </a>
                </div>
                @empty
                <div class="col-span-3 text-center py-12 text-gray-400">
                    No projects yet. <a href="{{ route('projects.create') }}" class="text-indigo-600 hover:underline">Create one</a>.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>