<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'employer') {
            // Admin-created notifications
            $notifications = DatabaseNotification::latest()->get()->map(function ($n) {
            // Fetch the actual receiver of the notification
            $employee = User::find($n->notifiable_id);

            $n->employee_name = $employee?->name ?? '—';

            return $n;
        });
        } else {
            // Task assigned & system notifications
            $notifications = $user->notifications()->latest()->get();
        }

        return view('employer.Notification.index', compact('notifications'));
    }

    public function create()
    {
        $url = route('notifications.store');
        return view('employer.notification.create', compact('url'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['title', 'date', 'description']);

        $path = null;

        if ($request->hasFile('attachment')) {
            $pdf = $request->file('attachment');
            // Generate a unique filename
            $pdfName = time() . '_' . uniqid() . '.' . $pdf->getClientOriginalExtension();
            // Move the file to the public/uploads/tasks directory
            $pdf->move(public_path('uploads/notifications'), $pdfName);
            // Store the relative file path
            $path = 'uploads/notifications/' . $pdfName;
        }

        $data['created_by'] = auth()->id();

        $data['attachment'] = isset($path) ? $path: NULL;


        Notification::create($data);

        return redirect()->route('notifications.index')->with('success', 'Notification created successfully!');
    }

    public function edit($id)
    {

        $notifications = Notification::where('id', $id)->first();  // Get all departments

        return view('employer.notification.create', [
            'notification' => $notifications,
            'url' => route('employer.notifications.update', $id),
        ]);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,docx,jpg,jpeg,png|max:2048',
        ]);

        $path = null;

        if ($request->hasFile('attachment')) {
            $pdf = $request->file('attachment');
            // Generate a unique filename
            $pdfName = time() . '_' . uniqid() . '.' . $pdf->getClientOriginalExtension();
            // Move the file to the public/uploads/tasks directory
            $pdf->move(public_path('uploads/notifications'), $pdfName);
            // Store the relative file path
            $path = 'uploads/notifications/' . $pdfName;
        }

        $Notification = Notification::findOrFail($id);
        $Notification->title = $request->title;
        $Notification->date = $request->date;
        $Notification->description = $request->description;

        if(isset($path)){
            $Notification->attachment = $path;
        }
        $Notification->save();

        return redirect()->route('notifications.index')->with('success', 'Notification updated successfully.');

    }

    public function destroy( $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return redirect()->route('notifications.index')->with('success', 'Notification deleted.');
    }

    public function markRead($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return back();
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}
