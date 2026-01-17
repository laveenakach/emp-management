@extends('layouts.app')

@section('content')
<style>
    div.dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px; /* space above search */
    }
    .btn-outline-warning.custom-hover:hover {
        background-color: #66fdee !important;
        color: #000;
        border-color: #4d4b44 !important;
    }

    a.btn.rounded-pill.px-4.shadow-sm.add-btn:hover {
        background-color: #6bf9f0;
        color: black;
    }

    a.btn.rounded-pill.px-4.shadow-sm.add-btn {
        background-color: black;
        color: white;
        /* border: 2px solid red; */
    }
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<div class="container mt-2">
    <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Candidates</h3>
        <a href="{{ route('candidates.create') }}" class="btn rounded-pill px-4 shadow-sm add-btn">
            <i class="bi bi-plus-circle me-2"></i>Create Candidate
        </a>
    </div>

    <div class="d-flex d-md-none justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Candidates</h3>
            <a href="{{ route('candidates.create') }}">
                <i class="bi bi-plus-circle fs-5"></i>
            </a>
    </div>

    <!-- Toast Messages -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if (session('success'))
        <div class="toast show text-white bg-success" role="alert">
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
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
        <div class="card-body table-responsive">
            <div class="table-responsive">
                <table id="candidateTable" class="table table-hover table-bordered table-responsive">
                    <thead class="table-dark">
                        <tr>
                            <th>Sr No</th>
                            <th>Candidate Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>GST No</th>
                            <th>Bank Account</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($candidates as $candidate)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $candidate->candidate_id }}</td>
                            <td>{{ $candidate->name }}</td>
                            <td>{{ $candidate->email }}</td>
                            <td>{{ $candidate->phone }}</td>
                            <td>{{ $candidate->gst_number }}</td>
                            <td>{{ $candidate->bank_account_number }}</td>
                        
                            <td class="text-nowrap">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('candidates.show', $candidate->id) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('candidates.edit', $candidate->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('candidates.destroy', $candidate->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this candidate?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
        $('#candidateTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['excelHtml5'],
            pageLength: 10
        });
    });
</script>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            let toast = document.querySelector('.toast');
            if (toast) {
                let bsToast = new bootstrap.Toast(toast);
                bsToast.hide();
            }
        }, 7000);
    });
</script>
@endsection
