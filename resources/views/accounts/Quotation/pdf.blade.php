<!DOCTYPE html>
<html>

<head>
    <title>Quotation #{{ $quotation->quotation_number }}</title>
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
        .quotation-details,
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

        .quotation-details td,
        .client-details td {
            padding: 2px 0;
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

        .totals td {
            padding: 5px;
            border: 1px solid #ccc;
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

        .terms,
        .signature {
            margin-top: 15px;
            font-size: 11px;
        }

        .terms p,
        .signature p {
            margin: 3px 0;
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

        .quotation-details td:last-child {
            text-align: left;
        }
         .client-details td:last-child {
            text-align: left;
        }
        
    </style>
</head>

<body>

    <h2>Quotation</h2>

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
            <td><strong>Quotation No:</strong> {{ $quotation->quotation_number }}</td>
            <td><strong>Date:</strong> {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d M, Y') }}</td>
        </tr>
        <tr>
            <td><strong>Client Name:</strong> {{ $quotation->client->name }}</td>
            <td><strong>Email:</strong> {{ $quotation->client->email }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong> {{ $quotation->client->phone }}</td>
            <td><strong>Address:</strong> {{ $quotation->client->address }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Rate (₹)</th>
                <th>Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @foreach($quotation->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->service_name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->rate, 2) }}</td>
                <td>{{ number_format($item->amount, 2) }}</td>
            </tr>
            @php $subtotal += $item->amount; @endphp
            @endforeach
        </tbody>
    </table>

    @php
    $gst = $subtotal * 0.18;
    $grandTotal = $subtotal + $gst;
    @endphp

    <table class="totals">
        <tr>
            <td width="85%">Subtotal:</td>
            <td>₹{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>GST (18%):</td>
            <td>₹{{ number_format($gst, 2) }}</td>
        </tr>
        <tr>
            <td>Grand Total:</td>
            <td><strong>₹{{ number_format($grandTotal, 2) }}</strong></td>
        </tr>
    </table>

    <div class="terms">
        <p><strong>Terms & Conditions:</strong></p>
            @if ($quotation->notes)
        <p>{!! nl2br(e($quotation->notes)) !!}</p>
        @endif
    </div>

    <div class="signature">
        <p><strong>Acceptance of Quotation:</strong></p>
        <p>Client's Signature: __________________________</p>
        <p>Date: __________________________</p>
    </div>

    <div class="footer">
        <p>Thank you for choosing Adlertech Innovations OPC Pvt Ltd</p>
    </div>

</body>

</html>