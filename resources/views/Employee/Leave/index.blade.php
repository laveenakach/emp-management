@extends('layouts.app')

@section('content')
<style>
    .btn-outline-warning.custom-hover:hover {
        background-color: #66fdee !important;
        /* Your desired hover color */
        color: #000;
        /* Text color on hover */
        border-color: #4d4b44 !important;
    }

    a.btn.rounded-pill.px-4.shadow-sm.add-btn:hover {
        background-color: #6bf9f0;
        color: black;
    }

    a.btn.rounded-pill.px-4.shadow-sm.add-btn {
        background-color: black;
        color: white;
        border: 2px soild red;
    }
    div.dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px; /* space above search */
    }
</style>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<div class="container mt-2">
    <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Employee Leave Requests</h3>
        <a href="{{ route('employee.leaves.create') }}" class="btn rounded-pill px-4 shadow-sm add-btn">
            <i class="bi bi-plus-circle me-2"></i> Add Leave
        </a>
    </div>

    <div class="d-flex d-md-none justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Employee Leave Requests</h3>
        <a href="{{ route('employee.leaves.create') }}"
                class="text-decoration-none text-dark">
                    <i class="bi bi-plus-circle fs-5"></i>
                </a>
    </div>

    <!-- Toast Messages -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if (session('status'))
        <div class="toast show text-white bg-success" role="alert">
            <div class="d-flex">
                <div class="toast-body">{{ session('status') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        @endif

        @if ($errors->any())
        <div class="toast show text-white bg-danger" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
            <table id="leaveTable" class="table table-hover table-bordered nowrap" style="width:100%;">
                <thead class="table-dark">
                    <tr>
                        <th>Sr. No</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Leave Request Date</th>
                        <th>Reason</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Paid / Unpaid</th>
                        <th>Leave Duration</th>
                        <th>Approved By</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaves as $leave)
                    <tr>
                        <td>{{ $loop->iteration }}</td> {{-- Serial Number --}}
                        <td>{{ $leave->user->name }}</td>
                        <td>{{ $leave->department_name }}</td>
                        <td>{{ $leave->leave_type ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->date)->format('d-m-Y') }}</td>
                        <td>{{ $leave->reason ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('d-m-Y') }}</td>
                        <td>{{ $leave->is_paid_leave ?? '-' }}</td>
                        <td>{{ $leave->leave_duration ?? '-' }}</td>
                        <td>{{ $leave->approved_by ?? '-' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $leave->status == 'Approved' ? 'success' : ($leave->status == 'Rejected' ? 'danger' : 'secondary') }}">
                                    {{ $leave->status }}
                                </span>

                                @if(Auth::user()->role === 'employer')
                                <button class="btn btn-sm btn-outline-warning ms-2" data-bs-toggle="modal" data-bs-target="#statusModal{{ $leave->id }}">
                                    <i class="bi bi-pencil-square me-1"></i>Manage Status
                                </button>
                                @endif
                            </div>


                            <!-- Modal -->
                            <div class="modal fade" id="statusModal{{ $leave->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $leave->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('employee.leaves.updateStatus', $leave->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel{{ $leave->id }}">Update Leave Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="approved_by_{{ $leave->id }}" class="form-label">Approved By</label>
                                                    <input type="text" class="form-control" name="approved_by" id="approved_by_{{ $leave->id }}" value="{{ $leave->approved_by }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Paid / Unpaid <span class="text-danger">*</span></label>
                                                    <select name="is_paid_leave" class="form-control" required>
                                                        <option value="">-Select-</option>
                                                        <option value="Paid">Paid</option>
                                                        <option value="Unpaid">Unpaid</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="status_{{ $leave->id }}" class="form-label">Status<span class="text-danger">*</span></label>
                                                    <select name="status" id="status_{{ $leave->id }}" class="form-select">
                                                        <option value="">-Select-</option>
                                                        <option value="Pending" {{ $leave->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="Approved" {{ $leave->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="Rejected" {{ $leave->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Save changes</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    $(document).ready(function() {
        $('#leaveTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['excelHtml5'],
            pageLength: 10,
            // scrollX: true,
            // scrollY: true,
        });
    });
</script>

<!-- Bootstrap Icons CDN (Optional) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss toast after 5 seconds
        setTimeout(() => {
            let toast = new bootstrap.Toast('.toast');
            toast.hide();
        }, 7000); // Toast will disappear after 5 seconds
    });
</script>

@endsection