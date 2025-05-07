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
            position: relative;
            min-height: 100%;
        }
        .header {
            margin-bottom: 30px;
        }
        .header-content {
            display: inline-block;
            width: 100%;
        }
        .logo-container {
            float: left;
            width: 50px;
            margin-right: 1px;
        }
        .logo {
            width: 60px;
            height: auto;
        }
        .company-info {
            float: left;
            text-align: left;
            padding-left: 20px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .letterhead-line {
            margin: 10px 0 5px;
            width: 100%;
            clear: both;
        }
        .letterhead-line::before,
        .letterhead-line::after {
            content: '';
            display: block;
            height: 1px;
            background-color: #ff0000a1;
            margin: 1px 0;
        }
        .document-title {
            font-size: 18px;
            margin-bottom: 15px;
            clear: both;
            margin-top: 5px;
        }
        .info-container {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-left {
            float: left;
            width: 50%;
        }
        .info-right {
            float: right;
            width: 50%;
        }
        .keluhan-section {
            clear: both;
            padding-top: 10px;
        }
        .content-wrapper {
            margin-bottom: 200px; /* Space for footer */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
        }
        th {
            background-color: #f0f0f0;
            text-align: center;
            font-size: 12px;
        }
        .signature-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background-color: white;
            width: 100%;
        }
        .signature-box {
            display: inline-block;
            width: 32%;
            text-align: center;
            margin: 0 2px;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 60%;
            margin: 20px auto 0;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    {{-- @php
        dd($requests->first()->workOrder);
    @endphp --}}

    <div class="header">
        <div class="header-content">
            <div class="logo-container">
                <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" class="logo">
            </div>
            <div class="company-info">
                <div class="company-name">CV.ARBELLA LEBAK SEJAHTERA</div>
                <div style="font-weight: bold; font-size:15px">JL.PANTI SOSIAL KM.10 RT.024 RW.009 No.41</div>
                <div style="font-weight: bold; font-size:15px">KEL.KEBUN BUNGA - KEC. SUKARAME - KOTA PALEMBANG - SUMSEL 30152</div>
                <div style="font-size:10px">Phone/WA: 082278100852&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email: Arbellalebaksejahtera@gmail.com</div>
            </div>
        </div>
        <div class="letterhead-line"></div>
    </div>

    <div class="content-wrapper">
        <div class="document-title">
            <h2 style="text-align: left; font-size: 20px; margin-top: 0; margin-bottom: 15px;">WORK ORDER MANUAL</h2>
            
            <div class="info-container">
                <div class="info-left" style="font-weight: bold">
                    <p style="font-size: 15px; margin: 5px 0;">No. Polisi&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $no_polisi ?? '-' }}</p>
                    <p style="font-size: 15px; margin: 5px 0;">Kilometer&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $kilometer ?? '-' }}</p>
                    <p style="font-size: 15px; margin: 5px 0;">Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ date('d/m/Y') }}</p>
                </div>
                <div class="info-right" style="font-weight: bold">
                    <p style="font-size: 15px; margin: 5px 0;">No.SPK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $no_spk ?? '-' }}</p>
                    <p style="font-size: 15px; margin: 5px 0;">Type Kend.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $type_kendaraan ?? '-' }}</p>

                    <p style="font-size: 15px; margin: 5px 0;">User&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $service_user ?? '-' }}</p>
                </div>
            </div>

            <div class="keluhan-section" style="font-weight: bold">
                <p style="font-size: 15px; margin: 5px 0;">KELUHAN&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $keluhan ?? '-' }}</p>
            </div>
        </div>
        

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">ITEM PEKERJAAN</th>
                    <th style="width: 10%;">QTY</th>
                    <th style="width: 35%;">KEBUTUHAN PART DAN BAHAN</th>
                    <th style="width: 25%;">KET</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $index => $request)
                <tr style="font-size: 15px">
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td style="text-align: left">{{ $request->sparepart_name }} </td>
                    <td style="text-align: center">{{ $request->quantity }} {{ $request->satuan }}</td>
                    <td style="text-align: left">{{ $request->kebutuhan_part ?? '' }}</td>
                    <td style="text-align: left">{{ $request->keterangan ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p style="font-size: 12px; margin-bottom: 5px;">PIC/USER Customer</p>
            {{-- <div class="signature-line"></div> --}}
        </div><!--
        --><div class="signature-box">
            <p style="font-size: 12px; margin-bottom: 5px;">Service Advisor</p>
            {{-- <div class="signature-line"></div> --}}
        </div><!--
        --><div class="signature-box">
            <p style="font-size: 12px; margin-bottom: 5px;">Mekanik</p>
            {{-- <div class="signature-line"></div> --}}
        </div>
    </div>
</body>
</html> 