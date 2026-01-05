@extends('layouts.app')
@section('title', 'View Task')

@section('content')
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Task Card --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="text-primary fw-bold mb-0">
                            <i class="bi bi-journal-text me-2"></i>Task Details
                        </h3>
                        <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="bi bi-arrow-left-circle me-1"></i> Back
                        </a>

                    </div>

                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted">Title</label>
                            <div>{{ $task->title }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted">Priority</label>
                            <div>{{ $task->priority }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted">Status</label>
                            <div>{{ $task->status }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted">Role</label>
                            <div>{{ $task->role }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted">Start Date</label>
                            <div>{{ \Carbon\Carbon::parse($task->start_date)->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted">Due Date</label>
                            <div>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted">Assigned User</label>
                            <div>{{ $task->assignedUser->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold text-muted">Description</label>
                            <div>{{ $task->description }}</div>
                        </div>
                        @if($task->file_path)
                        <div class="col-md-12 mt-3">
                            <label class="fw-semibold text-muted">Attachment</label><br>
                            <a href="{{ asset($task->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-arrow-down"></i> View Attachment
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Comments Section --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="text-secondary fw-bold mb-3"><i class="bi bi-chat-dots me-2"></i>Comments</h5>

                    {{-- Display Comments --}}
                    <div class="mb-4">
                        @forelse($task->comments as $comment)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">{{ $comment->user->name }}</strong>
                                <span class="text-muted small">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-dark">
                                {{ $comment->message }}
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No comments yet. Be the first to comment!</p>
                        @endforelse
                    </div>

                    {{-- Add Comment --}}
                    @if(auth()->check())
                    <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">Add a comment</label>
                            <textarea name="message" rows="3" class="form-control @error('message') is-invalid @enderror" placeholder="Write your comment..." required></textarea>
                            @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-send"></i> Post Comment
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection