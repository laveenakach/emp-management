@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: rgb(26 24 57) !important;">
            <h4 class="mb-0">Invoice Details</h4>
            <a href="{{ route('candidates.invoices.index') }}" class="btn btn-light btn-sm">← Back to List</a>
        </div>

        <div class="card-body">
            {{-- Invoice Info --}}
            <h5 class="fw-bold mb-3">Invoice Info</h5>
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Invoice No</th>
                    <td>{{ $invoice->invoice_no }}</td>
                    <th>Invoice Date</th>
                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Due Date</th>
                    <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '' }}</td>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                </tr>
            </table>

            {{-- Candidate Info --}}
            <h5 class="fw-bold mt-4 mb-3">Candidate Info</h5>
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Name</th>
                    <td>{{ $invoice->candidate->name }}</td>
                    <th>Email</th>
                    <td>{{ $invoice->candidate->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $invoice->candidate->phone }}</td>
                    <th>GST Number</th>
                    <td>{{ $invoice->candidate->gst_number }}</td>
                </tr>
                
                <tr>
                    <th>Bank Account</th>
                    <td>{{ $invoice->candidate->bank_account_number }} (IFSC: {{ $invoice->candidate->ifsc_code }})</td>
                    <th>Address</th>
                    <td>{{ $invoice->candidate->address }}</td>
                </tr>
            </table>

            {{-- Service Items --}}
            <h5 class="fw-bold mt-4 mb-3">Service Items</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Rate (₹)</th>
                        <th class="text-end">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach($invoice->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                            <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @php $subtotal += $item->amount; @endphp
                    @endforeach
                </tbody>
            </table>

            {{-- Totals --}}
            <h5 class="fw-bold mt-4 mb-3">Summary</h5>
            <table class="table table-bordered w-50 ms-auto">
                <tr>
                    <th width="40%">Subtotal</th>
                    <td class="text-end">₹{{ number_format($subtotal, 2) }}</td>
                     <th>Discount</th>
                    <td class="text-end">₹{{ number_format($subtotal * $invoice->discount / 100, 2) }}</td>
                </tr>
                <tr>
                    <th>CGST ({{ $invoice->cgst_percent }}%)</th>
                    <td class="text-end">₹{{ number_format($subtotal * $invoice->cgst_percent / 100, 2) }}</td>
                    <th>SGST ({{ $invoice->sgst_percent }}%)</th>
                    <td class="text-end">₹{{ number_format($subtotal * $invoice->sgst_percent / 100, 2) }}</td>
                </tr>
                
                <tr>
                    <th>GST ({{ $invoice->gst_percent }}%)</th>
                    <td class="text-end">₹{{ number_format($subtotal * $invoice->gst_percent / 100, 2) }}</td>
                    <th>Convenience Fees ({{ $invoice->convenience_fees }}%)</th>
                    <td class="text-end">₹{{ number_format($subtotal * $invoice->convenience_fees / 100, 2) }}</td>
                </tr>
               
                <tr class="table-light fw-bold">
                    <th></th>
                    <td class="text-end"></td>
                    <th>Grand Total</th>
                    <td class="text-end">₹{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </table>

        </div>
    </div>
</div>
@endsection
