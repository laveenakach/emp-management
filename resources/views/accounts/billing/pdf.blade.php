<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bill - {{ $bill->bill_number }}</title>
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
            text-align: left;
        }

        .client-details td:last-child {
            text-align: left;
        }
    </style>
</head>

<body>

    <h2>Bill</h2>

    <table class="company-info">
        <tr>
            <td width="40%">
                <img src="{{ public_path('images/cropped-1-1.png') }}" alt="Company Logo" width="130px">
            </td>
            <td>
                <div class="company-name">Adlertech Innovations OPC Pvt Ltd</div>
                Plot No 39, Yeswant Colony, Alka Sheti Farm, Kagal, Kolhapur, Maharashtra - 416216<br>
                Email: ranapratap@techadler.com | Phone: +91-9404621503<br>
                CIN: U72900PN2020OPC194593
            </td>
        </tr>
    </table>

    <table class="client-details">
        <tr>
            <td><strong> Bill No:</strong> {{ $bill->bill_number }}</td>
            <td><strong> Date:</strong> {{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td><strong> Due Date:</strong> {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</td>
            <td><strong> Status:</strong> {{ $bill->status }}</td>
        </tr>
    </table>

    <table class="client-details">
        <tr>
            <td><strong> Client Name:</strong> {{ $bill->client->name }}</td>
            <td><strong> Email:</strong> {{ $bill->client->email }}</td>
        </tr>
        <tr>
            <td><strong> Phone:</strong> {{ $bill->client->phone }}</td>
            <td><strong> Address:</strong> {{ $bill->client->address }}</td>
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
            @foreach ($bill->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->rate, 2) }}</td>
                <td>{{ number_format($item->amount, 2) }}</td>
            </tr>
            @php $subtotal += $item->amount; @endphp
            @endforeach
        </tbody>
    </table>

    <br>

    <table class="totals">
        <tr>
            <td width="85%">Subtotal:</td>
            <td>₹{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>CGST ({{ $bill->cgst_percent }}%):</td>
            <td>₹{{ number_format($subtotal * $bill->cgst_percent / 100, 2) }}</td>
        </tr>
        <tr>
            <td>SGST ({{ $bill->sgst_percent }}%):</td>
            <td>₹{{ number_format($subtotal * $bill->sgst_percent / 100, 2) }}</td>
        </tr>
        <tr>
            <td>GST ({{ $bill->gst_percent }}%):</td>
            <td>₹{{ number_format($subtotal * $bill->gst_percent / 100, 2) }}</td>
        </tr>
        <tr>
            <td>Total Tax ({{ $bill->total_tax_percent }}%):</td>
            <td>₹{{ number_format(($subtotal * $bill->total_tax_percent / 100), 2) }}</td>
        </tr>
        <tr>
            <td><strong>Grand Total:</strong></td>
            <td><strong>₹{{ number_format($bill->total_amount, 2) }}</strong></td>
        </tr>
    </table>

    @if($bill->bill_notes)
    <div class="terms">
        <p><strong>Terms & Conditions:</strong></p>
            @if ($bill->bill_notes)
        <p>{!! nl2br(e($bill->bill_notes)) !!}</p>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>Thank you for choosing Adlertech Innovations OPC Pvt Ltd</p>
    </div>

</body>

</html>