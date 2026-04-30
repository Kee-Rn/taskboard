<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip'],
        ]);

        $file = $request->file('file');
        $path = $file->store("attachments/tasks/{$task->id}", 'local');

        $task->attachments()->create([
            'filename'  => $file->getClientOriginalName(),
            'path'      => $path,
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
            'user_id'   => $request->user()->id,
        ]);

        return back()->with('success', 'File uploaded.');
    }

    public function destroy(Task $task, Attachment $attachment)
    {
        $this->authorize('update', $task);
        Storage::disk('local')->delete($attachment->path);
        $attachment->delete();
        return back()->with('success', 'Attachment removed.');
    }

    public function download(Attachment $attachment)
    {
        return Storage::disk('local')->download($attachment->path, $attachment->filename);
    }
}