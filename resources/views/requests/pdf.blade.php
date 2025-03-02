<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Work Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 18px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-box {
            float: left;
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 50px auto 0;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" class="logo">
        <div class="company-name">PT. Your Company Name</div>
        <div>Jl. Company Address No. 123, City</div>
        <div>Phone: (021) 123-4567 | Email: info@company.com</div>
    </div>

    <div class="document-title">
        <h2>WORK ORDER REQUEST</h2>
        <p>Date: {{ date('d/m/Y') }}</p>
        <p>Request ID: WO-{{ str_pad($requests->first()->id ?? '0', 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sparepart</th>
                <th>Jumlah</th>
                <th>Kebutuhan Part</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $index => $request)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $request->sparepart_name }}</td>
                <td style="text-align: center">{{ $request->quantity }}</td>
                <td>{{ $request->kebutuhan_part ?? '-' }}</td>
                <td>{{ $request->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <p>Dibuat oleh:</p>
            <div class="signature-line"></div>
            <p>{{ Auth::user()->name }}</p>
            <p>Service Staff</p>
        </div>
        <div class="signature-box">
            <p>Disetujui oleh:</p>
            <div class="signature-line"></div>
            <p>_______________________</p>
            <p>Supervisor</p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html> 