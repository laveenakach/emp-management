<?php

namespace App\Http\Controllers;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    
    public function store(Request $request, Task $task)
    {
        // print_r($request->all());

        // echo"<pre>";
        // print_r($task->id);
        // die;

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Comment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'Comment added successfully.');
    }
}
