<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Task</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" value="{{ old('title', $task->title) }}"
                               class="w-full border rounded-lg px-3 py-2" required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full border rounded-lg px-3 py-2">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full border rounded-lg px-3 py-2">
                                @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $val => $label)
                                <option value="{{ $val }}" {{ $task->status === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select name="priority" class="w-full border rounded-lg px-3 py-2">
                                @foreach(['low', 'medium', 'high'] as $p)
                                <option value="{{ $p }}" {{ $task->priority === $p ? 'selected' : '' }}>
                                    {{ ucfirst($p) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="due_date"
                               value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                               class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                        <select name="assignees[]" multiple class="w-full border rounded-lg px-3 py-2 h-28">
                            @foreach($members as $member)
                            <option value="{{ $member->id }}"
                                {{ $task->assignees->contains($member->id) ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Save Changes
                        </button>
                        <a href="{{ route('tasks.show', $task) }}"
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>