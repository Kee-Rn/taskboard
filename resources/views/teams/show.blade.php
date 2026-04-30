<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $team->name }}</h2>
            @can('update', $team)
            <a href="{{ route('teams.edit', $team) }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                Edit Team
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
            <div class="px-4 py-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        {{-- Members --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium mb-4">Members ({{ $team->members->count() }})</h3>
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 border-b">
                    <tr>
                        <th class="pb-2">Name</th>
                        <th class="pb-2">Email</th>
                        <th class="pb-2">Role</th>
                        @can('manageMembers', $team)<th class="pb-2">Action</th>@endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($team->members as $member)
                    <tr class="border-b">
                        <td class="py-2">{{ $member->name }}</td>
                        <td class="py-2 text-gray-500">{{ $member->email }}</td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 rounded text-xs
                                {{ $member->pivot->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $member->pivot->role }}
                            </span>
                        </td>
                        @can('manageMembers', $team)
                        <td class="py-2">
                            @if($member->id !== $team->owner_id)
                            <form action="{{ route('teams.members.remove', [$team, $member]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:text-red-700">Remove</button>
                            </form>
                            @endif
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @can('manageMembers', $team)
            <form action="{{ route('teams.members.add', $team) }}" method="POST" class="mt-4 flex gap-2">
                @csrf
                <input type="email" name="email" placeholder="Email address"
                       class="flex-1 border rounded px-3 py-1.5 text-sm">
                <select name="role" class="border rounded px-3 py-1.5 text-sm">
                    <option value="member">Member</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit"
                        class="px-4 py-1.5 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                    Add Member
                </button>
            </form>
            @endcan
        </div>

        {{-- Projects --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between mb-4">
                <h3 class="text-lg font-medium">Projects</h3>
                <a href="{{ route('projects.create') }}?team_id={{ $team->id }}"
                   class="text-sm text-indigo-600 hover:underline">+ New Project</a>
            </div>
            <div class="space-y-2">
                @forelse($team->projects as $project)
                <a href="{{ route('projects.show', $project) }}"
                   class="block border rounded-lg p-4 hover:border-indigo-400 transition">
                    <div class="font-medium">{{ $project->name }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ $project->tasks->count() }} tasks</div>
                </a>
                @empty
                <p class="text-gray-400 text-sm">No projects yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>