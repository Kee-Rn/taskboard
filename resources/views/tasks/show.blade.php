<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $task->title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('tasks.edit', $task) }}"
                   class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                    Edit
                </a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                      onsubmit="return confirm('Delete this task?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Task Details --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-3 gap-4 text-sm mb-4">
                <div>
                    <span class="text-gray-500">Status</span>
                    <div class="font-medium mt-1">{{ str_replace('_',' ',$task->status) }}</div>
                </div>
                <div>
                    <span class="text-gray-500">Priority</span>
                    <div class="font-medium mt-1">{{ $task->priority }}</div>
                </div>
                <div>
                    <span class="text-gray-500">Due Date</span>
                    <div class="font-medium mt-1 {{ $task->isOverdue() ? 'text-red-500' : '' }}">
                        {{ $task->due_date?->format('M d, Y') ?? '—' }}
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-gray-500 text-sm">Description</span>
                <p class="mt-1 text-gray-700">{{ $task->description ?? 'No description.' }}</p>
            </div>
            <div class="mt-4">
                <span class="text-gray-500 text-sm">Assigned to</span>
                <div class="flex gap-2 mt-1">
                    @foreach($task->assignees as $assignee)
                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-sm">
                        {{ $assignee->name }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- File Attachments --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-medium mb-3">Attachments</h3>
            @foreach($task->attachments as $file)
            <div class="flex items-center justify-between py-2 border-b text-sm">
                <a href="{{ route('attachments.download', $file) }}" class="text-indigo-600 hover:underline">
                    {{ $file->filename }}
                </a>
                <form action="{{ route('tasks.attachments.destroy', [$task, $file]) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-red-500 text-xs">Remove</button>
                </form>
            </div>
            @endforeach
            <form action="{{ route('tasks.attachments.store', $task) }}" method="POST"
                  enctype="multipart/form-data" class="mt-3">
                @csrf
                <div class="flex gap-2">
                    <input type="file" name="file" class="text-sm border rounded px-2 py-1 flex-1">
                    <button type="submit" class="px-3 py-1 bg-gray-700 text-white text-sm rounded">
                        Upload
                    </button>
                </div>
                @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </form>
        </div>

        {{-- Comments --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-medium mb-4">Comments ({{ $task->comments->count() }})</h3>
            @foreach($task->comments as $comment)
            <div class="mb-4 pb-4 border-b">
                <div class="flex justify-between text-sm text-gray-500 mb-1">
                    <span class="font-medium text-gray-700">{{ $comment->user->name }}</span>
                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-gray-700">{{ $comment->body }}</p>
                @if($comment->user_id === auth()->id())
                <form action="{{ route('tasks.comments.destroy', [$task, $comment]) }}" method="POST" class="mt-1">
                    @csrf @method('DELETE')
                    <button class="text-xs text-red-400 hover:text-red-600">Delete</button>
                </form>
                @endif
            </div>
            @endforeach

            <form action="{{ route('tasks.comments.store', $task) }}" method="POST" class="mt-4">
                @csrf
                <textarea name="body" rows="3" placeholder="Add a comment..."
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required></textarea>
                @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                    Post Comment
                </button>
            </form>
        </div>

    </div>
</x-app-layout>