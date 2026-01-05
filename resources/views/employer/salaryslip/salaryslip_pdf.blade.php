<!DOCTYPE html>
<html>

<head>
    <title>Salary Slip - {{ $user->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 60px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
        }

        .company-address {
            font-size: 13px;
            color: #555;
        }

        .section-title {
            font-weight: bold;
            background: #f0f0f0;
            padding: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .no-border td {
            border: none;
            padding: 4px;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>

    <table class="no-border">
        <tr>
            <td><img src="{{ public_path('images/cropped-1-1.png') }}" alt="Company Logo" width="200px"></td>
            <td>
                <div class="company-name">Adlertech Innovations OPC Pvt Ltd</div>
                <div class="company-address">Plot No 39 Yeswant Colony, Alka Sheti Farm Kagal ,<br> Kolhapur, Maharashtra, India - 416216<br>Email: ranapratap@techadler.com | Phone: +91-9404621503</div>
            </td>
        </tr>
    </table>
    <br>

    <div class="section-title">Employee Information</div>
    <table class="no-border">
        <tr>
            <td><strong>Employee Name:</strong> {{ $user->name }}</td>
            <td><strong>Employee ID:</strong> {{ $user->empuniq_id }}</td>
        </tr>
        <tr>
            <td><strong>Designation:</strong> {{ $user->designation_id ?? '-' }}</td>
            <td><strong>Department:</strong> {{ $user->department ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Account Number:</strong> {{ $user->bank_account ?? '-' }}</td>
            <td><strong>IFSC Code:</strong> {{ $user->ifsc_code ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Month:</strong> {{ \Carbon\Carbon::parse($slip->month)->format('F Y') }}</td>
            <td><strong>Generated On:</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}</td>
        </tr>

    </table>

    <br>

    <div class="section-title">Attendance Summary</div>
    <table>
        <tr>
            <th>Total Present Days</th>
            <th>Total Leave Days</th>
            <th>Total Half Days</th>
        </tr>
        <tr>
            <td>{{ $slip->total_present_days }}</td>
            <td>{{ $slip->total_leave_days }}</td>
            <td>{{ $slip->total_half_days }}</td>
        </tr>
    </table>

    <br>

    <div class="section-title">Earnings</div>
    <table>
        <tr>
            <th>Earnings Component</th>
            <th class="right">Amount (INR)</th>
        </tr>
        <tr>
            <td>Basic Salary</td>
            <td class="right">{{ number_format($slip->basic_salary, 2) }}</td>
        </tr>
        <tr>
            <td>House Rent Allowance (HRA)</td>
            <td class="right">{{ number_format($slip->hra ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Conveyance</td>
            <td class="right">{{ number_format($slip->conveyance ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Medical Allowance</td>
            <td class="right">{{ number_format($slip->medical ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Special Allowance</td>
            <td class="right">{{ number_format($slip->special_allowance ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Gross Salary</th>
            <th class="right">{{ number_format($slip->gross_salary ?? 0, 2) }}</th>
        </tr>
    </table>

    <br>

    <div class="section-title">Deductions</div>
    <table>
        <tr>
            <th>Type</th>
            <th class="right">Amount (INR)</th>
        </tr>
        <tr>
            <td>Professional Tax</td>
            <td class="right">{{ number_format($slip->professionalTax, 2) }}</td>
        </tr>
        <tr>
            <td>Absent Deductions</td>
            <td class="right">{{ number_format($slip->absentDeduction, 2) }}</td>
        </tr>

        <tr>
            <td>Total Deductions</td>
            <td class="right">{{ number_format($slip->deductions, 2) }}</td>
        </tr>
    </table>

    <br>

    <div class="section-title">Net Payable</div>
    <table>
        <tr>
            <th>Net Salary (INR)</th>
            <td class="right"><strong>{{ number_format($slip->net_salary, 2) }}</strong></td>
        </tr>
    </table>
    <p>Payment Mode : NET Banking - NFT - IMPS - Check</p>
    <p><small>This is a system-generated salary slip and does not require a physical signature.</small></p>

</body>

</html>