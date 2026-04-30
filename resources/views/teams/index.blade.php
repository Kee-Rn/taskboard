<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Teams</h2>
            <a href="{{ route('teams.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                + New Team
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @forelse($teams as $team)
            <div class="bg-white shadow rounded-lg p-6 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold">
                            <a href="{{ route('teams.show', $team) }}" class="text-indigo-600 hover:underline">
                                {{ $team->name }}
                            </a>
                        </h3>
                        <p class="text-gray-500 text-sm mt-1">{{ $team->description }}</p>
                        <div class="mt-2 text-sm text-gray-500">
                            {{ $team->members->count() }} members ·
                            Owner: {{ $team->owner->name }}
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('teams.show', $team) }}"
                           class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
                            View
                        </a>
                        @if($team->owner_id === auth()->id())
                        <a href="{{ route('teams.edit', $team) }}"
                           class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
                            Edit
                        </a>
                        <form action="{{ route('teams.destroy', $team) }}" method="POST"
                              onsubmit="return confirm('Delete this team?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 text-sm border border-red-300 text-red-600 rounded hover:bg-red-50">
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white shadow rounded-lg p-12 text-center text-gray-400">
                <p class="text-lg">No teams yet.</p>
                <a href="{{ route('teams.create') }}" class="mt-4 inline-block text-indigo-600 hover:underline">
                    Create your first team
                </a>
            </div>
            @endforelse

        </div>
    </div>
</x-app-layout>