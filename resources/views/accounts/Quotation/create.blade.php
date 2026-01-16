@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-10">
            <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">{{ isset($quotation) ? 'Edit' : 'Create' }} Quotation</h2>
                <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="d-flex d-md-none align-items-center justify-content-between mb-2">
                <h2 class="fw-bold text-primary">{{ isset($quotation) ? 'Edit' : 'Create' }} Quotation</h2>

                <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-dark btn-sm">
                        <i class="bi bi-arrow-left"></i>
                    </a>
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
                    <form action="{{ isset($quotation) ? route('quotations.update', $quotation->id) : route('quotations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($quotation)) @method('PUT') @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Client</label>
                                <select name="client_id" class="form-control" required>
                                    <option value="">-- Select Client --</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $quotation->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} ({{ $client->CLTuniq_id }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Quotation Number</label>
                                <input type="text" id="quotation_number" name="quotation_number" class="form-control" value="{{ old('quotation_number', $quotation->quotation_number ?? '') }}" readonly required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Quotation Date</label>
                                <input type="date"
                                    name="quotation_date"
                                    class="form-control"
                                    value="{{ old('quotation_date', isset($quotation->quotation_date) ? \Carbon\Carbon::parse($quotation->quotation_date)->format('Y-m-d') : date('Y-m-d')) }}"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control" required>
                                    @foreach(['Draft', 'Sent', 'Accepted', 'Rejected'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $quotation->status ?? 'Draft') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $quotation->notes ?? '') }}</textarea>
                        </div>

                        <hr>
                        <h5 class="text-primary">Services</h5>

                        <div id="item-container">
                            @php
                            $items = old('items', isset($quotation) ? $quotation->items->toArray() : [['service_name' => '', 'description' => '', 'quantity' => '', 'rate' => '', 'amount' => '']]);
                            @endphp

                            @foreach ($items as $index => $item)
                            <div class="row mb-2 item-row">
                                <div class="col-md-3">
                                    <input type="text" name="items[{{ $index }}][service_name]" class="form-control" placeholder="Service Name" value="{{ $item['service_name'] ?? '' }}" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="items[{{ $index }}][description]" class="form-control" placeholder="Description" value="{{ $item['description'] ?? '' }}">
                                </div>
                                <div class="col-md-1">
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty" placeholder="Qty" value="{{ $item['quantity'] ?? '' }}" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="items[{{ $index }}][rate]" class="form-control rate" placeholder="Unit Price" value="{{ $item['rate'] ?? '' }}" min="0" required>
                                </div>
                                <div class="col-md-3 d-flex">
                                    <input type="number" name="items[{{ $index }}][amount]" class="form-control amount" placeholder="amount" value="{{ $item['amount'] ?? '' }}" readonly>
                                    <button type="button" class="btn btn-danger ms-1 remove-item">&times;</button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <button type="button" id="add-item" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-plus"></i> Add Services
                            </button>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-4">Save Quotation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function () {
        $('#quotation_date').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = @json(isset($items) ? count($items) : 0);

        // Your existing JS code here, e.g.:
        function calculateTotal(row) {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.rate').value) || 0;

            const baseAmount = qty * price;
            const gst = baseAmount * 0.18;
            const totalWithGst = baseAmount + gst;

            row.querySelector('.amount').value = baseAmount.toFixed(2);

            // row.querySelector('.total').value = (qty * price).toFixed(2);
        }

        document.getElementById('add-item').addEventListener('click', function() {
            let rowHtml = `
            <div class="row mb-2 item-row">
                <div class="col-md-3">
                    <input type="text" name="items[${itemIndex}][service_name]" class="form-control" placeholder="Service Name" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="Description">
                </div>
                <div class="col-md-1">
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty" placeholder="Qty" min="1" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${itemIndex}][rate]" class="form-control rate" placeholder="Rate" min="0" required>
                </div>
                <div class="col-md-3 d-flex">
                    <input type="number" name="items[${itemIndex}][amount]" class="form-control amount" placeholder="Amount " readonly>
                    <button type="button" class="btn btn-danger ms-1 remove-item">&times;</button>
                </div>
            </div>`;

            const container = document.getElementById('item-container');
            container.insertAdjacentHTML('beforeend', rowHtml);

            const newRow = container.lastElementChild;
            newRow.querySelector('.qty').addEventListener('input', () => calculateTotal(newRow));
            newRow.querySelector('.rate').addEventListener('input', () => calculateTotal(newRow));

            itemIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                e.target.closest('.item-row').remove();
            }
        });

        // Attach calculateTotal listeners to existing rows
        document.querySelectorAll('.item-row').forEach(row => {
            row.querySelector('.qty').addEventListener('input', () => calculateTotal(row));
            row.querySelector('.rate').addEventListener('input', () => calculateTotal(row));
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientSelect = document.querySelector('select[name="client_id"]');
        const quotationInput = document.getElementById('quotation_number');

        clientSelect.addEventListener('change', function() {
            const clientId = this.value;
            if (!clientId) return;

            const now = new Date();
            const year = now.getFullYear().toString().slice(-2);
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const day = now.getDate().toString().padStart(2, '0');
            const random = Math.floor(1000 + Math.random() * 9000);

            const quotationNumber = `QT-${clientId}-${year}${month}${day}-${random}`;
            quotationInput.value = quotationNumber;
        });
    });
</script>