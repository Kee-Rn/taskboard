<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Task;

class CommentController extends Controller
{
    use AuthorizesRequests;
    public function store(StoreCommentRequest $request, Task $task)
    {
        $this->authorize('view', $task);

        $task->comments()->create([
            'body'    => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Comment added.');
    }

    public function destroy(Task $task, $comment)
    {
        $comment = $task->comments()->findOrFail($comment);

        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'Comment deleted.');
    }
}