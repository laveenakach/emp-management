@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">{{ isset($Invoice) ? 'Edit' : 'Create' }} Invoice</h2>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ isset($Invoice) ? route('accounts.inv.update', $Invoice->id) : route('invoices.store') }}">
                        @csrf
                        @if(isset($Invoice)) @method('PUT') @endif

                        <div class="row">
                            <!-- Client Selection -->
                            <div class="col-md-6 mb-3">
                                <label>Client <span class="text-danger">*</span></label>
                                <select name="client_id" id="client_id" class="form-control" required>
                                    <option value="">-- Select Client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $Invoice->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }} ({{ $client->CLTuniq_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bill Number (Loaded via AJAX) -->
                            <div class="col-md-6 mb-3">
                                <label>Bill Number <span class="text-danger">*</span></label>
                                <select name="bill_number" id="bill_number" class="form-control" required>
                                    <option value="">-- Select Bill --</option>
                                </select>
                            </div>

                            <!-- Client Info (AJAX loaded) -->
                            <div class="col-md-12 mb-3" id="client-details" style="display: none;">
                                <div class="border p-3 rounded bg-light" id="client-info">
                                    <!-- AJAX content here -->
                                </div>
                            </div>

                            <!-- Invoice Date -->
                            <div class="col-md-6 mb-3">
                                <label>Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" value="{{ old('invoice_date', $Invoice->invoice_date ?? date('Y-m-d')) }}" class="form-control" required>
                            </div>

                            <!-- Total Amount -->
                            <div class="col-md-6 mb-3">
                                <label>Total Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="total_amount" value="{{ old('total_amount', $Invoice->total_amount ?? '') }}" class="form-control" required>
                            </div>

                            <!-- Tax -->
                            <!-- <div class="col-md-6 mb-3">
                                <label>Tax (%)</label>
                                <input type="number" step="0.01" name="tax_percent" value="{{ old('tax_percent', $Invoice->tax_percent ?? '') }}" class="form-control">
                            </div> -->

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    @php $status = old('status', $Invoice->status ?? 'Pending'); @endphp
                                    <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Paid" {{ $status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="Overdue" {{ $status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success mt-2">{{ isset($Invoice) ? 'Update' : 'Create' }} Invoice</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#client_id').change(function() {
        let clientId = $(this).val();
        $('#bill_number').html('<option value="">-- Select Bill --</option>');
        $('#client-info').html('');
        $('#client-details').hide();
        $('input[name="total_amount"]').val('');
        $('input[name="tax_percent"]').val('');

        if (clientId) {
            // Get Client Info
            $.get(`/accounts/get-client-details/${clientId}`, function(data) {
                $('#client-info').html(`
                    <p><strong>Name:</strong> ${data.name}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Phone:</strong> ${data.phone}</p>
                    <p><strong>Address:</strong> ${data.address}</p>
                `);
                $('#client-details').show();
            });

            // Get Bill Numbers
            $.get(`/accounts/get-bills/${clientId}`, function(bills) {
                $.each(bills, function(id, billNumber) {
                    $('#bill_number').append(`<option value="${billNumber}" data-id="${id}">${billNumber}</option>`);
                });

                @if(old('bill_number'))
                    $('#bill_number').val('{{ old("bill_number") }}').trigger('change');
                @elseif(isset($Invoice))
                    $('#bill_number').val('{{ $Invoice->bill_number }}').trigger('change');
                @endif
            });
        }
    });

    $('#bill_number').change(function() {
        let billId = $(this).find(':selected').data('id');
        if (billId) {
            $.get(`/accounts/get-bill-details/${billId}`, function(data) {
                $('input[name="total_amount"]').val(data.total_amount);
                $('input[name="tax_percent"]').val(data.tax_percent);
            });
        }
    });

    // Preload if editing
    @if(old('client_id') || isset($Invoice))
        $('#client_id').trigger('change');
    @endif
});
</script>
@endsection
