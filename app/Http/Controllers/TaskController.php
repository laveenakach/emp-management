<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\TaskAssignedNotification;

class TaskController extends Controller
{
    public function index()
    {
        // $tasks = Task::join('users', 'tasks.assigned_to', '=', 'users.id')
        //     ->select(
        //         'tasks.*',
        //         'users.name as employee_name',
        //         'users.email as employee_email',
        //         'users.empuniq_id'
        //     )
        //     ->orderByDesc('tasks.id')
        //     ->where('created_by', Auth::id())
        //     ->orWhere('assigned_to', Auth::id())
        //     ->latest()->get();

        $user = Auth::user();

        if ($user->role === 'employee') {
            $tasks = Task::whereJsonContains('assigned_to', (string) $user->id)
                ->orderByDesc('id')
                ->get();
        } else {
            $tasks = Task::where('created_by', $user->id)
            ->latest()
            ->get();

        }

        // echo "<pre>";
        // print_r($tasks);
        // die;

        return view('employer.tasks.index', compact('tasks'));
    }

    public function submitedtask()
    {
        $user = Auth::user();

        if ($user->role === 'employee') {

            // Employee sees only tasks assigned to them AND submitted
            $tasks = Task::with('users')
                ->where('status', 'Submitted')
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->orderByDesc('id')
                ->get();

        } else {

            // Employer sees:
            // 1. Tasks they created
            // 2. Tasks assigned to them
            // AND status = Submitted
            $tasks = Task::with('users')
                ->where('status', 'Submitted')
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                    ->orWhereHas('users', function ($q2) use ($user) {
                        $q2->where('users.id', $user->id);
                    });
                })
                ->orderByDesc('id')
                ->get();
        }

        return view('employer.tasks.Submitedtask', compact('tasks'));
    }

    public function create()
    {
        $users = User::all();

        $employees = User::whereNotIn('users.role', ['employer'])->get();

        return view('employer.tasks.create', compact('users', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Not Started,In Progress,Completed,Blocked',
            'assigned_to' => 'required|array',
            'assigned_to.*' => 'exists:users,id',
            'role' => 'required|in:Owner,Reviewer,Collaborator',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'attachments.*' => 'file|max:2048'
        ]);

        $path = null;

        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tasks'), $fileName);
            $path = 'uploads/tasks/' . $fileName;
        }

        // ✅ Create Task (NO assigned_to column here)
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'created_by' => Auth::id(),
            'assigned_by' => Auth::id(),
            'role' => $request->role,
            'file_path' => $path,
        ]);

        // ✅ Attach multiple students
        $task->users()->attach($request->assigned_to);

        // ✅ Send notification to each student
        $students = User::whereIn('id', $request->assigned_to)->get();

        foreach ($students as $student) {
            $student->notify(new TaskAssignedNotification($task));
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task assigned to students successfully.');
    }

    // Show the form to edit an existing employee
    public function edit($id)
    {
        // $task = Task::join('users', 'tasks.assigned_to', '=', 'users.id')
        //     ->select(
        //         'tasks.*',
        //         'users.name as employee_name',
        //         'users.email as employee_email',
        //         'users.empuniq_id'
        //     )
        //     ->orderByDesc('tasks.id')
        //     //->paginate(10) // ✅ Add pagination
        //     ->where('created_by', Auth::id())
        //     ->Where('tasks.id', $id)
        //     ->first();

        $task = Task::where('created_by', Auth::id())
            // ->orWhereJsonContains('assigned_to', Auth::id())
            ->with('users')   // 👈 IMPORTANT
            ->findOrFail($id);

        // echo"<pre>";
        // print_r($task);
        // die;

        //$employees = User::where('role', 'employee')->get();

        $employees = User::whereNotIn('users.role', ['employer'])->get();


        return view('employer.tasks.create', compact('task', 'employees'));
    }

    // Update an existing employee
    public function update(Request $request, $id)
    {

        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Not Started,In Progress,Completed,Blocked',
            'assigned_to' => 'required|exists:users,id',
            'role' => 'required|in:Owner,Reviewer,Collaborator',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'attachments.*' => 'file|max:2048'
        ]);

        $path = null;

        if ($request->hasFile('attachments')) {
            $pdf = $request->file('attachments');
            // Generate a unique filename
            $pdfName = time() . '_' . uniqid() . '.' . $pdf->getClientOriginalExtension();
            // Move the file to the public/uploads/tasks directory
            $pdf->move(public_path('uploads/tasks'), $pdfName);
            // Store the relative file path
            $path = 'uploads/tasks/' . $pdfName;
        }

        // print_r($request->all());
        // die;
        $Task = Task::findOrFail($id);
        $Task->title = $request->title;
        $Task->description = $request->description;
        $Task->priority = $request->priority;
        $Task->status = $request->status;

        $Task->start_date = Carbon::parse($request->start_date);
        $Task->due_date = Carbon::parse($request->due_date);
        $Task->created_by = Auth::id();
        $Task->assigned_by = Auth::id();

        $Task->assigned_to = $request->assigned_to;
        $Task->role = $request->role;
        // $Task->assigned_to = json_encode($request->assigned_to);

        // Only set file_path if a file was uploaded
        if ($path) {
            $Task->file_path = $path;
        }
        $Task->save();

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        return view('employer.tasks.show', compact('task'));
    }

    public function softDelete($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();  // This will soft delete the task (requires SoftDeletes in model)

        return redirect()->route('tasks.index')->with('status', 'Task soft-deleted successfully.');
    }

    // Show trashed tasks
    public function trashed()
    {
        // $trashedTasks = Task::join('users', 'tasks.assigned_to', '=', 'users.id')
        //     ->select(
        //         'tasks.*',
        //         'users.name as employee_name',
        //     )
        //     ->orderByDesc('tasks.id')
        //     ->where('created_by', Auth::id())
        //     ->onlyTrashed()->get();


        $user = Auth::user();

        if ($user->role === 'employee') {
            $trashedTasks = Task::onlyTrashed()
                ->whereJsonContains('assigned_to', (string) $user->id)
                ->orderByDesc('id')
                ->get();
        } else {
            $trashedTasks = Task::onlyTrashed()
                ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                        ->orWhereJsonContains('assigned_to', (string) $user->id);
                })
                ->orderByDesc('id')
                ->get();
        }





        return view('employer.Tasks.trashed', compact('trashedTasks'));
    }

    // Restore
    public function restore($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $task->restore();
        return redirect()->route('tasks.trashed')->with('status', 'Task restored successfully.');
    }

    // Force delete
    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $task->forceDelete();
        return redirect()->route('tasks.trashed')->with('status', 'Task permanently deleted.');
    }


    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    // Show reschedule form
    public function showRescheduleForm(Task $task)
    {
        return view('employer.tasks.reschedule', compact('task'));
    }

    // Process reschedule
    public function reschedule(Request $request, Task $task)
    {
        $request->validate([
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
        ]);

        $task->start_date = $request->start_date;
        $task->due_date = $request->due_date;
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task rescheduled successfully.');
    }

    public function submit(Request $request)
    {

        // print_r($request->all());
        // die;

        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'progress' => 'required|string',
            'submission_file' => 'nullable|file|max:2048',
        ]);

        $task = Task::findOrFail($request->task_id);
        $task->progress = $request->progress;
        $task->status = $request->status;
        $task->submitted_by = auth()->id();
        $task->submitted_at = now();

        $path = null;

        if ($request->hasFile('submission_file')) {
            $pdf = $request->file('submission_file');
            // Generate a unique filename
            $pdfName = time() . '_' . uniqid() . '.' . $pdf->getClientOriginalExtension();
            // Move the file to the public/uploads/tasks directory
            $pdf->move(public_path('uploads/taskssubmition'), $pdfName);
            // Store the relative file path
            $path = 'uploads/taskssubmition/' . $pdfName;
        }

        $task->submission_file = $path;
        $task->save();

        return back()->with('success', 'Task submitted successfully.');
    }
}
