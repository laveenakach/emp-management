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
</style>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<div class="container mt-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">Quotation</h3>
        <a href="{{ route('quotations.create') }}" class="btn rounded-pill px-4 shadow-sm add-btn">
            <i class="bi bi-plus-circle me-2"></i>Create Quotation
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
        <div class="card-body table-responsive">
            <table id="salarySlipTable" class="table table-hover table-bordered table-responsive">
                <thead class="table-light">
                    <tr>
                        <th>Sr no</th>
                        <th>Quotation No</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>PDF</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Quotations as $q)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $q->quotation_number }}</td>
                        <td>{{ $q->client->name }}</td>
                        <td>{{ $q->quotation_date }}</td>
                        <td>{{ $q->status }}</td>
                        <td>{{ $q->notes }}</td>
                        <td>
                            <a href="{{ route('quotations.download', $q->id) }}" class="btn btn-sm btn-primary">
                                Download PDF
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('quotations.edit', $q->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('quotations.destroy', $q->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this quotation?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            <!-- <form action="{{ route('quotations.email', $q->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-info">Email</button>
                            </form> -->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
        $('#salarySlipTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['excelHtml5'],
            pageLength: 10
        });
    });
</script>

<!-- Bootstrap Icons CDN (Optional) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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