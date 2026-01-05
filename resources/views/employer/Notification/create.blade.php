@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="text-center mb-4">
                <h3 class="mb-4">{{ isset($notification->id) ? '✏️ Edit Notification' : '📢 Create Notification' }}</h3>

                <p class="text-muted">Send a notification to all employees with optional attachments.</p>
            </div>

            @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ $url }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($notification))
                        @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">📌 Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control rounded-pill" value="{{ old('title', isset($notification) ? $notification->title : '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label fw-semibold">🗓 Date <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control rounded-pill" value="{{ old('date', isset($notification) ? $notification->date : '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">📝 Description</label>
                            <textarea id="description" name="description" rows="4" class="form-control rounded-4">{{ old('description',isset($notification) ? $notification->description : '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label fw-semibold">📎 Attachment (Optional)</label>
                            <input type="file" name="attachment" class="form-control">

                            @if(isset($notification->attachment))
                            <div class="mt-2">
                                <a href="{{ asset($notification->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-paperclip me-1"></i> View Current File
                                </a>
                            </div>
                            @endif
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2">
                                <i class="bi bi-send-check me-1"></i> {{ isset($notification->id) ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('notifications.index') }}" class="text-decoration-none text-secondary">
                    <i class="bi bi-arrow-left-circle me-1"></i> Back to Notifications
                </a>
            </div>
        </div>
    </div>
</div>
@endsection