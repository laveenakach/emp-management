<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskSubmittedNotification;

class TaskController extends Controller
{
    private function getTimeSlots()
    {
        $slots = [];

        for ($h = 1; $h <= 12; $h++) {
            foreach (['00','15','30','45'] as $m) {
                $slots[] = sprintf('%02d:%s AM', $h, $m);
                $slots[] = sprintf('%02d:%s PM', $h, $m);
            }
        }

        return $slots;
    }

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
            $tasks = Task::whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
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

            // Employee: see ONLY their own APPROVED tasks
            $tasks = Task::with('users')
                ->where('status', 'Approved')
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->orderByDesc('id')
                ->get();

        } else {

            // Employer: see APPROVED tasks created by them
            $tasks = Task::with('users')
                ->where('status', 'Approved')
                ->where('created_by', $user->id)
                ->orderByDesc('id')
                ->get();
        }

        return view('employer.tasks.Submitedtask', compact('tasks'));
    }

    public function create()
    {
        $employees = User::whereNotIn('role', ['employer'])->get();
        $task = null;
        $timeSlots = $this->getTimeSlots();

        return view('employer.tasks.create', compact('employees', 'task', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Not Started,Submitted,Approved,Rejected',
            'assigned_to' => 'required|array',
            'assigned_to.*' => 'exists:users,id',
            'role' => 'required|in:Owner,Reviewer,Collaborator',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'due_time' => 'nullable',
            'attachments' => 'nullable|file|max:2048',
        ]);

        // File upload
        $path = null;
        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/tasks'), $fileName);
            $path = 'uploads/tasks/' . $fileName;
        }

        // Datetime handling
        $startAt = $request->start_date && $request->start_time
            ? Carbon::createFromFormat('Y-m-d H:i', $request->start_date.' '.$request->start_time)
            : null;

        $dueAt = $request->due_date && $request->due_time
            ? Carbon::createFromFormat('Y-m-d H:i', $request->due_date.' '.$request->due_time)
            : null;

        // Create task
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $startAt,
            'due_date' => $dueAt,
            'created_by' => Auth::id(),
            'assigned_by' => Auth::id(),
            'role' => $request->role,
            'file_path' => $path,
        ]);

        // Attach users with assigned_at
        $attachData = [];
        foreach ($request->assigned_to as $userId) {
            //$attachData[$userId] = ['start_date' => now()];
            $attachData[$userId] = [
                'start_date' => $startAt ?? now()
            ];
        }

        $task->users()->attach($attachData);

        // Send notifications
        $users = User::whereIn('id', $request->assigned_to)->get();
        foreach ($users as $user) {
            $user->notify(new TaskAssignedNotification($task));
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task assigned successfully.');
    }

    // Show the form to edit an existing employee
    public function edit($id)
    {
        $task = Task::where('created_by', Auth::id())
            ->with('users')   // 👈 IMPORTANT
            ->findOrFail($id);

        $employees = User::whereNotIn('users.role', ['employer'])->get();

        $timeSlots = $this->getTimeSlots();

        return view('employer.tasks.create', compact('task', 'employees','timeSlots'));
    }

    // Update an existing employee
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Not Started,Submitted,Approved,Rejected',
            'assigned_to' => 'required|array',
            'assigned_to.*' => 'exists:users,id',
            'role' => 'required|in:Owner,Reviewer,Collaborator',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'due_time' => 'nullable',
        ]);

        $startAt = $request->start_date && $request->start_time
            ? Carbon::createFromFormat('Y-m-d H:i', $request->start_date.' '.$request->start_time)
            : null;

        $dueAt = $request->due_date && $request->due_time
            ? Carbon::createFromFormat('Y-m-d H:i', $request->due_date.' '.$request->due_time)
            : null;

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $startAt,
            'due_date' => $dueAt,
            'role' => $request->role,
        ]);

        // Sync users
        $syncData = [];
        foreach ($request->assigned_to as $userId) {
            $syncData[$userId] = ['start_date' => now()];
        }

        $task->users()->sync($syncData);

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
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
        $user = Auth::user();

        if ($user->role === 'employee') {
            $tasks = Task::onlyTrashed()
                ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
                ->get();
        } else {
            $tasks = Task::onlyTrashed()
                ->where('created_by', $user->id)
                ->get();
        }

        return view('tasks.trashed', compact('tasks'));
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
        $task->status = 'Not Started';

        // Notification::create([
        //     'user_id' => $task->assigned_to,
        //     'title'   => 'Task Rescheduled',
        //     'message' => "Task '{$task->title}' has been rescheduled."
        // ]);
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task rescheduled successfully.');
    }

    public function submit(Request $request, Task $task)
    {
        if ($task->status === 'Approved') {
            return back()->withErrors('Task already approved.');
        }

        if ($task->due_date && now()->gt($task->due_date)) {
            return back()->withErrors('Task deadline exceeded.');
        }

        $request->validate([
            'submission_file' => 'required|file|max:2048',
        ]);

        $userId = auth()->id();

        // Get pivot row
        $pivot = $task->users()
            ->where('users.id', $userId)
            ->first()
            ->pivot;

        // 🔥 CALCULATE WORKED MINUTES
        $workedMinutes = \Carbon\Carbon::parse($task->start_date)
            ->diffInMinutes(now());

        // Store file
        // $path = $request->file('submission_file')
        //     ->store('task_uploads', 'public');

        // ✅ Update pivot
        $task->users()->updateExistingPivot(
            $userId,
            [
                'submitted_at'   => now(),
                'worked_minutes' => $workedMinutes,
            //   'submission_file'=> $path,
            ]
        );

        // Update task status
        $task->update(['status' => 'Submitted']);

        // Notify employer
        $employee = auth()->user();
        $employer = User::find($task->created_by);

        if ($employer) {
            $employer->notify(new TaskSubmittedNotification($task, $employee));
        }

        return back()->with('success', 'Task submitted successfully.');
    }

    public function approveTask(Task $task)
    {
        // only allow approving submitted tasks
        if ($task->status !== 'Submitted') {
            return back()->withErrors('Only submitted tasks can be approved.');
        }

        $task->update([
            'status' => 'Approved'
        ]);

        // 🔔 notify employee(s)
        // foreach ($task->users as $user) {
        //     $user->notify(new \App\Notifications\GenericNotification([
        //         'title' => 'Task Approved',
        //         'message' => "Your task '{$task->title}' has been approved."
        //     ]));
        // }

        return back()->with('success', 'Task approved successfully.');
    }

    public function rejectTask(Task $task)
    {
        if ($task->status !== 'Submitted') {
            return back()->withErrors('Only submitted tasks can be rejected.');
        }

        $task->update([
            'status' => 'Rejected'
        ]);

        // 🔔 notify employee(s)
        // foreach ($task->users as $user) {
        //     $user->notify(new \App\Notifications\GenericNotification([
        //         'title' => 'Task Rejected',
        //         'message' => "Your task '{$task->title}' has been rejected."
        //     ]));
        // }

        return back()->with('success', 'Task rejected successfully.');
    }

    public function requestMoreTime(Task $task)
    {
        if (! $task->users->contains(auth()->id())) {
            abort(403);
        }

        // Update task status
        $task->update([
            'status' => 'Not Started'
        ]);

        // 🔔 notify employer
        // Notification::create([
        //     'user_id' => $task->employer_id,
        //     'title'   => 'More Time Requested',
        //     'message' => "Employee requested more time for task '{$task->title}'."
        // ]);

        return back()->with('success', 'Request sent to employer');
    }
}
