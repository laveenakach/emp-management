@extends('layouts.app')

@section('content')


<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-10">
            <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">{{ isset($Bill) ? 'Edit' : 'Create' }} Bill</h2>
                <a href="javascript:void(0);" onclick="window.history.back();" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="d-flex d-md-none align-items-center justify-content-between mb-2">
                <h2 class="fw-bold text-primary">{{ isset($Bill) ? 'Edit' : 'Create' }} Bill</h2>

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
                    <form method="POST" action="{{ isset($Bill) ? route('billings.update', $Bill->id) : route('billings.store') }}">
                        @csrf
                        @if(isset($Bill)) @method('PUT') @endif

                        <div class="mb-3">
                            <label>Client</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $Bill->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} ({{ $client->CLTuniq_id }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Bill Number</label>
                                <input type="text" id="bill_number" name="bill_number" value="{{ old('bill_number', $Bill->bill_number ?? '') }}" class="form-control" readonly required>
                            </div>
                            <div class="col">
                                <label>Bill Date</label>
                                <input type="date" name="bill_date" value="{{ old('bill_date', $Bill->bill_date ?? '') }}" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Due Date</label>
                                <input type="date" name="due_date" value="{{ old('due_date', $Bill->due_date ?? '') }}" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- <div class="col">
                                <label>Tax (%)</label>
                                <input type="number" step="0.01" name="tax_percent" value="{{ old('tax_percent', $Bill->tax_percent ?? '18') }}" class="form-control" required>
                            </div> -->


                            <label>Tax (%)</label>

                            <div class="col-md-2">
                                <label><input type="checkbox" name="gst" value="18"
                                        {{ old('gst', $Bill->gst_percent ?? 0) > 0 ? 'checked' : '' }}> GST 18%</label>
                            </div>
                            <div class="col-md-2">
                                <label><input type="checkbox" name="cgst" value="9"
                                        {{ old('cgst', $Bill->cgst_percent ?? 0) > 0 ? 'checked' : '' }}> CGST 9%</label>
                            </div>
                            <div class="col-md-2">
                                <label><input type="checkbox" name="sgst" value="9"
                                        {{ old('sgst', $Bill->sgst_percent ?? 0) > 0 ? 'checked' : '' }}> SGST 9%</label>
                            </div>


                            <div class="col-md-4">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    @php $status = old('status', $Bill->status ?? 'Pending'); @endphp
                                    <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Paid" {{ $status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="Overdue" {{ $status == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="bill_notes" class="form-control" rows="3">{{ old('bill_notes', $Bill->bill_notes ?? '') }}</textarea>
                            </div>
                        </div>

                        <h5>Bill Items</h5>
                        <table class="table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">+</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $items = old('items', isset($Bill) ? $Bill->items : [['description' => '', 'quantity' => '', 'rate' => '']]);
                                @endphp
                                @foreach($items as $index => $item)
                                <tr>
                                    <td><input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item['description'] ?? '' }}" required></td>
                                    <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item['quantity'] ?? '' }}" required></td>
                                    <td><input type="number" name="items[{{ $index }}][rate]" class="form-control" value="{{ $item['rate'] ?? '' }}" required></td>
                                    <td>
                                        @if ($loop->first)
                                        <!-- No remove button on first item -->
                                        @else
                                        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">X</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-success">{{ isset($Bill) ? 'Update' : 'Create' }} Bill</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let itemIndex = {
        {
            count($items)
        }
    };

    function addItem() {
        const table = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
        const row = table.insertRow();
        row.innerHTML = `
            <td><input type="text" name="items[${itemIndex}][description]" class="form-control" required></td>
            <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control" required></td>
            <td><input type="number" name="items[${itemIndex}][rate]" class="form-control" required></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">X</button></td>
        `;
        itemIndex++;
    }
</script>


<script>
    function generateBillNumber(clientId) {
        if (!clientId) return;

        const now = new Date();
        const year = now.getFullYear().toString().substr(-2);
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const day = now.getDate().toString().padStart(2, '0');
        const random = Math.floor(1000 + Math.random() * 9000); // Random 4-digit

        const billNumber = `BILL-${clientId}-${year}${month}${day}-${random}`;
        document.getElementById('bill_number').value = billNumber;
    }

    document.querySelector('select[name="client_id"]').addEventListener('change', function() {
        generateBillNumber(this.value);
    });
</script>

@endsection