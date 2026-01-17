@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-10">

            <div class="d-none d-md-flex gap-2 d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary">{{ isset($invoice) ? 'Edit Candidate Invoice' : 'Create Candidate Invoice' }}</h2>
                <a href="{{ route('candidates.invoices.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="d-flex d-md-none align-items-center justify-content-between mb-2">
                <h2 class="fw-bold text-primary">{{ isset($invoice) ? 'Edit Candidate Invoice' : 'Create Candidate Invoice' }}</h2>

                <a href="{{ route('candidates.invoices.index') }}" class="btn btn-dark btn-sm">
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
                    <form method="POST" action="{{ isset($invoice) ? route('candidates.invoices.update',$invoice->id) : route('candidates.invoices.store') }}">
                        @csrf
                        @if(isset($invoice)) @method('PUT') @endif

                        <div class="row">
                            <!-- Candidate -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Candidate <span class="text-danger">*</span></label>
                                <select name="candidate_id" class="form-control rounded-pill" required>
                                    <option value="">-- Select Candidate --</option>
                                    @foreach($candidates as $candidate)
                                        <option value="{{ $candidate->id }}" {{ old('candidate_id',$invoice->candidate_id ?? '') == $candidate->id ? 'selected' : '' }}>
                                            {{ $candidate->name }} ({{ $candidate->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Invoice Date -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control rounded-pill"
                                       value="{{ old('invoice_date',$invoice->invoice_date ?? now()->format('Y-m-d')) }}" required>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Due Date</label>
                                <input type="date" name="due_date" class="form-control rounded-pill"
                                       value="{{ old('due_date',$invoice->due_date ?? '') }}">
                            </div>

                            <!-- Discount -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Discount (%)</label>
                                <input type="text" name="discount" id="discount" class="form-control rounded-pill"
                                       value="{{ old('discount',$invoice->discount ?? 0) }}">
                            </div>

                            <!-- Discount -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Convenience Fees (%)</label>
                                <input type="number" name="convenience_fees" id="convenience_fees" class="form-control rounded-pill"
                                       value="{{ old('convenience_fees',$invoice->convenience_fees ?? 0) }}">
                            </div>

                            <!-- GST -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-semibold">GST (%)</label>
                                <input type="number" name="gst_percent" id="gst_percent" class="form-control rounded-pill"
                                       value="{{ old('gst_percent',$invoice->gst_percent ?? 0) }}">
                            </div>

                            <!-- CGST -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-semibold">CGST (%)</label>
                                <input type="number" name="cgst_percent" id="cgst_percent" class="form-control rounded-pill"
                                       value="{{ old('cgst_percent',$invoice->cgst_percent ?? 0) }}">
                            </div>

                            <!-- SGST -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-semibold">SGST (%)</label>
                                <input type="number" name="sgst_percent" id="sgst_percent" class="form-control rounded-pill"
                                       value="{{ old('sgst_percent',$invoice->sgst_percent ?? 0) }}">
                            </div>

                            <!-- Status -->
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                @php $status = old('status',$invoice->status ?? 'Pending'); @endphp
                                <select name="status" class="form-control rounded-pill" required>
                                    <option value="Pending" {{ $status=='Pending'?'selected':'' }}>Pending</option>
                                    <option value="Paid" {{ $status=='Paid'?'selected':'' }}>Paid</option>
                                    <option value="Overdue" {{ $status=='Overdue'?'selected':'' }}>Overdue</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Service Items -->
                        <h5 class="fw-bold mb-3">Service Items</h5>
                        <table class="table table-bordered" id="items_table">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th width="10%">Qty</th>
                                    <th width="15%">Rate</th>
                                    <th width="15%">Amount</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($invoice) && $invoice->items->count())
                                    @foreach($invoice->items as $i => $item)
                                        <tr>
                                            <td><input type="text" name="items[{{ $i }}][description]" class="form-control" value="{{ $item->description }}" required></td>
                                            <td><input type="number" name="items[{{ $i }}][qty]" class="form-control qty" value="{{ $item->qty }}"></td>
                                            <td><input type="number" name="items[{{ $i }}][rate]" class="form-control rate" value="{{ $item->rate }}"></td>
                                            <td><input type="text" name="items[{{ $i }}][amount]" class="form-control amount" value="{{ $item->amount }}" readonly></td>
                                            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td><input type="text" name="items[0][description]" class="form-control" required></td>
                                        <td><input type="number" name="items[0][qty]" class="form-control qty" value="1" min="1"></td>
                                        <td><input type="number" name="items[0][rate]" class="form-control rate" value="0"></td>
                                        <td><input type="text" name="items[0][amount]" class="form-control amount" readonly></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-success mb-3" id="addRow">
                            <i class="bi bi-plus-circle"></i> Add Item
                        </button>

                        <!-- Totals -->
                        <div class="row mt-4">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr><th>Subtotal:</th><td><span id="subtotal">0</span></td></tr>
                                    <tr><th>Total Tax:</th><td><span id="total_tax">0</span></td></tr>
                                    <tr><th>Discount:</th><td><span id="total_discount">0</span></td></tr>
                                    <tr><th>Convenience Fees:</th><td><span id="convenience_fees_amount">0</span></td></tr>
                                    <tr class="fw-bold"><th>Grand Total:</th><td><span id="grand_total">0</span></td></tr>
                                </table>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">
                                <i class="bi bi-save me-1"></i> {{ isset($invoice) ? 'Update Invoice' : 'Save Invoice' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener("DOMContentLoaded", function() {
    let row = {{ isset($invoice) && $invoice->items->count() ? $invoice->items->count() : 1 }};

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll("#items_table tbody tr").forEach(tr => {
            let qty = parseFloat(tr.querySelector(".qty").value) || 0;
            let rate = parseFloat(tr.querySelector(".rate").value) || 0;
            let amount = qty * rate;
            tr.querySelector(".amount").value = amount.toFixed(2);
            subtotal += amount;
        });

        let discount = parseFloat(document.getElementById("discount").value) || 0;
        let convenience_fees = parseFloat(document.getElementById("convenience_fees").value) || 0;
        let gst = parseFloat(document.getElementById("gst_percent").value) || 0;
        let cgst = parseFloat(document.getElementById("cgst_percent").value) || 0;
        let sgst = parseFloat(document.getElementById("sgst_percent").value) || 0;

        let total_tax_percent = gst + cgst + sgst;
        let discount_amount = subtotal * (discount / 100);
        let convenience_fees_amount = subtotal * (convenience_fees / 100);
        let subtotal1 = subtotal - discount_amount;
        let tax = subtotal1 * (total_tax_percent / 100);
        let grand_total = subtotal1 + tax + convenience_fees_amount;

        document.getElementById("subtotal").innerText = subtotal.toFixed(2);
        document.getElementById("total_tax").innerText = tax.toFixed(2);
        document.getElementById("total_discount").innerText = discount_amount.toFixed(2);
        document.getElementById("convenience_fees_amount").innerText = convenience_fees_amount.toFixed(2);
        document.getElementById("grand_total").innerText = grand_total.toFixed(2);
        document.getElementById("grand_total").innerText = grand_total.toFixed(2);
    }

    document.getElementById("addRow").addEventListener("click", () => {
        let tbody = document.querySelector("#items_table tbody");
        let tr = document.createElement("tr");
        tr.innerHTML = `
            <td><input type="text" name="items[${row}][description]" class="form-control" required></td>
            <td><input type="number" name="items[${row}][qty]" class="form-control qty" value="1" min="1"></td>
            <td><input type="number" name="items[${row}][rate]" class="form-control rate" value="0"></td>
            <td><input type="text" name="items[${row}][amount]" class="form-control amount" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        row++;
    });

    document.addEventListener("input", e => {
        if (e.target.classList.contains("qty") || e.target.classList.contains("rate") ||
            e.target.id === "discount" || e.target.id === "gst_percent" ||
            e.target.id === "cgst_percent" || e.target.id === "sgst_percent") {
            calculateTotals();
        }
    });

    document.addEventListener("click", e => {
        if (e.target.closest(".remove-row")) {
            e.target.closest("tr").remove();
            calculateTotals();
        }
    });

    calculateTotals();
});
</script>
