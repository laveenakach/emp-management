<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #2b2b2b;
            margin: 10px 30px 30px 30px;
            line-height: 1.4;
        }

        h2 {
            text-align: center;
            color: #003366;
            font-size: 18px;
            margin: 10px 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .company-info,
        .client-details,
        .totals {
            width: 100%;
            margin-bottom: 10px;
        }

        .company-info td {
            vertical-align: top;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #003366;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px;
        }

        thead {
            background-color: #e6f2ff;
        }

        th {
            color: #003366;
            text-align: left;
        }

        td:last-child,
        th:last-child {
            text-align: right;
        }

        .totals td:first-child {
            text-align: right;
            font-weight: bold;
        }

        .totals td:last-child {
            text-align: right;
        }

        .totals tr:last-child td {
            background-color: #f0faff;
            font-size: 13px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 25px;
            color: #888;
        }

        .company-info td:last-child {
            text-align: center;
            border: none;
        }

        .company-info td:first-child {
            border: none;
        }

        .client-details td:last-child {
            text-align: left;
        }
    </style>
</head>

<body>

    <h2>Invoice</h2>

    <table class="company-info">
        <tr>
            <td>
                <div class="company-name">Adlertech Innovations OPC Pvt Ltd</div>
                Plot No 39, Yeswant Colony, Alka Sheti Farm, Kagal, Kolhapur, Maharashtra - 416216<br>
                Email: adler@techadler.com | Phone: +91-9404621503<br>
                CIN: U72900PN2020OPC194593
            </td>
            <td width="40%">
                <img src="{{ public_path('images/cropped-1-1.png') }}" alt="Company Logo" width="180px">
            </td>
        </tr>
    </table>
    <p><strong>Invoice details:</strong></p>
    <table class="client-details">
        <tr>
            <td><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</td>
            <td><strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td><strong>Due Date:</strong> {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '' }}</td>
            <td><strong>Status:</strong> {{ ucfirst($invoice->status) }}</td>
        </tr>
    </table>
    <p><strong>Candidate details:</strong></p>
    <table class="client-details">
        <tr>
            <td><strong>Candidate Name:</strong> {{ $invoice->candidate->name }}</td>
            <td><strong>Email:</strong> {{ $invoice->candidate->email }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong> {{ $invoice->candidate->phone }}</td>
            <td><strong>Address:</strong> {{ $invoice->candidate->address }}</td>
        </tr>
        <tr>
            <td><strong>GSTIN:</strong> {{ $invoice->candidate->gst_number }}</td>
            <td><strong>Bank A/C:</strong> {{ $invoice->candidate->bank_account_number }} ({{ $invoice->candidate->ifsc_code }})</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Rate (₹)</th>
                <th>Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @foreach ($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ number_format($item->rate, 2) }}</td>
                <td>{{ number_format($item->amount, 2) }}</td>
            </tr>
            @php $subtotal += $item->amount; @endphp
            @endforeach
        </tbody>
    </table>
    <br>
    <table class="totals">
        @php
        $discountAmount = ($subtotal * $invoice->discount) / 100;
        $convenience_feesAmount = ($subtotal * $invoice->convenience_fees) / 100;
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = ($afterDiscount * $invoice->total_tax_percent) / 100;
        $finalAmount = $afterDiscount + $taxAmount - $convenience_feesAmount;
        @endphp
        <tr>convenience_fees
            <td width="30%">Subtotal:</td>
            <td>₹{{ number_format($subtotal, 2) }}</td>
            <td><strong>Discount</strong>({{ $invoice->discount }}%):</td>
            <td>₹{{ number_format($subtotal * $invoice->discount / 100, 2) }}</td>
        </tr>
        <tr>
            <td>CGST ({{ $invoice->cgst_percent }}%):</td>
            <td>₹{{ number_format($afterDiscount * $invoice->cgst_percent / 100, 2) }}</td>
            <td><strong>SGST</strong> ({{ $invoice->sgst_percent }}%):</td>
            <td>₹{{ number_format($afterDiscount * $invoice->sgst_percent / 100, 2) }}</td>
        </tr>
        <tr>
            <td>GST ({{ $invoice->gst_percent }}%):</td>
            <td>₹{{ number_format($afterDiscount * $invoice->gst_percent / 100, 2) }}</td>
            <td><strong>Total Tax</strong> ({{ $invoice->total_tax_percent }}%):</td>
            <td>₹{{ number_format($taxAmount, 2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><strong>Convenience Fees</strong> ({{ $invoice->convenience_fees }}%):</td>
            <td>₹{{ number_format($convenience_feesAmount, 2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><strong>Grand Total:</strong></td>
            <td><strong>₹{{ number_format($invoice->total_amount, 2) }}</strong></td>
        </tr>
    </table>

    <table class="company-info">
        <tr>
            <td>
                <div class="company-name">Payment Instructions:</div>
                Bank Transfer / UPI details: ________ <br>
                <!-- Your next installment of 10000 rs due on 5th December 2025
                Your total fees of Enrollment program for 6 month is 40000 rs including 1 % convience charges.<br> Please make payment on time<br>
                Thank you<br> -->
                <textarea 
                        name="payment_instructions" 
                        class="form-control" 
                        rows="5"
                        placeholder="Enter payment instructions here...">
                    </textarea>
                <!-- Please ensure payment is made before the due date to confirm your admission -->
            </td>
            <td width="40%">
                <img src="{{ public_path('images/qrscanner.jpeg') }}" alt="Company Logo" width="240px">
            </td>
        </tr>
    </table>

    <div class="terms">
        <p><strong>Terms & Conditions:</strong></p>
        <p>- Full payment must be made to activate Capstone Projects, Mock Interviews, Final Certification & Placement Assistance.<br>
            - Installment payments as per Fee Policy.<br>
            - Late payment penalty: ₹500/week for delayed installments.<br>
            - Refund & Deferral as per Annexure.<br>
            - This is without GST invoice. ITC claims subject to GST rules.</p>
    </div>
    <div>
        <p><strong>Declaration:</strong></p>
        This invoice is issued for admission convenience/procedure fees only. GST is not applicable for this transaction.
    </div>

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="text-align: right;">
                <p><strong>Signature & Stamp</strong><br>
                    (For Adlertech Innovations OPC Pvt Ltd)</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for choosing Adlertech Innovations OPC Pvt Ltd</p>
    </div>
</body>

</html>